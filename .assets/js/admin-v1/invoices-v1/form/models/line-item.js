/* global Backbone, _, $ */

export const LineItem = Backbone.Model.extend({
	defaults: {
		id: null,
		type: 'standard',
		name: '',
		price: 0,
		quantity: 1,
		subtotal: 0,
		subtotal_tax: 0,
		discount: 0,
		discount_tax: 0,
		tax_total: 0,
		total: 0,
		taxable: false,
		description: '',
		unit: '',
		item_id: null,
		taxes: [],
	},
});

export default LineItem;
