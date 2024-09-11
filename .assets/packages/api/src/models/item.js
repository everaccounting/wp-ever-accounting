import Base from './base.js';
import TaxesCollection from '../collections/taxes';

export default Base.extend( {
	endpoint: 'items',

	defaults: {
		id: null,
		type: 'standard',
		name: '',
		description: '',
		unit: '',
		price: 0,
		cost: 0,
		taxable: false,
		tax_ids: '',
		category_id: null,
		status: 'active',
		updated_at: '',
		created_at: '',
	},


	getTaxes() {
		var ids = this.get( 'tax_ids' ),
			taxes = new TaxesCollection();
		console.log(this.toJSON());
		if (_.isEmpty(ids)) {
			return jQuery.Deferred().resolve( [] );
		}

		return taxes.fetch({
			data: {
				include: ids,
			},
		});
	}
} );
