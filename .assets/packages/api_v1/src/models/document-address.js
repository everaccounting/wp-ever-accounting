import Base from './base.js';

export default Base.extend( {
	endpoint: 'addresses',

	defaults: {
		id: '',
		document_id: '',
		type: 'invoice',
		name: '',
		company: '',
		street: '',
		city: '',
		state: '',
		zip: '',
		country: '',
		phone: '',
		email: '',
		tax_number: '',
		updated_at: '',
		created_at: '',
	},
} );
