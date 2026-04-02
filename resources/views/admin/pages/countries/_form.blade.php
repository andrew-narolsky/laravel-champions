<form id="countries-form" method="POST" action="{{ $action }}">
    @csrf
    <input type="hidden" name="_method" value="{{ $method }}">

    <div class="row">
        <div class="{{ isset($country) ? 'col-9' : 'col-12' }}">
            <div class="card">
                <div class="card-body">
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
                </div>
            </div>
        </div>
        @isset($country)
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="file">Thumbnail</label>
                            @include('admin.partials.uploader', [
                                'module' => $country::MODULE_NAME,
                                'moduleId' => $country->id ?? null,
                                'id' => $country->thumbnail->id ?? null,
                                'file' => $country->thumbnail?->getFileUrl() ?? null,
                                'allowed' => 'images',
                                'uploadUrl' => route('attachments.upload'),
                                'deleteUrl' => route('attachments.destroy', ['attachment' => '__ID__']),
                            ])
                        </div>
                    </div>
                </div>
            </div>
        @endisset
    </div>
</form>

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
