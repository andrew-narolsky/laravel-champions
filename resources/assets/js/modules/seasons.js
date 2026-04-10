import BaseModel from '../core/base-model';

export default class Seasons extends BaseModel {
    constructor() {
        super('seasons-form');
        this.initPositions();
    }

    initPositions() {
        document.querySelectorAll('[data-add-position]').forEach(addBtn => {
            const place = addBtn.dataset.addPosition;
            const container = document.getElementById(`${place}-container`);
            const template = document.getElementById(`${place}-template`);

            if (!container || !template) return;

            addBtn.addEventListener('click', () => {
                const clone = template.content.cloneNode(true);
                const select = clone.querySelector('select');
                container.appendChild(clone);
                new SlimSelect({ select, settings: { showSearch: false } });
            });

            container.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.remove-position-row');
                if (!removeBtn) return;
                if (container.querySelectorAll('.position-row').length > 1) {
                    removeBtn.closest('.position-row').remove();
                }
            });
        });
    }
}