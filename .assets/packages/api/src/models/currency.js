import Base from './base.js';

export default Base.extend( {
	endpoint: 'currencies',

	defaults: {
		id: null,
		code: '',
		name: '',
		exchange_rate: 1,
		precision: 2,
		symbol: '$',
		subunit: '',
		position: 'before',
		thousand_separator: ',',
		decimal_separator: '.',
		status: 'active',
		updated_at: '',
		created_at: '',
	},
} );
