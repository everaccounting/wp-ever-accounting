import Base from './base.js';
import DocumentTaxes from "../collections/document-taxes";

export default Base.extend({
	endpoint: 'line-items',

	defaults: {
		id: null,
		type: 'standard',
		name: '',
		price: 0,
		quantity: 1,
		subtotal: 0,
		discount: 0,
		tax_total: 0,
		total: 0,
		description: '',
		unit: '',
		item_id: null,
		updated_at: '',
		created_at: '',

		// Relationships
		taxes: new DocumentTaxes(),
	},

});
