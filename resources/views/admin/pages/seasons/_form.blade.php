<form id="seasons-form" method="POST" action="{{ $action }}">
    @csrf
    <input type="hidden" name="_method" value="{{ $method }}">
    <input type="hidden" name="competition_id" value="{{ $competition->id }}">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Season Name</label>
                        <input name="name"
                               type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               placeholder="e.g., 2024/25"
                               value="{{ old('name', ($season ?? null)?->name ?? '') }}">
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($competition->type === \App\Enums\CompetitionType::CHAMPIONSHIP)
                        @include('admin.pages.seasons._championship')
                    @else
                        @include('admin.pages.seasons._cup')
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // noinspection JSUnresolvedReference
            new Seasons();
        });
    </script>
@stop
