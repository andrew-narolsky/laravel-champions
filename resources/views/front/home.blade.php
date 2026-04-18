@extends('layouts.front')

@section('meta')
    <title>Football Champions — European Football Tournaments</title>
    <meta name="description" content="Information about European football tournaments: leagues, cups, and super cups. Season winners, club and tournament statistics." />
@endsection

@section('content')

    <!-- ═══════════════════════════════════════
     HERO BANNER
    ═══════════════════════════════════════ -->
    <section class="hero-section">
        <div class="hero-bg" style="background-image: url({{ asset('build/images/champions.webp') }});"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="container">
                <p class="hero-title">Born to be<br><span>champions</span></p>
                <p class="hero-slogan">Leagues, cups and super cups — all of Europe in one place</p>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════
         STATS
    ═══════════════════════════════════════ -->
    <div class="stats-section">
        <div class="container">
            <div class="stats-v3-inner" id="stats-v3">

                <div class="v3-item">
                    <div class="v3-text">
                        <span class="v3-label">Countries</span>
                        <span class="v3-value" data-target="{{ $countriesCount }}" data-suffix="">0</span>
                    </div>
                    <div class="v3-icon">
                        <img src="{{ asset('build/images/icons/countries.svg') }}" width="64" height="64" alt="Countries" loading="lazy">
                    </div>
                </div>

                <div class="v3-item">
                    <div class="v3-text">
                        <span class="v3-label">Tournaments</span>
                        <span class="v3-value" data-target="{{ $competitionsCount }}" data-suffix="">0</span>
                    </div>
                    <div class="v3-icon">
                        <img src="{{ asset('build/images/icons/tournaments.svg') }}" width="64" height="64" alt="Tournaments" loading="lazy">
                    </div>
                </div>

                <div class="v3-item">
                    <div class="v3-text">
                        <span class="v3-label">Clubs</span>
                        <span class="v3-value" data-target="{{ $clubsCount }}" data-suffix="+">+</span>
                    </div>
                    <div class="v3-icon">
                        <img src="{{ asset('build/images/icons/clubs.svg') }}" width="64" height="64" alt="Clubs" loading="lazy">
                    </div>
                </div>

                <div class="v3-item">
                    <div class="v3-text">
                        <span class="v3-label">Seasons</span>
                        <span class="v3-value" data-target="{{ $seasonsCount }}" data-suffix="+">+</span>
                    </div>
                    <div class="v3-icon">
                        <img src="{{ asset('build/images/icons/seasons.svg') }}" width="64" height="64" alt="Seasons" loading="lazy">
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
         SEO TEXT
    ═══════════════════════════════════════ -->
    <section class="seo-section">
        <div class="container">
            <h1 class="seo-title">European Football Tournaments</h1>
            <p class="seo-text">
                A reference site covering national football competitions across Europe — from the English Premier League and Spanish La Liga
                to the Portuguese Primeira Liga, Turkish Süper Lig and beyond. Here you'll find detailed information
                about leagues, cups and super cups: season winners and runners-up, cup final scorelines,
                a full chronology of titles and all-time tournament records.
            </p>
            <p class="seo-text">
                Every club has its own page with a crest, a short profile and a complete trophy cabinet — grouped by competition
                with the year of each title. A country page features an interactive map of cities and clubs, a list of all tournaments
                and a table of the most decorated teams. On a tournament page you'll find season-by-season navigation, a standings table
                for leagues and final match-ups for cup competitions.
            </p>
            <p class="seo-text">
                Explore European football through the interactive map on the home page — pick a country, read a quick summary
                and jump straight to its tournaments. Or search directly: type a club, tournament or country name and get
                instant results. We collect and structure data on more than 500 clubs and 1,200 seasons — so the entire history
                of European football is accessible, organised and in one place.
            </p>
        </div>
    </section>

    <!-- ═══════════════════════════════════════
         WINNERS SLIDER
    ═══════════════════════════════════════ -->
    <section class="slider-section">
        <div class="container">
            <div class="slider-header">
                <h2 class="section-title">Top-5 Champions 2024/25</h2>
                <div class="slider-nav">
                    <button class="slider-btn" id="prev-v2" aria-label="Previous"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
                    <button class="slider-btn" id="next-v2" aria-label="Next"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
                </div>
            </div>

            <div class="swiper winners-swiper">
                <div class="swiper-wrapper">

                    <a class="swiper-slide sc-v2" href="countries/england/tournaments/premier-league/">
                        <div class="sc-v2-bg" style="background-color:#C8102E; background-image:url({{ asset('build/images/liverpool.webp') }});"></div>
                        <div class="sc-v2-overlay">
                            <div class="sc-v2-content">
                                <span class="sc-v2-flag">🏴󠁧󠁢󠁥󠁮󠁧󠁿</span>
                                <div class="sc-v2-tournament">Premier League</div>
                                <div class="sc-v2-club">Liverpool</div>
                                <div class="sc-v2-season">2024/25</div>
                            </div>
                        </div>
                    </a>

                    <a class="swiper-slide sc-v2" href="countries/spain/tournaments/la-liga/">
                        <div class="sc-v2-bg" style="background-color:#A50044; background-image:url({{ asset('build/images/barcelona.webp') }});"></div>
                        <div class="sc-v2-overlay">
                            <div class="sc-v2-content">
                                <span class="sc-v2-flag">🇪🇸</span>
                                <div class="sc-v2-tournament">La Liga</div>
                                <div class="sc-v2-club">Barcelona</div>
                                <div class="sc-v2-season">2024/25</div>
                            </div>
                        </div>
                    </a>

                    <a class="swiper-slide sc-v2" href="countries/germany/tournaments/bundesliga/">
                        <div class="sc-v2-bg" style="background-color:#DC052D; background-image:url({{ asset('build/images/bayern.webp') }});"></div>
                        <div class="sc-v2-overlay">
                            <div class="sc-v2-content">
                                <span class="sc-v2-flag">🇩🇪</span>
                                <div class="sc-v2-tournament">Bundesliga</div>
                                <div class="sc-v2-club">Bayern Munich</div>
                                <div class="sc-v2-season">2024/25</div>
                            </div>
                        </div>
                    </a>

                    <a class="swiper-slide sc-v2" href="countries/france/tournaments/ligue-1/">
                        <div class="sc-v2-bg" style="background-color:#004170; background-image:url({{ asset('build/images/psg.webp') }});"></div>
                        <div class="sc-v2-overlay">
                            <div class="sc-v2-content">
                                <span class="sc-v2-flag">🇫🇷</span>
                                <div class="sc-v2-tournament">Ligue 1</div>
                                <div class="sc-v2-club">PSG</div>
                                <div class="sc-v2-season">2024/25</div>
                            </div>
                        </div>
                    </a>

                    <a class="swiper-slide sc-v2" href="countries/italy/tournaments/serie-a/">
                        <div class="sc-v2-bg" style="background-color:#12A0C4; background-image:url({{ asset('build/images/napoli.webp') }});"></div>
                        <div class="sc-v2-overlay">
                            <div class="sc-v2-content">
                                <span class="sc-v2-flag">🇮🇹</span>
                                <div class="sc-v2-tournament">Serie A</div>
                                <div class="sc-v2-club">Napoli</div>
                                <div class="sc-v2-season">2024/25</div>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════
         MOST TITLED CLUBS
    ═══════════════════════════════════════ -->
    <section class="titled-section">
        <div class="container">
            <h2 class="section-title">Most Titled Clubs in Europe</h2>

            @php
                $limit = $topChampionClubs->count();
                $perColumn = ceil($limit / 2);
                $topChampionChunks = $topChampionClubs->values()->chunk($perColumn);
                $globalIndex = 1;
            @endphp

            <div class="titled-grid">
                @foreach($topChampionChunks as $clubs)
                    <table class="titled-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Club</th>
                            <th>Country</th>
                            <th>Titles</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($clubs as $club)
                            <tr>
                                <td class="rank">
                                    {{ $globalIndex++ }}
                                </td>
                                <td class="club-name">
                                    <a href="{{ route('club.show', $club) }}" class="cl-az-item">
                                        <img class="cl-flag" src="{{ $club->attachment?->getFileUrl() }}" alt="{{ $club->name }}" loading="lazy">
                                        {{ $club->name }}
                                    </a>
                                </td>
                                <td>
                                    {{ $club->country->name ?? '' }}
                                </td>
                                <td class="titles">
                                    {{ $club->titles }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ═══════════════════════════════════════
         MOST TITLED CUP CLUBS
    ═══════════════════════════════════════ -->
    <section class="titled-section titled-section--dark">
        <div class="container">
            <h2 class="section-title">Most Titled Cup Clubs in Europe</h2>

            @php
                $limit = $topChampionClubs->count();
                $perColumn = ceil($limit / 2);
                $topCupChunks = $topCupClubs->values()->chunk($perColumn);
                $globalIndex = 1;
            @endphp

            <div class="titled-grid">
                @foreach($topCupChunks as $clubs)
                    <table class="titled-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Club</th>
                            <th>Country</th>
                            <th>Titles</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($clubs as $club)
                            <tr>
                                <td class="rank">
                                    {{ $globalIndex++ }}
                                </td>
                                <td class="club-name">
                                    <a href="{{ route('club.show', $club) }}" class="cl-az-item">
                                        <img class="cl-flag" src="{{ $club->attachment?->getFileUrl() }}" alt="{{ $club->name }}" loading="lazy">
                                        {{ $club->name }}
                                    </a>
                                </td>
                                <td>
                                    {{ $club->country->name ?? '' }}
                                </td>
                                <td class="titles">
                                    {{ $club->titles }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ═══════════════════════════════════════
         COUNTRIES V5 — A–Z
    ═══════════════════════════════════════ -->
    <section class="cl-az-section">
        <div class="container">
            <h2 class="section-title">UEFA Member Associations</h2>
            @php
                $groupedCountries = $countries
                    ->sortBy('name')
                    ->groupBy(fn($country) => strtoupper(mb_substr($country->name, 0, 1)));

                $alphabet = range('A', 'Z');
            @endphp

            <nav class="cl-az-nav" aria-label="Jump to letter">
                @foreach($alphabet as $letter)
                    @if($groupedCountries->has($letter))
                        <a href="#cl-{{ $letter }}" class="cl-az-letter">{{ $letter }}</a>
                    @else
                        <span class="cl-az-letter cl-empty">{{ $letter }}</span>
                    @endif
                @endforeach
            </nav>

            <div class="cl-az-groups">
                @foreach($groupedCountries as $letter => $countriesGroup)
                    <div class="cl-az-group" id="cl-{{ $letter }}">
                        <div class="cl-az-group-letter">{{ $letter }}</div>

                        @foreach($countriesGroup as $country)
                            <a href="{{ route('country.show', $country) }}" class="cl-az-item">
                                <span class="cl-flag">
                                    {{ $country->flag_code }}
                                </span>
                                {{ $country->name }}
                            </a>
                        @endforeach

                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection
