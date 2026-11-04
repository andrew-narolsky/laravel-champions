{{-- $club, optional $logo (default true), optional $year: show the name the club had that year --}}
@if($club)
    @php $clubName = $club->nameAt($year ?? null); @endphp
    <a href="{{ route('club.show', $club) }}" class="club-inner" @if($clubName !== $club->name) title="{{ $club->name }}" @endif>
        @if($logo ?? true)
            <img src="{{ $club->attachment?->getFileUrl() ?? asset('/build/images/shield.svg') }}" class="lc-logo" alt="{{ $clubName }}" loading="lazy">
        @endif
        {{ $clubName }}
    </a>
@else
    —
@endif
