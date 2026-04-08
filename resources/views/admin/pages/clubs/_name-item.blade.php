<div class="name-item border p-2 mb-2">
    <input type="text" name="names[{{ $index }}][name]" placeholder="Name"
           value="{{ $item->name ?? '' }}" class="form-control mb-2">

    <input type="number" name="names[{{ $index }}][from_year]" placeholder="From year"
           value="{{ $item->from_year ?? '' }}" class="form-control mb-2">

    <input type="number" name="names[{{ $index }}][to_year]" placeholder="To year"
           value="{{ $item->to_year ?? '' }}" class="form-control mb-2">

    <input type="text" name="names[{{ $index }}][note]" placeholder="Note"
           value="{{ $item->note ?? '' }}" class="form-control mb-2">

    <button type="button" class="btn btn-danger btn-sm remove-name">Remove</button>
</div>
