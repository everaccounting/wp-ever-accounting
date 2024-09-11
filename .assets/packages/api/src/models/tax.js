import Base from './base.js';

export default Base.extend( {
	endpoint: 'taxes',

	defaults: {
		id: null,
		name: '',
		rate: 0,
		compound: false,
		description: '',
		status: 'active',
		updated_at: '',
		created_at: '',
	},
} );
