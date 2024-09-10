/* global Backbone, _, $ */

export const State = Backbone.Model.extend({
	defaults: {
		id: null,
		number: '',
		date: '',
		due_date: '',
		status: 'draft',
		customer_id: null,
		customer_name: '',
		customer_email: '',
		customer_address: '',
		customer_phone: '',
		customer_vat: '',
		customer_note: '',
		currency_code: '',
		currency_rate: 1,
		subtotal: 0,
		subtotal_tax: 0,
		discount: 0,
		discount_tax: 0,
		tax_total: 0,
		total: 0,
		items: [],
		tax_enabled: 'yes'
	},
});

export default State;
