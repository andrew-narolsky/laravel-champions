<form id="competitions-form" method="POST" action="{{ $action }}">
    @csrf
    <input type="hidden" name="_method" value="{{ $method }}">

    <div class="row">
        <div class="{{ isset($competition) ? 'col-9' : 'col-12' }}">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input name="name"
                               type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               placeholder="Name"
                               value="{{ old('name', $competition->name ?? '')}}">
                        @error('name')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input name="slug"
                               type="text"
                               class="form-control @error('slug') is-invalid @enderror"
                               id="slug"
                               placeholder="Slug"
                               value="{{ old('slug', $competition->slug ?? '')}}">
                        @error('slug')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="country_id">Country</label>
                        <select name="country_id"
                                id="country_id">
                            @foreach ($countries as $key => $country)
                                <option
                                    value="{{ $key }}" @selected(old('country_id', $competition->country_id ?? 0) === $key)>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                        @error('country_id')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type"
                                id="type">
                            @foreach($types as $value => $label)
                                <option
                                    value="{{ $value }}" @selected(old('type', $competition->type->value ?? '') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <input name="description"
                               type="text"
                               class="form-control @error('description') is-invalid @enderror"
                               id="description"
                               placeholder="Description"
                               value="{{ old('description', $competition->description ?? '') }}">
                        @error('description')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea name="content"
                                  class="form-control text-editor @error('content') is-invalid @enderror"
                                  id="content">{{ old('content', $competition->content ?? '') }}</textarea>
                        @error('content')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        @isset($competition)
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="file">Thumbnail</label>
                            @include('admin.partials.uploader', [
                                'module' => $competition::MODULE_NAME,
                                'moduleId' => $competition->id ?? null,
                                'id' => $competition->attachment->id ?? null,
                                'file' => $competition->attachment?->getFileUrl() ?? null,
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
            new Competitions();
            TextEditor.init();
        });
    </script>
@stop
