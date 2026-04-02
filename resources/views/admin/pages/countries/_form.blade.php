<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form id="countries-form" method="POST" action="{{ $action }}">
                    @csrf
                    <input type="hidden" name="_method" value="{{ $method }}">

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Name" value="{{ old('name', $country->name ?? '')}}">
                        @error('name')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input name="slug" type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" placeholder="Slug" value="{{ old('slug', $country->slug ?? '')}}">
                        @error('slug')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <input name="description" type="text" class="form-control @error('description') is-invalid @enderror" id="description" placeholder="Description" value="{{ old('description', $country->description ?? '') }}">
                        @error('description')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea name="content" class="form-control text-editor @error('content') is-invalid @enderror" id="content">{{ old('content', $country->content ?? '') }}</textarea>
                        @error('content')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('js')
    @vite('resources/assets/js/utils/text-editor.js')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // noinspection JSUnresolvedReference
            new Countries();
            TextEditor.init();
        });
    </script>
@stop
