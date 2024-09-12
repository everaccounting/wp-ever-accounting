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

	/**
	 * Get the URL for the model.
	 *
	 * @return {string}.
	 */
	url: function () {
		var url = this.apiRoot.replace(/\/+$/, '') + '/' + this.namespace.replace(/\/+$/, '') + '/' + this.endpoint.replace(/\/+$/, '');
		if (!_.isEmpty(this.get('id'))) {
			url += '/' + this.get('id');
		}

		if (!_.isEmpty(this.get('code'))) {
			url += '/' + this.get('code');
		}

		return url;
	},

} );
