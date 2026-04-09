<div class="form-group">
    <label>Champion</label>
    <select name="result[champion_id]">
        @foreach($clubs as $id => $club)
            <option
                value="{{ $id }}" @selected(old('result.champion_id', $season->result->champion_id ?? 0) == $id)>
                {{ $club }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Runner-up</label>
    <select name="result[runner_up_id]">
        @foreach($clubs as $id => $club)
            <option
                value="{{ $id }}" @selected(old('result.runner_up_id', $season->result->runner_up_id ?? 0) == $id)>
                {{ $club }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Third place</label>
    <select name="result[third_place_id]">
        @foreach($clubs as $id => $club)
            <option
                value="{{ $id }}" @selected(old('result.third_place_id', $season->result->third_place_id ?? 0) == $id)>
                {{ $club }}
            </option>
        @endforeach
    </select>
</div>
