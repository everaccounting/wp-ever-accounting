export default Backbone.Collection.extend({
	/**
	 * API root.
	 */
	apiRoot: wpApiSettings.root || '/wp-json',

	/**
	 * API namespace.
	 */
	namespace: 'eac/v1',

	/**
	 * API endpoint.
	 */
	endpoint: '',

	/**
	 * Request nonce.
	 */
	nonce: wpApiSettings.nonce || '',

	/**
	 * Setup default state.
	 */
	preinitialize: function( models, options ) {
		this.options = options;
	},

	/**
	 * Get the URL for the model.
	 *
	 * @return {string}.
	 */
	url: function () {
		return this.apiRoot.replace(/\/+$/, '') + '/' + this.namespace.replace(/\/+$/, '') + '/' + this.endpoint.replace(/\/+$/, '');
	},

	/**
	 * Set nonce header before every Backbone sync.
	 *
	 * @param {string} method.
	 * @param {Backbone.Model} model.
	 * @param {{beforeSend}, *} options.
	 * @return {*}.
	 */
	sync: function (method, model, options) {
		var beforeSend;
		options = options || {};
		// if cached is not set then set it to true.
		if ( _.isUndefined( options.cache ) ) {
			options.cache = true;
		}

		// Include the nonce with requests.
		if ( ! _.isEmpty(model.nonce) ) {
			beforeSend = options.beforeSend;
			options.beforeSend = function( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', model.nonce );

				if ( beforeSend ) {
					return beforeSend.apply( this, arguments );
				}
			};

			// Update the nonce when a new nonce is returned with the response.
			options.complete = function( xhr ) {
				var returnedNonce = xhr.getResponseHeader( 'X-WP-Nonce' );
				if ( ! _.isEmpty( returnedNonce ) ) {
					model.nonce = returnedNonce;
				}
			};
		}

		return Backbone.sync( method, model, options );
	}
});
