export default class FileUploader {
    constructor(element) {
        this.wrapper = element;
        this.dropZone = element.querySelector('.drop-zone');
        this.input = element.querySelector('input[type=file]');
        this.previewList = element.querySelector('.preview-list');
        this.uploadUrl = element.dataset.uploadUrl;
        this.deleteUrlTemplate = element.dataset.deleteUrl;
        this.module = element.dataset.module;
        this.moduleId = element.dataset.moduleId;
        this.csrf = document.querySelector('meta[name="csrf-token"]').content;

        this.mimeGroups = {
            images: [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif'
            ],
            archives: [
                'application/gzip',
                'application/x-gzip',
            ],
            docs: ['application/pdf'],
        };

        this.allowedTypes = element.dataset.allowed
            ? element.dataset.allowed.split(',').flatMap(type =>
                this.mimeGroups[type] ?? [type]
            )
            : [];

        this.maxSize = element.dataset.maxSize
            ? parseInt(element.dataset.maxSize) * 1024 * 1024
            : 10 * 1024 * 1024;

        this.initEvents();
    }

    initEvents() {
        this.dropZone.addEventListener('click', () => this.input.click());
        this.input.addEventListener('change', (e) => this.handleFiles(e.target.files));

        this.dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.dropZone.classList.add('drag-over');
        });

        this.dropZone.addEventListener('dragleave', () => {
            this.dropZone.classList.remove('drag-over');
        });

        this.dropZone.addEventListener('drop', async (e) => {
            e.preventDefault();
            this.dropZone.classList.remove('drag-over');
            await this.handleFiles(e.dataTransfer.files);
        });

        this.previewList.querySelectorAll('.preview-item').forEach(div => {
            const copyBtn = div.querySelector('.copy-btn');
            const deleteBtn = div.querySelector('.delete-btn');

            if (copyBtn) {
                copyBtn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    await this.copyUrl(div, e.currentTarget);
                });
            }

            if (deleteBtn) {
                deleteBtn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    await this.deleteFile(div);
                });
            }
        });
    }

    async handleFiles(files) {
        for (const file of files) {

            if (this.allowedTypes.length && !this.allowedTypes.includes(file.type)) {
                Notyf.error('Unsupported file type');
                continue;
            }

            if (file.size > this.maxSize) {
                const maxMb = (this.maxSize / (1024 * 1024)).toFixed(0);
                Notyf.error('File more than ' + maxMb + 'MB');
                continue;
            }

            await this.uploadFile(file);
        }
    }

    async uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('module', this.module);
        formData.append('module_id', this.moduleId);

        this.wrapper.classList.add('loaded');
        const preview = this.createPreview(file);

        try {
            const response = await axios.post(this.uploadUrl, formData, {
                headers: {
                    'X-CSRF-TOKEN': this.csrf,
                    'Content-Type': 'multipart/form-data',
                },
                onUploadProgress: (progressEvent) => {
                    if (progressEvent.lengthComputable) {
                        const percent = (progressEvent.loaded / progressEvent.total) * 100;
                        preview.querySelector('.progress').style.width = `${percent}%`;
                    }
                },
            });

            const res = response.data;
            preview.dataset.id = res.id;
            preview.dataset.url = res.url;
            preview.querySelector('.progress').style.width = '100%';

            if (res.redirect) {
                Notyf.success('File successfully uploaded');
                setTimeout(() => window.location.reload(), 3000);
            }
            setTimeout(() => preview.classList.add('uploaded'), 1000);
        } catch (error) {
            Notyf.error(error.response.data.message ?? 'Upload failed');
            preview.classList.add('error');

            setTimeout(() => {
                this.wrapper.classList.remove('loaded');
                preview.remove();
                this.input.value = '';
            }, 1000);
        }
    }

    createPreview(file) {
        const div = document.createElement('div');
        div.className = 'preview-item';
        div.innerHTML = `
            <div class="thumb">
                ${file.type.startsWith('image')
            ? `<img src="${URL.createObjectURL(file)}" alt="">`
            : `<div class="file-icon"><i class="mdi mdi-zip-box"></i></div>`}
            </div>
            <div class="info">
                <button class="copy-btn btn btn-info">
                    <i class="mdi mdi-link"></i>
                </button>
                <button class="delete-btn btn btn-danger">
                    <i class="mdi mdi-window-close"></i>
                </button>
                <div class="progress-bar">
                    <div class="progress"></div>
                </div>
            </div>`;
        this.previewList.appendChild(div);

        div.querySelector('.copy-btn').addEventListener('click', async (e) => {
            e.preventDefault();
            await this.copyUrl(div, e.currentTarget);
        });

        div.querySelector('.delete-btn').addEventListener('click', async (e) => {
            e.preventDefault();
            await this.deleteFile(div);
        });

        return div;
    }

    async copyUrl(div, copyBtn) {
        const url = div.dataset.url;
        if (!url) return Notyf.error('The file has not been uploaded yet!');
        try {
            await navigator.clipboard.writeText(url);
            const icon = copyBtn.querySelector('i');
            const originalIcon = icon.className;
            icon.className = 'mdi mdi-check text-success';
            setTimeout(() => (icon.className = originalIcon), 1500);
        } catch (err) {
            Notyf.error('Could not copy link!');
        }
    }

    async deleteFile(preview) {
        const id = preview.dataset.id;
        if (!id) return;

        const url = this.deleteUrlTemplate.replace('__ID__', id);

        try {
            await axios.delete(url, {
                headers: {
                    'X-CSRF-TOKEN': this.csrf,
                },
            });

            this.wrapper.classList.remove('loaded');
            preview.remove();
            this.input.value = '';
        } catch (error) {
            Notyf.error('Delete failed!');
        }
    }

    static initAll() {
        document.querySelectorAll('.file-uploader').forEach(el => new FileUploader(el));
    }
}

document.addEventListener('DOMContentLoaded', () => FileUploader.initAll());
