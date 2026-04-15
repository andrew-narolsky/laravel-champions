export default class BaseModel {

    form;
    updateButton;

    constructor(formId, options = {}) {
        this.form = document.getElementById(formId);
        this.updateButton = document.querySelector('#update');

        if (!this.form || !this.updateButton) {
            return;
        }

        FormHelper.initSelects(this.form, {showSearch: false});

        if (options.slug) {
            this.initSlugGenerator(options.slug);
        }

        this.init();
    }

    init() {
        this.updateButton.addEventListener(
            'click',
            this.onUpdateButtonClick.bind(this)
        );
    }

    onUpdateButtonClick(e) {
        e.preventDefault();
        FormHelper.send(this.form);
    }

    initSlugGenerator({
                          sourceField,
                          slugField,
                          separator = '_',
                      }) {
        const sourceInput = this.form.querySelector(`#${sourceField}`);
        const slugInput = this.form.querySelector(`#${slugField}`);

        if (!sourceInput || !slugInput) {
            return;
        }

        let slugManuallyEdited = false;

        slugInput.addEventListener('input', () => {
            slugManuallyEdited = true;
        });

        sourceInput.addEventListener('input', () => {
            if (slugManuallyEdited) {
                return;
            }

            slugInput.value = this.slugify(
                sourceInput.value,
                separator
            );
        });
    }

    slugify(text, separator) {
        return text
            .toString()
            .trim()
            .toLowerCase()
            .replace(/[\s]+/g, separator)
            .replace(/[^\w\-]+/g, '')
            .replace(new RegExp(`${separator}{2,}`, 'g'), separator)
            .replace(new RegExp(`^${separator}|${separator}$`, 'g'), '');
    }
}
