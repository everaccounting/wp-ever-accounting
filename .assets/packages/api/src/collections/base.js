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
	initialize: function( models, options ) {
		this.state = {
			data: {},
			total: null,
			page: 1,
		};
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

		// When reading, add pagination data.
		if ( 'read' === method ) {
			if ( options.data ) {
				self.state.data = _.clone( options.data );

				delete self.state.data.page;
			} else {
				self.state.data = options.data = {};
			}

			if ( 'undefined' === typeof options.data.page ) {
				self.state.page  = null;
				self.state.total = null;
			} else {
				self.state.page = options.data.page - 1;
			}

			success = options.success;
			options.success = function( data, textStatus, request ) {
				if ( ! _.isUndefined( request ) ) {

					self.state.total = parseInt( request.getResponseHeader( 'x-wp-total' ), 10 );
				}

				if ( null === self.state.page ) {
					self.state.page = 1;
				} else {
					self.state.page++;
				}

				if ( success ) {
					return success.apply( this, arguments );
				}
			};
		}

		return Backbone.sync( method, model, options );
	}
});
