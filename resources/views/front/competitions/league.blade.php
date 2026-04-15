<!-- HERO -->
<section class="hero-section hero-section--inner hero-section--inner-competition">
    <div class="hero-bg" style="background-image: url({{ asset('build/images/default-banner.webp') }});"></div>
    <div class="hero-overlay"></div>

    <nav class="country-breadcrumb">
        <div class="container">
            <a href="/">Home</a>
            <span class="breadcrumb-sep">›</span>
            <a href="{{ route('country.show', $competition->country) }}">{{ $competition->country->name }}</a>
            <span class="breadcrumb-sep">›</span>
            <span>{{ $competition->name }}</span>
        </div>
    </nav>

    <div class="hero-content">
        <div class="container">
            <p class="hero-title"><span>{{ $competition->name }}</span></p>
            <p class="hero-slogan">{{ $competition->description }}</p>
        </div>
    </div>
</section>

@if ($latestChampion)
    <!-- LAST CHAMPION -->
    <section class="last-champ-section">
        <div class="container">
            <div class="last-champ-grid">

                <div class="last-champ-card">
                    @php
                        $champion = $latestChampion->result?->champions?->first();
                    @endphp
                    @isset($champion->attachment)
                        <img src="{{ $champion->attachment?->getFileUrl() }}" class="lc-logo" alt="{{ $champion->name }}" loading="lazy">
                    @else
                        <img src="{{ asset('/build/images/shield.svg') }}" class="lc-logo" alt="{{ $champion->name }}" loading="lazy">
                    @endisset
                    <div class="lc-info">
                        <div class="lc-season">Last Champion</div>
                        <div class="lc-name">{{ $champion->name }}</div>
                        <div class="lc-detail">Season {{ $latestChampion->name }}</div>
                    </div>
                </div>

                <div class="last-champ-card">
                    @isset($competition->attachment)
                        <img src="{{ $competition->attachment->getFileUrl() }}" class="lc-logo" alt="{{ $competition->name }}" loading="lazy">
                    @else
                        <img src="{{ asset('/build/images/tournaments/championship.svg') }}" class="lc-logo" alt="{{ $competition->name }}" loading="lazy">
                    @endisset
                    <div class="lc-info">
                        <div class="lc-season">Total Seasons</div>
                        <div class="lc-name lc-number">{{ $seasons->count() }}</div>
                        <div class="lc-detail">{{ $seasons->last()->name }} ·  ·  · {{ $seasons->first()->name }}</div>
                    </div>
                </div>

                <div class="last-champ-card">
                    @isset($topCupClub->attachment)
                        <img src="{{ $topCupClub->attachment->getFileUrl() }}" class="lc-logo" alt="{{ $topCupClub->name }}" loading="lazy">
                    @else
                        <img src="{{ asset('/build/images/shield.svg') }}" class="lc-logo" alt="{{ $topCupClub->name }}" loading="lazy">
                    @endisset
                    <div class="lc-info">
                        <div class="lc-season">Most Titles</div>
                        <div class="lc-name">{{ $topCupClub->name }}</div>
                        <div class="lc-detail">{{ $topCupClub->titles }} titles</div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endif

<!-- SEO -->
<section class="seo-section">
    <div class="container">
        <h1>About {{ $competition->name }}</h1>
        {!! $competition->content !!}
    </div>
</section>

