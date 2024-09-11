export default Backbone.Collection.extend({
	namespace: 'eac/v1',

	nonce: wpApiSettings.nonce,

	/**
	 * Initialize the collection.
	 *
	 * @param {Array} models.
	 * @param {Object} options.
	 * @return {void}.
	 */
	preinitialize: function (models, options) {
		this.options = options;
	},

	/**
	 * Get the URL for the collection.
	 *
	 * @return {string}.
	 */
	url: function () {
		const apiRoot = wpApiSettings.root || '/wp-json';
		return apiRoot.replace(/\/+$/, '') + '/' + this.namespace.replace(/\/+$/, '') + '/' + this.endpoint.replace(/\/+$/, '');
	},

	/**
	 * Set nonce header before every Backbone sync.
	 *
	 * @param {string} method.
	 * @param {Backbone.Model} model.
	 * @param {{beforeSend}, *} options.
	 * @return {*}.
	 */
	sync: function( method, model, options ) {
		options = options || {};

		if ( ! _.isEmpty( model.nonce ) ) {
			// Set nonce header.
			options.beforeSend = function( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', model.nonce );
			};

			// Update the nonce when a new nonce is returned with the response.
			options.complete = function( xhr ) {
				var newNonce = xhr.getResponseHeader( 'X-WP-Nonce' );
				if ( newNonce && newNonce !== model.nonce ) {
					model.set( 'nonce', newNonce );
				}
			}
		}

		return Backbone.sync(  method, model, options );
	}
});
