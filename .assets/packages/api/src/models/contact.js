import Base from './base.js';

export default Base.extend( {
	endpoint: 'contacts',

	defaults: {
		id: null,
		type: 'customer',
		name: '',
		company: '',
		email: '',
		phone: '',
		website: '',
		address: '',
		city: '',
		state: '',
		postcode: '',
		country: '',
		vat_number: '',
		vat_exempt: false,
		currency_code: '',
		thumbnail_id: null,
		user_id: null,
		status: 'active',
		created_via: 'api',
		creator_id: null,
		updated_at: '',
		created_at: '',
	},
} );
