import BaseModel from '../core/base-model.js';

export default class Competitions extends BaseModel {
    constructor() {
        super('competitions-form', {
            slug: {
                sourceField: 'name',
                slugField: 'slug',
                separator: '-',
            }
        });
    }
}
