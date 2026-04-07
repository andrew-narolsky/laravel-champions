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
    }
}
