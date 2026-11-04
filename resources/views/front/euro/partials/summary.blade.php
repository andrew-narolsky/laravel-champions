{{-- $summary: EuroStatsService::competitionSummary() --}}
@php
    $tileItems = array_filter([
        isset($summary['seasons']) ? ['Seasons', $summary['seasons'], 'seasons'] : null,
        ['Clubs', $summary['clubs'], 'clubs'],
        ['Matches', $summary['matches'], 'matches'],
        ['Goals', $summary['goals'], 'goals'],
    ]);
@endphp

@if($summary['matches'])
    <div class="stats-section">
        <div class="container">
            <div class="stats-v3-inner" id="stats-v3">
                @foreach($tileItems as [$label, $value, $icon])
                    <div class="v3-item">
                        <div class="v3-text">
                            <span class="v3-label">{{ $label }}</span>
                            <span class="v3-value" data-target="{{ $value }}" data-suffix="{{ $value >= 1000 ? '+' : '' }}" @if($value >= 1000) data-formatted="{{ number_format($value / 1000, 1) }}k+" @endif>{{ $value >= 1000 ? number_format($value / 1000, 1) . 'k+' : $value }}</span>
                        </div>
                        <div class="v3-icon">
                            <img src="{{ asset("build/images/icons/{$icon}.svg") }}" width="64" height="64" alt="{{ $label }}" loading="lazy">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