@if ($seasons->count())
    <!-- SEASONS -->
    <section class="seasons-section">
        <div class="container">

            <div class="seasons-header">
                <h2 class="section-title">Seasons</h2>
                @php
                    $decades = collect($seasons)
                        ->map(function ($season) {
                            preg_match('/\d{4}/', $season->name, $matches);
                            return isset($matches[0]) ? substr($matches[0], 0, 3) . '0' : null;
                        })
                        ->filter()
                        ->unique()
                        ->sortDesc()
                        ->take(4)
                        ->values();
                @endphp

                <div class="decade-filter" id="decade-filter">
                    <button class="decade-btn active" data-decade="all">All</button>

                    @foreach($decades as $decade)
                        <button class="decade-btn" data-decade="{{ $decade }}">
                            {{ $decade }}s
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="table-scroll">
                <table class="seasons-table" id="seasons-table">
                    <thead>
                    <tr>
                        <th>Season</th>
                        <th>Champion</th>
                        <th>Runner-up</th>
                        <th>3rd place</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($seasons as $season)
                        @php
                            $champion = $season->result?->champions?->first();
                            $runnerUp = $season->result?->runnerUps?->first();
                            $third = $season->result?->thirdPlaces?->first();

                            // decade (2024 → 2020)
                            preg_match('/\d{4}/', $season->name, $matches);
                            $year = $matches[0] ?? null;
                            $decade = $year ? floor($year / 10) * 10 : null;
                        @endphp

                        <tr data-decade="{{ $decade }}">
                            <td>{{ $season->name }}</td>

                            {{-- Champion --}}
                            <td class="place-1">
                                @if($champion)
                                    <div class="flex items-center gap-2">
                                        {{ $champion->name }}
                                    </div>
                                @else
                                    —
                                @endif
                            </td>

                            {{-- Runner-up --}}
                            <td>
                                @if($runnerUp)
                                    <div class="flex items-center gap-2">
                                        {{ $runnerUp->name }}
                                    </div>
                                @else
                                    —
                                @endif
                            </td>

                            {{-- Third place --}}
                            <td>
                                @if($third)
                                    <div class="flex items-center gap-2">
                                        {{ $third->name }}
                                    </div>
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

@if ($allTime->count())
    <!-- ALL-TIME RECORDS TABLE -->
    <section class="alltime-section alltime-section--light">
        <div class="container">
            <h2 class="section-title">All-Time Premier League Record</h2>
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
                    @foreach($allTime as $index => $row)
                        @php
                            $club = $row['club'];
                            $isFirst = $loop->first;
                            $isLast = $loop->last;
                        @endphp

                        {{-- Champions --}}
                        <tr class="row-1st {{ $isFirst ? 'row-first' : '' }} {{ $isLast ? 'row-last' : '' }}">
                            <td class="rank" rowspan="3">{{ $index + 1 }}</td>

                            <td class="club-name" rowspan="3">
                                <a href="{{ route('club.show', $club) }}" class="club-inner">
                                    @if($club->attachment)
                                        @isset($club->attachment)
                                            <img src="{{ $club->attachment?->getFileUrl() }}" class="lc-logo" alt="{{ $club->name }}" loading="lazy">
                                        @else
                                            <img src="{{ asset('/build/images/shield.svg') }}" class="lc-logo" alt="{{ $club->name }}" loading="lazy">
                                        @endisset
                                    @endif
                                    {{ $club->name }}
                                </a>
                            </td>

                            <td class="titles">
                                {{ $row['champions']['count'] }}
                            </td>

                            <td class="years">
                                {{ $row['champions']['years'] ?: '—' }}
                            </td>
                        </tr>

                        {{-- Runner-up --}}
                        <tr class="row-2nd {{ $isFirst ? 'row-first' : '' }} {{ $isLast ? 'row-last' : '' }}">
                            <td class="runnerup {{ $row['runnerups']['count'] == 0 ? 'no-medal' : '' }}">
                                {{ $row['runnerups']['count'] }}
                            </td>

                            <td class="years">
                                {{ $row['runnerups']['years'] ?: '—' }}
                            </td>
                        </tr>

                        {{-- Third --}}
                        <tr class="row-3rd {{ $isFirst ? 'row-first' : '' }} {{ $isLast ? 'row-last' : '' }}">
                            <td class="third {{ $row['third']['count'] == 0 ? 'no-medal' : '' }}">
                                {{ $row['third']['count'] }}
                            </td>

                            <td class="years">
                                {{ $row['third']['years'] ?: '—' }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endif
