import BaseModel from '../core/base-model.js';

export default class Countries extends BaseModel {
    constructor() {
        super('countries-form', {
            slug: {
                sourceField: 'name',
                slugField: 'slug',
                separator: '-',
            }
        });
    }
}
