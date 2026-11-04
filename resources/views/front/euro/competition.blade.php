@extends('layouts.front')

@php
    $metaTitle = "{$competition->name} — Results, Winners & History";
    $metaDescription = "Complete history of {$competition->name}: every season, all rounds and matches, winners, finals and all-time table.";
    $ogImage = $competition->attachment?->getFileUrl() ?? asset('build/images/champions.webp');
    $latest = $seasons->firstWhere('winner_club_id', '!=', null);
@endphp

@section('meta')
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}" />

    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('euro.show', $competition) }}" />
    <meta property="og:title" content="{{ $metaTitle }}" />
    <meta property="og:description" content="{{ $metaDescription }}" />
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta name="twitter:title" content="{{ $metaTitle }}" />
    <meta name="twitter:description" content="{{ $metaDescription }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />

    @include('front.partials.breadcrumb-jsonld', ['items' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => $competition->name, 'url' => route('euro.show', $competition)],
    ]])
@endsection

@section('content')

@include('front.euro.partials.hero', [
    'title' => $competition->name,
    'slogan' => $competition->description,
    'crumbs' => [['name' => $competition->name]],
])

@if ($latest)
    <section class="last-champ-section">
        <div class="container">
            <div class="last-champ-grid">
                <div class="last-champ-card">
                    <img src="{{ $latest->winner->attachment?->getFileUrl() ?? asset('/build/images/shield.svg') }}" class="lc-logo" alt="{{ $latest->winner->name }}" loading="lazy">
                    <div class="lc-info">
                        <div class="lc-season">Last Winner</div>
                        <div class="lc-name">{{ $latest->winner->nameAt($latest->startYear()) }}</div>
                        <div class="lc-detail">Season {{ $latest->name }}</div>
                    </div>
                </div>

                <div class="last-champ-card">
                    <div class="tc-icon">
                        <img src="{{ $competition->attachment?->getFileUrl() ?? asset('/build/images/tournaments/cup.svg') }}" class="lc-logo" alt="{{ $competition->name }}" loading="lazy">
                    </div>
                    <div class="lc-info">
                        <div class="lc-season">Total Seasons</div>
                        <div class="lc-name lc-number">{{ $seasons->count() }}</div>
                        <div class="lc-detail">{{ $seasons->last()->name }} ·  ·  · {{ $seasons->first()->name }}</div>
                    </div>
                </div>

                @if($topClub)
                    <div class="last-champ-card">
                        <img src="{{ $topClub['club']->attachment?->getFileUrl() ?? asset('/build/images/shield.svg') }}" class="lc-logo" alt="{{ $topClub['club']->name }}" loading="lazy">
                        <div class="lc-info">
                            <div class="lc-season">Most Titles</div>
                            <div class="lc-name">{{ $topClub['club']->name }}</div>
                            <div class="lc-detail">{{ $topClub['titles'] }} titles</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif

@include('front.euro.partials.summary')

@if($competition->content)
    <section class="seo-section">
        <div class="container">
            <h1>About {{ $competition->name }}</h1>
            {!! $competition->content !!}
        </div>
    </section>
@endif

@if ($seasons->count())
    <section class="seasons-section">
        <div class="container">
            <div class="seasons-header">
                <h2 class="section-title">Seasons</h2>
            </div>

            <div class="table-scroll">
                <table class="seasons-table">
                    <thead>
                    <tr>
                        <th>Season</th>
                        <th>Winner</th>
                        <th>Finalist</th>
                        <th>Final</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($seasons as $season)
                        @php $final = $season->ties->first(); @endphp
                        <tr>
                            <td><a href="{{ route('euro.season', [$competition, $season->name]) }}">{{ $season->name }}</a></td>
                            <td class="place-1">{{ $season->winner?->nameAt($season->startYear()) ?? '—' }}</td>
                            <td>{{ $season->runnerUp?->nameAt($season->startYear()) ?? '—' }}</td>
                            <td>
                                @if($final)
                                    {{ $final->matches->map(function ($m) use ($season) {
                                        $score = $m->score($season->winner_club_id);
                                        // the shoot-out winner is the season winner, so its kicks go first
                                        return ($pen = $m->penalties()) ? "{$score}, pen. {$pen[0]}:{$pen[1]}" : $score;
                                    })->join(', ') }}
                                    @if($final->note) <small>({{ $final->note }})</small> @endif
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endif

@if ($finals->count())
    <section class="alltime-section alltime-section--light">
        <div class="container">
            <h2 class="section-title">Winners &amp; Finalists</h2>
            <div class="table-scroll">
                <table class="alltime-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Club</th>
                        <th>Count</th>
                        <th>Years</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($finals as $row)
                        @php
                            $club = $row['club'];
                            $edge = ($loop->first ? 'row-first ' : '') . ($loop->last ? 'row-last' : '');
                        @endphp
                        <tr class="row-1st {{ $edge }}">
                            <td class="rank" rowspan="2">{{ $loop->iteration }}</td>
                            <td class="club-name" rowspan="2">@include('front.euro.partials.club', ['club' => $club])</td>
                            <td class="titles">{{ $row['champions']['count'] }}</td>
                            <td class="years">{{ $row['champions']['years'] ?: '—' }}</td>
                        </tr>
                        <tr class="row-2nd {{ $edge }}">
                            <td class="runnerup {{ $row['runnerups']['count'] == 0 ? 'no-medal' : '' }}">{{ $row['runnerups']['count'] }}</td>
                            <td class="years">{{ $row['runnerups']['years'] ?: '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endif

@if ($allTime->count())
    <section class="alltime-section">
        <div class="container">
            <h2 class="section-title">All-Time table</h2>
            <p>3 points for a win. Penalty shoot-outs count as draws.</p>
            @include('front.euro.partials.table', ['rows' => $allTime, 'showSeasons' => true])
        </div>
    </section>
@endif

@endsection
