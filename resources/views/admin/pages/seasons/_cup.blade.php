@php
    $existingResult = ($season ?? null)?->result;

    $championIds = old('result.places.champion', $existingResult?->champions->pluck('id')->toArray() ?? []);
    if (empty($championIds)) $championIds = [null];

    $runnerUpIds = old('result.places.runner_up', $existingResult?->runnerUps->pluck('id')->toArray() ?? []);
    if (empty($runnerUpIds)) $runnerUpIds = [null];
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

<div class="form-group">
    <label>Score</label>
    <input type="text"
           name="result[score]"
           class="form-control"
           placeholder="e.g. 2-1"
           value="{{ old('result.score', $existingResult?->score ?? '') }}">
</div>