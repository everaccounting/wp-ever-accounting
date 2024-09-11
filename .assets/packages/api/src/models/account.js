import Base from './base.js';

export default Base.extend( {
	endpoint: 'accounts',

	defaults: {
		id: '',
		type: 'bank',
		name: '',
		number: '',
		opening_balance: 0,
		bank_name: '',
		bank_phone: '',
		bank_address: '',
		currency_code: 'USD',
		creator_id: '',
		thumbnail_id: '',
		status: 'active',
		updated_at: '',
		created_at: '',
	},
} );
