import Base from './base.js';

export default Base.extend( {
	endpoint: 'notes',

	defaults: {
		id: null,
		object_id: null,
		creator_id: null,
		note_metadata:{},
		status: 'active',
		updated_at: '',
		created_at: '',
	},
} );
