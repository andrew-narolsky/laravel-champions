import BaseModel from '../core/base-model';

export default class Clubs extends BaseModel {
    constructor() {
        super('clubs-form', {
            slug: {
                sourceField: 'name',
                slugField: 'slug',
                separator: '-',
            }
        });

        this.wrapper = document.getElementById('names-wrapper');
        this.addButton = document.getElementById('add-name');

        this.initNameFields();
    }

    initNameFields() {
        if (this.addButton) {
            this.addButton.addEventListener('click', () => this.addName());
        }

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-name')) {
                e.target.closest('.name-item').remove();
            }
        });
    }

    addName() {
        const index = this.wrapper.children.length;

        const html = `
            <div class="name-item border p-2 mb-2">
                <input type="text" name="names[${index}][name]" placeholder="Name" class="form-control mb-2">
                <input type="number" name="names[${index}][from_year]" placeholder="From year" class="form-control mb-2">
                <input type="number" name="names[${index}][to_year]" placeholder="To year" class="form-control mb-2">
                <input type="text" name="names[${index}][note]" placeholder="Note" class="form-control mb-2">
                <button type="button" class="btn btn-danger btn-sm remove-name">Remove</button>
            </div>
        `;

        this.wrapper.insertAdjacentHTML('beforeend', html);
    }
}
