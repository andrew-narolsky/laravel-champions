<form id="clubs-form" method="POST" action="{{ $action }}">
    @csrf
    <input type="hidden" name="_method" value="{{ $method }}">

    <div class="row">
        <div class="{{ isset($club) ? 'col-9' : 'col-12' }}">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input name="name"
                               type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               placeholder="Name"
                               value="{{ old('name', $club->name ?? '')}}">
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
                               value="{{ old('slug', $club->slug ?? '')}}">
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
                                    value="{{ $key }}" @selected(old('country_id', $club->country_id ?? 0) === $key)>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="nickname">Nickname</label>
                        <input name="nickname"
                               type="text"
                               class="form-control @error('nickname') is-invalid @enderror"
                               id="nickname"
                               placeholder="Nickname"
                               value="{{ old('nickname', $club->nickname ?? '') }}">
                        @error('nickname')
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
                               value="{{ old('description', $club->description ?? '') }}">
                        @error('description')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea name="content"
                                  class="form-control text-editor @error('content') is-invalid @enderror"
                                  id="content">{{ old('content', $club->content ?? '') }}</textarea>
                        @error('content')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="founded_at">Founded</label>
                        <input name="founded_at"
                               type="date"
                               class="form-control @error('founded_at') is-invalid @enderror"
                               id="founded_at"
                               value="{{ old('founded_at', isset($club) && $club->founded_at ? $club->founded_at->format('Y-m-d') : '') }}">
                        @error('founded_at')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="destroyed_at">Destroyed</label>
                        <input name="destroyed_at"
                               type="date"
                               class="form-control @error('destroyed_at') is-invalid @enderror"
                               id="destroyed_at"
                               value="{{ old('destroyed_at', isset($club) && $club->destroyed_at ? $club->destroyed_at->format('Y-m-d') : '') }}">
                        @error('destroyed_at')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input name="city"
                               type="text"
                               class="form-control @error('city') is-invalid @enderror"
                               id="city"
                               placeholder="City"
                               value="{{ old('city', $club->city ?? '') }}">
                        @error('city')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="stadium">Stadium</label>
                        <input name="stadium"
                               type="text"
                               class="form-control @error('stadium') is-invalid @enderror"
                               id="stadium"
                               placeholder="Stadium"
                               value="{{ old('stadium', $club->stadium ?? '') }}">
                        @error('stadium')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input name="address"
                               type="text"
                               class="form-control @error('address') is-invalid @enderror"
                               id="address"
                               placeholder="Address"
                               value="{{ old('address', $club->address ?? '') }}">
                        @error('address')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        @isset($club)
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="file">Thumbnail</label>
                            @include('admin.partials.uploader', [
                                'module' => $club::MODULE_NAME,
                                'moduleId' => $club->id ?? null,
                                'id' => $club->attachment->id ?? null,
                                'file' => $club->attachment?->getFileUrl() ?? null,
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
            new Clubs();
            TextEditor.init();
        });
    </script>
@stop
