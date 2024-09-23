import Base from './base.js';

export default Base.extend( {
	endpoint: 'categories',

	defaults: {
		id: null,
		type: '',
		name: '',
		description: '',
		status: '',
		updated_at: '',
		created_at: '',
	},

} );
