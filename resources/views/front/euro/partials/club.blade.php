{{-- $club, optional $logo (default true) --}}
@if($club)
    <a href="{{ route('club.show', $club) }}" class="club-inner">
        @if($logo ?? true)
            <img src="{{ $club->attachment?->getFileUrl() ?? asset('/build/images/shield.svg') }}" class="lc-logo" alt="{{ $club->name }}" loading="lazy">
        @endif
        {{ $club->name }}
    </a>
@else
    —
@endif
