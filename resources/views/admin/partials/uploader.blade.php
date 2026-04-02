<div class="file-uploader @if($file) loaded @endif"
     data-upload-url="{{ $uploadUrl }}"
     data-delete-url="{{ $deleteUrl }}"
     data-module="{{ $module }}"
     data-module-id="{{ $moduleId ?? '' }}"
     @if(!empty($allowed)) data-allowed="{{ $allowed }}" @endif
     @if(!empty($maxSize)) data-max-size="{{ $maxSize }}" @endif>
    <div class="drop-zone">
        <span class="text">
            <i class="mdi mdi-upload"></i>
        </span>
        <input type="file" id="file">
    </div>
    <div class="preview-list">
        @if($file)
            <div class="preview-item uploaded" data-id="{{ $id }}" data-url="{{ $file }}">
                <div class="thumb">
                    <img src="{{ $file }}" alt="{{ $module }}" loading="lazy">
                </div>
                <div class="info">
                    <button class="copy-btn btn btn-info">
                        <i class="mdi mdi-link"></i>
                    </button>
                    <button class="delete-btn btn btn-danger">
                        <i class="mdi mdi-window-close"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
