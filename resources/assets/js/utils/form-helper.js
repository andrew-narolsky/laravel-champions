export default class FormHelper {

    static send(form, data = null) {
        this.clearErrors(form);

        const url = form.getAttribute('action');
        const formData = data ?? new FormData(form);

        return axios.post(url, formData)
            .then((response) => {

                Notyf.success(response.data.message);

                if (response.data.redirect) {
                    setTimeout(() => {
                        window.location = response.data.redirect;
                    }, 3000);
                }

                return response;
            })
            .catch((error) => {

                if (error.response) {

                    if (error.response.status === 422) {
                        this.handleValidationErrors(form, error.response.data.errors);
                        Notyf.error('Some fields are invalid!');
                        return;
                    }

                    const message = error.response.data?.message || 'Server error';
                    Notyf.error(message);

                } else {
                    Notyf.error('Network error');
                }

                throw error;
            });
    }

    static formatFieldName(field) {
        const parts = field.split('.');

        let result = parts.shift();

        parts.forEach(part => {
            result += `[${part}]`;
        });

        return result;
    }

    static handleValidationErrors(form, errors) {
        for (let field in errors) {
            const formatted = this.formatFieldName(field);

            const input = form.querySelector(`[name="${CSS.escape(formatted)}"]`);

            if (!input) continue;

            this.showFieldError(input, errors[field][0]);
        }
    }

    static showFieldError(input, message) {
        input.classList.add('is-invalid');

        let feedback = input.parentElement.querySelector('.invalid-feedback');

        if (!feedback) {
            feedback = document.createElement('span');
            feedback.classList.add('invalid-feedback');
            feedback.setAttribute('role', 'alert');
            input.parentElement.appendChild(feedback);
        }

        feedback.innerHTML = `<strong>${message}</strong>`;

        input.addEventListener('input', function handler() {
            input.classList.remove('is-invalid');
            feedback.remove();
            input.removeEventListener('input', handler);
        });
    }

    static clearErrors(form) {
        form.querySelectorAll('.is-invalid')
            .forEach(el => el.classList.remove('is-invalid'));

        form.querySelectorAll('.invalid-feedback')
            .forEach(el => el.remove());
    }

    static initSelects(form, settings = {}) {
        const selectElements = form.querySelectorAll('select');
        const instances = [];

        selectElements.forEach(select => {
            const slim = new SlimSelect({
                select,
                settings: {...settings},
            });
            instances.push(slim);
        });

        return instances;
    }
}
