import Base from './base.js';
import LineItems from '../collections/line-items'

export default Base.extend( {
	endpoint: 'invoices',

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
		items: new LineItems(),
	},

} );
