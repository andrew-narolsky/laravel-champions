import BaseModel from '../core/base-model';

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
