@extends('layouts.front')

@php
    $title = "{$season->competitionTitle()} {$season->name}";
    $metaTitle = "{$title} — Results, Rounds & Final";
    $metaDescription = "{$title}: all rounds, match results"
        . ($season->winner ? ", winner {$season->winner->name}" : '') . '.';
    $ogImage = $competition->attachment?->getFileUrl() ?? asset('build/images/champions.webp');
@endphp

@section('meta')
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}" />

    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('euro.season', [$competition, $season->name]) }}" />
    <meta property="og:title" content="{{ $metaTitle }}" />
    <meta property="og:description" content="{{ $metaDescription }}" />
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta name="twitter:title" content="{{ $metaTitle }}" />
    <meta name="twitter:description" content="{{ $metaDescription }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />

    @include('front.partials.breadcrumb-jsonld', ['items' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => $competition->name, 'url' => route('euro.show', $competition)],
        ['name' => $season->name, 'url' => route('euro.season', [$competition, $season->name])],
    ]])
@endsection

@section('content')

@include('front.euro.partials.hero', [
    'title' => [$season->competitionTitle(), $season->name],
    'crumbs' => [
        ['name' => $competition->name, 'url' => route('euro.show', $competition)],
        ['name' => $season->name],
    ],
])

@if($season->winner)
    <section class="last-champ-section">
        <div class="container">
            <div class="last-champ-grid">
                <div class="last-champ-card">
                    <img src="{{ $season->winner->attachment?->getFileUrl() ?? asset('/build/images/shield.svg') }}" class="lc-logo" alt="{{ $season->winner->name }}" loading="lazy">
                    <div class="lc-info">
                        <div class="lc-season">Winner</div>
                        <div class="lc-name"><a href="{{ route('club.show', $season->winner) }}">{{ $season->winner->name }}</a></div>
                        <div class="lc-detail">Season {{ $season->name }}</div>
                    </div>
                </div>

                <div class="last-champ-card">
                    <div class="tc-icon">
                        <img src="{{ asset('build/images/icons/matches.svg') }}" class="lc-logo" alt="Matches" loading="lazy">
                    </div>
                    <div class="lc-info">
                        <div class="lc-season">Matches</div>
                        <div class="lc-name lc-number">{{ $summary['matches'] }}</div>
                        <div class="lc-detail">{{ $summary['clubs'] }} clubs</div>
                    </div>
                </div>

                <div class="last-champ-card">
                    <div class="tc-icon">
                        <img src="{{ asset('build/images/icons/goals.svg') }}" class="lc-logo" alt="Goals" loading="lazy">
                    </div>
                    <div class="lc-info">
                        <div class="lc-season">Goals</div>
                        <div class="lc-name lc-number">{{ $summary['goals'] }}</div>
                        <div class="lc-detail">{{ number_format($summary['goals_per_match'], 2) }} per match</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<section class="seasons-section">
    <div class="container">
        <div class="seasons-header">
            @if($prevSeason)
                <a href="{{ route('euro.season', [$competition, $prevSeason->name]) }}">‹ {{ $prevSeason->name }}</a>
            @endif
            @if($nextSeason)
                <a href="{{ route('euro.season', [$competition, $nextSeason->name]) }}">{{ $nextSeason->name }} ›</a>
            @endif
        </div>

        @foreach($stages as $stage)
        <div class="euro-stage">
            <h2 class="section-title">{{ $stage->label() }}</h2>

            @php
                // Notes of this stage become footnotes: *, **, *** ...
                $footnotes = [];
                $mark = function (?string $note) use (&$footnotes): string {
                    if (!$note) {
                        return '';
                    }
                    $index = array_search($note, $footnotes, true);
                    if ($index === false) {
                        $footnotes[] = $note;
                        $index = count($footnotes) - 1;
                    }
                    return str_repeat('*', $index + 1);
                };
            @endphp

            @if($stage->isGroup())
                @foreach($groupTables[$stage->value] ?? [] as $group => $rows)
                    @if($group !== '')<h3 class="table-title">Group {{ $group }}</h3>@endif
                    @include('front.euro.partials.table', ['rows' => $rows, 'logo' => false])

                    <div class="table-scroll euro-group-matches">
                        <table class="seasons-table euro-ties-table">
                            <colgroup>
                                <col class="col-date">
                                <col class="col-club">
                                <col class="col-club">
                                <col class="col-score">
                            </colgroup>
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Club</th>
                                <th>Club</th>
                                <th>Matches</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($groupMatches[$stage->value][$group] ?? [] as $match)
                                <tr>
                                    <td>{{ $match->played_at?->format('d.m.Y') ?? '—' }}</td>
                                    <td @class(['place-1' => $match->home_goals > $match->away_goals])>@include('front.euro.partials.club', ['club' => $match->homeClub, 'logo' => false])</td>
                                    <td @class(['place-1' => $match->away_goals > $match->home_goals])>@include('front.euro.partials.club', ['club' => $match->awayClub, 'logo' => false])</td>
                                    <td>{{ $match->score() }}{{ $mark($match->note) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @else
                <div class="table-scroll">
                    <table class="seasons-table euro-ties-table">
                        <colgroup>
                            <col class="col-date">
                            <col class="col-club">
                            <col class="col-club">
                            <col class="col-score">
                            <col class="col-agg">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Club</th>
                            <th>Club</th>
                            <th>Matches</th>
                            <th>Agg.</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ties[$stage->value] ?? [] as $tie)
                            <tr>
                                <td>
                                    @foreach($tie->matches as $match)
                                        <div>{{ $match->played_at?->format('d.m.Y') ?? '—' }}</div>
                                    @endforeach
                                </td>
                                <td @class(['place-1' => $tie->winner_club_id === $tie->home_club_id])>
                                    @include('front.euro.partials.club', ['club' => $tie->homeClub, 'logo' => false])
                                </td>
                                <td @class(['place-1' => $tie->away_club_id && $tie->winner_club_id === $tie->away_club_id])>
                                    @include('front.euro.partials.club', ['club' => $tie->awayClub, 'logo' => false])
                                </td>
                                <td>
                                    @foreach($tie->matches as $match)
                                        <div>
                                            {{ $match->score($tie->home_club_id) }}
                                            {{ $mark($match->note) }}
                                        </div>
                                    @endforeach
                                </td>
                                <td><strong>{{ match (true) {
                                    !$tie->away_club_id => 'bye',
                                    $tie->matches->isEmpty() => 'w/o',
                                    default => $tie->aggregate(),
                                } }}</strong>{{ $mark($tie->note) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if($footnotes)
                <div class="stage-notes">
                    @foreach($footnotes as $i => $note)
                        <div><small>{{ str_repeat('*', $i + 1) }} {{ $note }}</small></div>
                    @endforeach
                </div>
            @endif
        </div>
        @endforeach
    </div>
</section>

@endsection
