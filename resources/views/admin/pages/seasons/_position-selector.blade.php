{{-- params: $place, $label, $existingIds, $clubs --}}
<div class="form-group">
    <label>{{ $label }}</label>
    <div id="{{ $place }}-container">
        @foreach($existingIds as $selectedId)
            <div class="position-row d-flex align-items-center mb-2">
                <div class="flex-grow-1">
                    <select name="result[places][{{ $place }}][]">
                        <option value="">— Select club —</option>
                        @foreach($clubs as $id => $club)
                            <option value="{{ $id }}" @selected($id == $selectedId)>{{ $club }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-inverse-danger btn-icon ms-2 remove-position-row">
                    <i class="mdi mdi-minus"></i>
                </button>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn btn-inverse-success btn-sm mt-1" data-add-position="{{ $place }}">
        <i class="mdi mdi-plus"></i> Add club
    </button>
</div>

<template id="{{ $place }}-template">
    <div class="position-row d-flex align-items-center mb-2">
        <div class="flex-grow-1">
            <select name="result[places][{{ $place }}][]">
                <option value="">— Select club —</option>
                @foreach($clubs as $id => $club)
                    <option value="{{ $id }}">{{ $club }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" class="btn btn-inverse-danger btn-icon ms-2 remove-position-row">
            <i class="mdi mdi-minus"></i>
        </button>
    </div>
</template>
