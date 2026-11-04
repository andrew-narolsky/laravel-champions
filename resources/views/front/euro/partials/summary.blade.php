{{-- $summary: EuroStatsService::competitionSummary() --}}
@php
    $tileItems = array_filter([
        isset($summary['seasons']) ? ['Seasons', $summary['seasons'], 'seasons'] : null,
        ['Clubs', $summary['clubs'], 'clubs'],
        ['Matches', $summary['matches'], 'matches'],
        ['Goals', $summary['goals'], 'goals'],
    ]);
    $win = $summary['biggest_win'];

@endphp

@if($summary['matches'])
    <div class="stats-section">
        <div class="container">
            <div class="stats-v3-inner" id="stats-v3">
                @foreach($tileItems as [$label, $value, $icon])
                    <div class="v3-item">
                        <div class="v3-text">
                            <span class="v3-label">{{ $label }}</span>
                            <span class="v3-value" data-target="{{ $value }}" data-suffix="">{{ $value }}</span>
                        </div>
                        <div class="v3-icon">
                            <img src="{{ asset("build/images/icons/{$icon}.svg") }}" width="64" height="64" alt="{{ $label }}" loading="lazy">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="euro-records">
                <div><span class="v3-label">Goals per match</span> {{ number_format($summary['goals_per_match'], 2) }}</div>
                @if($win)
                    <div>
                        <span class="v3-label">Biggest win</span>
                        <a href="{{ route('club.show', $win->homeClub) }}">{{ $win->homeClub->name }}</a>
                        <strong>{{ $win->score() }}</strong>
                        <a href="{{ route('club.show', $win->awayClub) }}">{{ $win->awayClub->name }}</a>
                        <small>{{ $win->stage->label() }}, {{ $win->season->name }}</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
