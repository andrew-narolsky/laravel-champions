@extends('layouts.front')

@php
    [$year, $detail] = $club->founded_date_parts ?? [null, null];
@endphp

@section('meta')
    <title>{{ $club->name }} Football Club — History, Trophies & Statistics</title>
    <meta name="description" content="Discover {{ $club->name }}: club history, stadium, honours, season results and all-time statistics. Founded in {{ $year }}{{ $club->city ? ', based in ' . $club->city : '' }}." />
@endsection

@section('content')

    <!-- HERO -->
    <section class="hero-section hero-section--inner">
        <div class="hero-bg" style="background-image: url({{ asset('build/images/default-banner.webp') }});"></div>
        <div class="hero-overlay"></div>

        <nav class="country-breadcrumb">
            <div class="container">
                <a href="/">Home</a>
                <span class="breadcrumb-sep">›</span>
                <a href="{{ route('country.show', $club->country) }}">{{ $club->country->name }}</a>
                <span class="breadcrumb-sep">›</span>
                <span>{{ $club->name }}</span>
            </div>
        </nav>

        <div class="hero-content">
            <div class="container">
                <div class="club-hero-inner">
                    <div>
                        <p class="hero-title">{{ $club->nickname }} <span>{{ $club->name }}</span></p>
                        <p class="hero-slogan">{{ $club->description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CLUB INFO STATIC -->
    <section class="last-champ-section">
        <div class="container">
            <div class="club-info-grid @if($club->destroyed_at) club-info-grid-4 @endif">

                <!-- Logo block (wide) -->
                <div class="last-champ-card" style="padding: 10px 24px;">
                    @if($club->attachment)
                        <img src="{{ $club->attachment?->getFileUrl() }}" alt="{{ $club->name }}" class="lc-logo" style="width:100px;height:100px;">
                    @else
                        <img src="{{ asset('build/images/shield.svg') }}" alt="{{ $club->name }}" class="lc-logo" style="width:100px;height:100px;">
                    @endif
                    <div class="lc-info">
                        <div class="lc-season">Club</div>
                        <div class="lc-name">{{ $club->name }}</div>
                        <div class="lc-detail">{{ $club->city }}</div>
                    </div>
                </div>

                <!-- Stadium -->
                <div class="last-champ-card">
                    <svg width="48" height="48" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" style="color:#C8102E;flex-shrink:0">
                        <rect x="2" y="5" width="44" height="38" rx="11" stroke-width="2"/>
                        <rect x="7" y="11" width="34" height="26" stroke-width="1.5"/>
                        <line x1="24" y1="11" x2="24" y2="37" stroke-width="1.2"/>
                        <circle cx="24" cy="24" r="5" stroke-width="1.2"/>
                        <circle cx="24" cy="24" r="1" fill="currentColor" stroke="none"/>
                        <rect x="7" y="16" width="8" height="16" stroke-width="1.2"/>
                        <rect x="33" y="16" width="8" height="16" stroke-width="1.2"/>
                    </svg>
                    @php
                        [$stadium, $capacity] = array_pad(explode(',', $club->stadium ?? '', 2), 2, null);
                    @endphp
                    <div class="lc-info">
                        <div class="lc-season">Stadium</div>
                        <div class="lc-name">
                            {{ $stadium }}
                        </div>
                        <div class="lc-detail">
                            {{ trim($capacity) }}
                        </div>
                    </div>
                </div>

                <!-- Founded -->
                <div class="last-champ-card">
                    <svg width="48" height="48" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#C8102E;flex-shrink:0">
                        <rect x="5" y="9" width="38" height="34" rx="3"/>
                        <rect x="5" y="9" width="38" height="12" rx="3" fill="currentColor" fill-opacity="0.15" stroke="none"/>
                        <path d="M16 5v8M32 5v8" stroke-width="2.5"/>
                        <path d="M5 21h38" stroke-width="1" stroke-opacity="0.4"/>
                        <circle cx="13" cy="29" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="21" cy="29" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="29" cy="29" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="37" cy="29" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="13" cy="37" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="21" cy="37" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="29" cy="37" r="3.5" fill="currentColor" stroke="none"/>
                    </svg>
                    <div class="lc-info">
                        <div class="lc-season">Founded</div>

                        <div class="lc-name">
                            {{ $year ?? '—' }}
                        </div>

                        @if($detail)
                            <div class="lc-detail">
                                {{ $detail }}
                            </div>
                        @endif
                    </div>
                </div>

                @if($club->destroyed_at)
                    <!-- Destroyed -->
                    <div class="last-champ-card">
                        <svg width="48" height="48" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#C8102E;flex-shrink:0">

                            <rect x="5" y="9" width="38" height="34" rx="3"></rect>

                            <rect x="5" y="9" width="38" height="12" rx="3"
                                  fill="currentColor" fill-opacity="0.15" stroke="none"></rect>

                            <path d="M16 5v8M32 5v8" stroke-width="2.5"></path>

                            <path d="M5 21h38" stroke-width="1" stroke-opacity="0.4"></path>

                            <path d="M19 26 L29 36 M29 26 L19 36"
                                  stroke="currentColor"
                                  stroke-width="2.8"
                                  stroke-linecap="round"></path>

                        </svg>

                        <div class="lc-info">
                            <div class="lc-season">Destroyed</div>

                            @php([$year, $detail] = $club->destroyed_date_parts)

                            <div class="lc-name">
                                {{ $year ?? '—' }}
                            </div>

                            @if($detail)
                                <div class="lc-detail">
                                    {{ $detail }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- SEO -->
    <section class="seo-section">
        <div class="container">
            <h1>About {{ $club->name }}</h1>
            {!! $club->content !!}
        </div>
    </section>

    @if($club->names->count())
        <!-- PREVIOUS NAMES -->
        <section class="prev-names-section">
            <div class="container">
                <h2 class="section-title" style="margin-bottom: 28px;">Previous Names</h2>
                <div class="prev-names-list">
                    @foreach($club->names as $name)
                        <div class="prev-names-item">
                            <div class="prev-names-year">
                                {{ $name->period }}
                            </div>

                            <div class="prev-names-name">
                                {{ $name->name }}
                            </div>

                            <div class="prev-names-note">
                                {{ $name->note ?? '' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($stats->count())
        <!-- TROPHIES -->
        <section class="alltime-section @if($club->names->count()) alltime-section--light @endif">
            <div class="container">
                <h2 class="section-title">Trophies & Finals</h2>
                <div class="table-scroll">

                    <table class="alltime-table">
                        <thead>
                        <tr>
                            <th>Tournament</th>
                            <th>Count</th>
                            <th>Years</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($stats as $stat)

                            {{-- CHAMPIONS --}}
                            <tr class="row-1st row-first">
                                <td class="club-name" rowspan="3">
                                    <span class="club-inner">
                                        {{ $stat['type']->label() }}
                                    </span>
                                </td>

                                <td class="titles">
                                    {{ isset($stat['champions']) ? $stat['champions']['count'] : 0 }}
                                </td>

                                <td class="years">
                                    {{ isset($stat['champions']) ? $stat['champions']['years'] : '—' }}
                                </td>
                            </tr>

                            {{-- RUNNER-UP --}}
                            <tr class="row-2nd">
                                <td class="runnerup">
                                    {{ isset($stat['runnerups']) ? $stat['runnerups']['count'] : 0 }}
                                </td>

                                <td class="years">
                                    {{ isset($stat['runnerups']) ? $stat['runnerups']['years'] : '—' }}
                                </td>
                            </tr>

                            @if($stat['third'] ?? false)
                                {{-- THIRD --}}
                                <tr class="row-3rd">
                                    <td class="third">
                                        {{ $stat['third']['count'] }}
                                    </td>

                                    <td class="years">
                                        {{ $stat['third']['years'] ?: '—' }}
                                    </td>
                                </tr>
                            @endif

                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </section>
    @endif

@endsection
