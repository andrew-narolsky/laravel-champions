<?php

namespace App\Console\Commands;

use App\Enums\EuroStage;
use App\Models\Attachment;
use App\Models\Club;
use App\Models\ClubName;
use App\Models\EuroCompetition;
use App\Models\EuroSeason;
use App\Models\EuroTie;
use App\Services\StatsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Imports one season of a European competition from JSON. Format: see data/euro/README.md
 */
class ImportEuroSeason extends Command
{
    protected $signature = 'euro:import
        {file : Path to the season JSON file}
        {--force : Replace the season if it already exists}';

    protected $description = 'Import a European competition season (ties and matches) from JSON';

    /** @var array<string, int|null> */
    private array $clubCache = [];

    /** @var array<string, true> */
    private array $unresolved = [];

    public function handle(): int
    {
        $path = $this->argument('file');

        if (!is_file($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        // Resolve all clubs first, so nothing is written if any is unknown.
        $this->collectClubs($data);

        if ($this->unresolved) {
            $this->error('Unknown clubs (use slug, exact name or historical name):');
            foreach (array_keys($this->unresolved) as $name) {
                $this->line("  - {$name}");
            }
            return self::FAILURE;
        }

        try {
            $season = DB::transaction(fn () => $this->import($data));
            app(StatsService::class)->clear();
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->info(sprintf(
            'Imported %s %s: %d ties, %d matches. Winner: %s',
            $season->competition->name,
            $season->name,
            $season->ties()->count(),
            $season->matches()->count(),
            $season->winner?->name ?? '—',
        ));

        return self::SUCCESS;
    }

    private function import(array $data): EuroSeason
    {
        $competition = EuroCompetition::firstOrCreate(
            ['slug' => $data['competition']['slug'] ?? Str::slug($data['competition']['name'])],
            [
                'name' => $data['competition']['name'],
                'description' => $data['competition']['description'] ?? null,
            ]
        );

        $this->attachLogo($competition);

        $season = $competition->seasons()->where('name', $data['season'])->first();

        if ($season && !$this->option('force')) {
            throw new RuntimeException("Season {$data['season']} already exists. Use --force to replace it.");
        }

        if ($season) {
            $season->ties()->delete();
            $season->matches()->delete();
        }

        $season ??= new EuroSeason(['euro_competition_id' => $competition->id, 'name' => $data['season']]);
        $season->fill([
            'competition_name' => $data['competition_name'] ?? null,
            'points_for_win' => $data['points_for_win'] ?? 3,
            'point_adjustments' => array_map(fn (array $a) => [
                'club_id' => $this->club($a['club']),
                'stage' => $a['stage'] ?? EuroStage::GROUP->value,
                'points' => (int) $a['points'],
                'note' => $a['note'] ?? null,
            ], $data['point_adjustments'] ?? []) ?: null,
            'winner_club_id' => null,
            'runner_up_club_id' => null,
        ])->save();

        $order = 0;

        foreach ($data['stages'] as $stageData) {
            $stage = EuroStage::tryFrom($stageData['stage'])
                ?? throw new RuntimeException("Unknown stage: {$stageData['stage']}");

            foreach ($stageData['ties'] ?? [] as $tieData) {
                $this->importTie($season, $stage, $tieData, ++$order);
            }

            foreach ($stageData['matches'] ?? [] as $matchData) {
                $season->matches()->create($this->matchAttributes($stage, $matchData) + [
                    'group' => $matchData['group'] ?? null,
                ]);
            }
        }

        $season->syncWinnerFromFinal();

        if (isset($data['winner'])) {
            $season->update(['winner_club_id' => $this->club($data['winner'])]);
        }

        return $season->load('competition', 'winner');
    }

    /**
     * Logo from database/data/images/euro/<slug>.webp (same convention as CompetitionSeeder).
     */
    private function attachLogo(EuroCompetition $competition): void
    {
        $src = database_path("data/images/euro/{$competition->slug}.webp");

        if (!file_exists($src)) {
            return;
        }

        $path = 'uploads/' . EuroCompetition::MODULE_NAME;
        $filename = "{$competition->slug}.webp";

        Storage::disk('public')->put("{$path}/{$filename}", file_get_contents($src));

        Attachment::updateOrCreate(
            ['module' => EuroCompetition::MODULE_NAME, 'module_id' => $competition->id],
            ['filename' => $filename, 'path' => $path, 'ext' => 'webp', 'type' => 'image', 'size' => filesize($src)],
        );
    }

    private function importTie(EuroSeason $season, EuroStage $stage, array $tieData, int $order): void
    {
        [$first, $second] = array_pad(array_map($this->club(...), $tieData['clubs']), 2, null);

        $tie = $season->ties()->create([
            'stage' => $stage,
            'home_club_id' => $first,
            'away_club_id' => $second,
            'note' => $tieData['note'] ?? null,
            'order' => $order,
        ]);

        foreach ($tieData['matches'] ?? [] as $leg => $matchData) {
            $match = $tie->matches()->make($this->matchAttributes($stage, $matchData) + ['leg' => $leg + 1]);
            $match->euro_season_id = $season->id;

            if (!in_array($match->home_club_id, [$first, $second], true) || !in_array($match->away_club_id, [$first, $second], true)) {
                throw new RuntimeException("Match clubs do not belong to tie {$tieData['clubs'][0]} — " . ($tieData['clubs'][1] ?? '?'));
            }

            $match->save();
        }

        $tie->update(['winner_club_id' => $this->resolveWinner($tie->load('matches'), $tieData)]);
    }

    /**
     * Explicit "winner" wins (null = nobody advanced, e.g. both clubs withdrew);
     * otherwise the club with the better aggregate.
     */
    private function resolveWinner(EuroTie $tie, array $tieData): ?int
    {
        if (array_key_exists('winner', $tieData)) {
            return $tieData['winner'] === null ? null : $this->club($tieData['winner']);
        }

        if (!$tie->away_club_id) {
            return $tie->home_club_id;
        }

        $home = $tie->goalsFor($tie->home_club_id);
        $away = $tie->goalsFor($tie->away_club_id);

        if ($home === $away && ($playoff = $tie->playoff()) && $playoff->home_goals !== $playoff->away_goals) {
            return $playoff->home_goals > $playoff->away_goals ? $playoff->home_club_id : $playoff->away_club_id;
        }

        if ($home === $away) {
            throw new RuntimeException(sprintf(
                'Tie %s — %s is level on aggregate (%d:%d): add a play-off match or set "winner" explicitly.',
                $tieData['clubs'][0], $tieData['clubs'][1], $home, $away
            ));
        }

        return $home > $away ? $tie->home_club_id : $tie->away_club_id;
    }

    private function matchAttributes(EuroStage $stage, array $m): array
    {
        if (!preg_match('/^\s*(\d+)\s*[:\-–]\s*(\d+)\s*$/u', (string) $m['score'], $score)) {
            throw new RuntimeException("Bad score \"{$m['score']}\" in {$m['home']} — {$m['away']}");
        }

        return [
            'stage' => $stage,
            'played_at' => $m['date'] ?? null,
            'home_club_id' => $this->club($m['home']),
            'away_club_id' => $this->club($m['away']),
            'home_goals' => (int) $score[1],
            'away_goals' => (int) $score[2],
            'extra_time' => (bool) ($m['extra_time'] ?? false),
            'neutral' => (bool) ($m['neutral'] ?? false),
            'note' => $m['note'] ?? null,
        ];
    }

    private function club(string $ref): int
    {
        return $this->findClub($ref) ?? throw new RuntimeException("Unknown club: {$ref}");
    }

    private function findClub(string $ref): ?int
    {
        if (array_key_exists($ref, $this->clubCache)) {
            return $this->clubCache[$ref];
        }

        $id = Club::where('slug', $ref)->value('id');

        if (!$id) {
            $ids = Club::where('name', $ref)->pluck('id');
            $id = $ids->count() === 1 ? $ids->first() : null;
        }

        if (!$id) {
            $ids = ClubName::where('name', $ref)->distinct()->pluck('club_id');
            $id = $ids->count() === 1 ? $ids->first() : null;
        }

        return $this->clubCache[$ref] = $id;
    }

    private function collectClubs(array $data): void
    {
        $refs = [];

        if (isset($data['winner'])) {
            $refs[] = $data['winner'];
        }

        foreach ($data['point_adjustments'] ?? [] as $a) {
            $refs[] = $a['club'];
        }

        foreach ($data['stages'] ?? [] as $stage) {
            foreach ($stage['ties'] ?? [] as $tie) {
                array_push($refs, ...$tie['clubs']);
                if (isset($tie['winner'])) {
                    $refs[] = $tie['winner'];
                }
                foreach ($tie['matches'] ?? [] as $m) {
                    array_push($refs, $m['home'], $m['away']);
                }
            }
            foreach ($stage['matches'] ?? [] as $m) {
                array_push($refs, $m['home'], $m['away']);
            }
        }

        foreach (array_unique($refs) as $ref) {
            if (!$this->findClub($ref)) {
                $this->unresolved[$ref] = true;
            }
        }
    }
}
