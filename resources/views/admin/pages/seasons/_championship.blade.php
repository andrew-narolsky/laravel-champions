@php
    $existingResult = ($season ?? null)?->result;

    $championIds = old('result.places.champion', $existingResult?->champions->pluck('id')->toArray() ?? []);
    if (empty($championIds)) $championIds = [null];

    $runnerUpIds = old('result.places.runner_up', $existingResult?->runnerUps->pluck('id')->toArray() ?? []);
    if (empty($runnerUpIds)) $runnerUpIds = [null];

    $thirdPlaceIds = old('result.places.third_place', $existingResult?->thirdPlaces->pluck('id')->toArray() ?? []);
    if (empty($thirdPlaceIds)) $thirdPlaceIds = [null];
@endphp

@include('admin.pages.seasons._position-selector', [
    'place' => 'champion',
    'label' => 'Champion(s)',
    'existingIds' => $championIds,
])

@include('admin.pages.seasons._position-selector', [
    'place' => 'runner_up',
    'label' => 'Runner-up(s)',
    'existingIds' => $runnerUpIds,
])

@include('admin.pages.seasons._position-selector', [
    'place' => 'third_place',
    'label' => 'Third place',
    'existingIds' => $thirdPlaceIds,
])