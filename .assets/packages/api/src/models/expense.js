import Base from './base.js';

export default Base.extend( {
	endpoint: 'payments',

	defaults: {
		id: '',
		type: '',
		number: '',
		date: '',
		amount: '',
		currency_code: '',
		exchange_rate: '',
		reference: '',
		note: '',
		payment_method: '',
		account_id: '',
		document_id: '',
		contact_id: '',
		category_id: '',
		attachment_id: '',
		parent_id: '',
		reconciled: '',
		status: '',
		uuid: '',
		created_via: '',
		creator_id: '',
		updated_at: '',
		created_at: '',
	},
} );
