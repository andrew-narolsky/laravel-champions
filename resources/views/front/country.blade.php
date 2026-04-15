@extends('layouts.front')

@section('meta')
    <title>{{ $country->name }} Football — Clubs, Competitions & Champions</title>
    <meta name="description" content="Explore football in {{ $country->name }}: clubs, leagues, cups and super cups. View champions, season results, statistics and complete competition history." />
@endsection

@section('content')

    <!-- COUNTRY HERO -->
    <section class="hero-section hero-section--inner">
        <div class="hero-bg" style="background-image: url({{ asset('build/images/default-banner.webp') }});"></div>
        <div class="hero-overlay"></div>

        <nav class="country-breadcrumb">
            <div class="container">
                <a href="/">Home</a>
                <span class="breadcrumb-sep">›</span>
                <span>{{ $country->name }}</span>
            </div>
        </nav>

        <div class="hero-content">
            <div class="container">
                <p class="hero-title"><span>{{ $country->name }}</span></p>
                <p class="hero-slogan">{{ $country->description }}</p>
            </div>
        </div>
    </section>

    @if($country->competitions->count())
        <!-- TOURNAMENTS -->
        <section class="tournaments-section">
            <div class="container">
                <div class="slider-header">
                    <h2 class="section-title">Tournaments</h2>
                    <div class="slider-nav">
                        <button class="slider-btn" id="tc-prev" aria-label="Previous"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
                        <button class="slider-btn" id="tc-next" aria-label="Next"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
                    </div>
                </div>
                <div class="swiper tc-swiper">
                    <div class="swiper-wrapper">

                        @foreach($country->competitions as $competition)
                            <div class="swiper-slide">
                                <a href="{{ route('competition.show', $competition) }}" class="tournament-card">
                                    <div class="tc-icon">
                                        @if($competition->attachment)
                                            <img src="{{ $competition->attachment?->getFileUrl() }}" width="56" height="56" alt="{{ $competition->name }}" loading="lazy">
                                        @else
                                            <img src="{{ asset('build/images/tournaments/' . $competition->type->title() . '.svg') }}" width="56" height="56" alt="{{ $competition->name }}" loading="lazy">
                                        @endif
                                    </div>
                                    <div class="tc-body">
                                        <span class="tc-type">{{ $competition->type->label() }}</span>
                                        <span class="tc-name">{{ $competition->name }}</span>
                                        <span class="tc-meta">{{ $competition->description }}</span>
                                    </div>
                                </a>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- ═══════════════════════════════════════
         SEO TEXT
    ═══════════════════════════════════════ -->
    <section class="seo-section">
        <div class="container">
            <h1>About {{ $country->name }}</h1>
            {!! $country->content !!}
        </div>
    </section>

    @if($topChampionClubs->count())
        <!-- CLUBS & RECENT CHAMPIONS -->
        <section class="country-stats-section titled-section--dark">
            <div class="container">
                <div class="country-stats-grid">

                    <div class="country-stats-col">
                        <h2 class="section-title">Most Titled Clubs</h2>
                        <table class="titled-table">
                            <thead>
                            <tr><th>#</th><th>Club</th><th>Titles</th></tr>
                            </thead>
                            <tbody>
                                @foreach($topChampionClubs as $key => $club)
                                    <tr>
                                        <td class="rank">
                                            {{ ++$key }}
                                        </td>
                                        <td class="club-name">
                                            <a href="{{ route('club.show', $club) }}" class="cl-az-item">
                                                <img class="cl-flag" src="{{ $club->attachment?->getFileUrl() }}" alt="{{ $club->name }}" loading="lazy">
                                                {{ $club->name }}
                                            </a>
                                        </td>
                                        <td class="titles">
                                            {{ $club->titles }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="country-stats-col">
                        <h2 class="section-title">Recent Champions</h2>
                        <table class="champions-table">
                            <thead>
                            <tr><th>Season</th><th>Champion</th></tr>
                            </thead>
                            <tbody>
                            @foreach($latestChampions as $season)
                                @php
                                    $champion = $season->result?->champions?->first();
                                @endphp

                                <tr>
                                    <td>{{ $season->name }}</td>
                                    <td class="club-name">
                                        <a href="{{ route('club.show', $champion) }}" class="cl-az-item">
                                            <img class="cl-flag" src="{{ $champion->attachment?->getFileUrl() }}" alt="{{ $champion->name }}" loading="lazy">
                                            {{ $champion?->name ?? '—' }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                </div>
            </div>
        </section>
    @endif

    @if($country->clubs->count())
        <!-- CLUBS A–Z -->
        <section class="cl-az-section">
            <div class="container">
                <h2 class="section-title">Clubs</h2>
                @php
                    $groupedClubs = $country->clubs
                        ->sortBy(fn($club) => $club->normalized_name)
                        ->groupBy(fn($club) => strtoupper(mb_substr($club->normalized_name, 0, 1)));

                    $alphabet = range('A', 'Z');
                @endphp

                <nav class="cl-az-nav" aria-label="Jump to letter">
                    @foreach($alphabet as $letter)
                        @if($groupedClubs->has($letter))
                            <a href="#cl-{{ $letter }}" class="cl-az-letter">{{ $letter }}</a>
                        @else
                            <span class="cl-az-letter cl-empty">{{ $letter }}</span>
                        @endif
                    @endforeach
                </nav>

                <div class="cl-az-groups">
                    @foreach($groupedClubs as $letter => $countriesGroup)
                        <div class="cl-az-group" id="cl-{{ $letter }}">
                            <div class="cl-az-group-letter">{{ $letter }}</div>

                            @foreach($countriesGroup as $club)
                                <a href="{{ route('club.show', $club) }}" class="cl-az-item">
                                    @if($club->attachment?->getFileUrl())
                                        <img class="cl-flag" src="{{ $club->attachment?->getFileUrl() }}" alt="{{ $club->name }}" loading="lazy">
                                    @else
                                        <span class="cl-flag">⚽</span>
                                    @endif
                                    {{ $club->name }}
                                </a>
                            @endforeach

                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
