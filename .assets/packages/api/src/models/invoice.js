import Base from './base.js';

export default Base.extend( {
	endpoint: 'invoices',

	defaults: {
		id: '',
		type: '',
		status: '',
		number: '',
		contact_id: '',
		subtotal: '',
		discount_total: '',
		tax_total: '',
		total: '',
		total_paid: '',
		discount_amount: '',
		discount_type: '',
		billing_data: '',
		reference: '',
		note: '',
		vat_exempt: '',
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
	},

	updateTotals() {
		let items_amount = this.TotalBeforeDiscountAndTax();
	}
} );
