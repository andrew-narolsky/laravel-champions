<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeasonSaveRequest;
use App\Models\Competition;
use App\Models\Season;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class SeasonController extends Controller
{
    public function create(Competition $competition): View
    {
        $clubs = $competition->country->clubs->pluck('name', 'id');
        return view('admin.pages.seasons.create', compact('competition', 'clubs'));
    }

    public function store(SeasonSaveRequest $request, Competition $competition): JsonResponse
    {
        DB::transaction(function () use ($request, $competition, &$season) {
            $season = $competition->seasons()->create($request->validated());
            $this->saveSeasonResult($season, $request->input('result', []));
        });

        return response()->json([
            'message' => 'Season successfully created!',
            'redirect' => route('competitions.edit', $competition)
        ]);
    }

    public function edit(Competition $competition, Season $season): View
    {
        $clubs = $competition->country->clubs->pluck('name', 'id');
        return view('admin.pages.seasons.update', compact('competition', 'season', 'clubs'));
    }

    public function update(SeasonSaveRequest $request, Competition $competition, Season $season): JsonResponse
    {
        DB::transaction(function () use ($request, $season) {
            $season->update($request->validated());
            $this->saveSeasonResult($season, $request->input('result', []));
        });

        return response()->json([
            'message' => 'Season successfully updated!',
            'redirect' => route('competitions.edit', $competition)
        ]);
    }

    public function destroy(Competition $competition, Season $season): RedirectResponse
    {
        $season->delete();
        return redirect()->route('competitions.edit', $competition);
    }

    private function saveSeasonResult(Season $season, array $data): void
    {
        $season->result()->updateOrCreate([], [
            'champion_id' => $data['champion_id'] ?? null,
            'runner_up_id' => $data['runner_up_id'] ?? null,
            'third_place_id' => $data['third_place_id'] ?? null,
            'score' => $data['score'] ?? null,
        ]);
    }
}
