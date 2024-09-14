import Base from './base.js';
import DocumentItems from '../collections/document-items';

export default Base.extend({
	defaults: {
		id: '',
		type: '',
		status: '',
		number: '',
		contact_id: '',
		subtotal: 0,
		discount_total: 0,
		tax_total:0,
		total: 0,
		total_paid: 0,
		discount_amount: 0,
		discount_type: 'fixed',
		reference: '',
		note: '',
		vat_exempt: 'no',
		issue_date: '',
		due_date: '',
		sent_date: '',
		payment_date: '',
		currency_code: '',
		exchange_rate: '',
		parent_id: '',
		created_via: '',
		creator_id: '',
		uuid: '',
		updated_at: '',
		created_at: '',

		// Relationships
		address: {},
		currency: {},
		items: new DocumentItems(),

		// Flags
		is_fetching: false,
	},

	getNextNumber( options = {} ) {
		return this.fetch({
			url: `${this.apiRoot}${this.namespace}/utilities/next-number?type=${this.get('type')}`,
			type: 'GET',
			...options,
		})
	}
});
