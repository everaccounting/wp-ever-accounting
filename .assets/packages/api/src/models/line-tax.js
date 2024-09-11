import Base from './base.js';

export default Base.extend( {
	endpoint: 'line-taxes',

	defaults: {
		id: null,
		name: '',
		rate: 0,
		compound: false,
		amount: 0,
		line_id: 0,
		tax_id: 0,
		document_id: 0,
		updated_at: '',
		created_at: '',
	},
} );
