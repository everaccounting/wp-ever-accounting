window.eac_invoice = window.eac_invoice || {};

(function ($) {
	var invoice;

	/**
	 * Expose the module in window.window.eac_invoice.
	 */
	invoice = window.eac_invoice = {model: {}, view: {}, collection: {}};

	// For debugging.
	invoice.debug = true;

	/**
	 * invoice.log
	 *
	 * A debugging utility for invoice. Works only when a
	 * debug flag is on and the browser supports it.
	 */
	invoice.log = function () {
		if (window.console && invoice.debug) {
			window.console.log.apply(window.console, arguments);
		}
	};

	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */

	invoice.model.Item = Backbone.Model.extend({
		defaults: {
			id: null,
			type: 'standard',
			name: '',
			price: 0,
			quantity: 1,
			subtotal: 0,
			subtotal_tax: 0,
			discount: 0,
			discount_tax: 0,
			tax_total: 0,
			total: 0,
			taxable: false,
			description: '',
			unit: '',
			item_id: null,
		},

		fetch: function (id) {
			return wp.ajax.send({
				url: eac_invoices_vars.rest_url + '/items/' + id,
				method: 'GET',
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', eac_invoices_vars.rest_nonce);
				},
			})
		}
	});

	/**
	 * invoice.model.LineTax
	 *
	 * The model for the line item tax.
	 *
	 * @param {Object} options
	 */
	invoice.model.LineTax = Backbone.Model.extend({
		defaults: {
			'id': null,
			'name': '',
			'rate': 0,
			'is_compound': false,
			'amount': 0,
			'line_id': null,
			'tax_id': null,
			'document_id': null,
		}
	});

	/**
	 * invoice.model.FrameState
	 *
	 * The model for the state of the invoice frame.
	 *
	 * @param {Object} options
	 */
	invoice.model.FrameState = Backbone.Model.extend({
		defaults: {
			loading: false,
			invoice: {
				balance: 0,
				billing_data: {},
				contact_id: 0,
				created_at: null,
				created_via: null,
				creator_id: 1,
				currency_code: "USD",
				discount_amount: null,
				discount_total: null,
				discount_type: "fixed",
				due_date: '',
				exchange_rate: null,
				fees_total: null,
				id: null,
				issue_date: '',
				items_total: null,
				note: null,
				notes: "",
				number: "",
				parent_id: null,
				payment_date: null,
				reference: null,
				sent_date: null,
				shipping_total: null,
				status: "draft",
				tax_inclusive: false,
				tax_total: null,
				total: null,
				total_paid: null,
				type: "invoice",
				updated_at: null,
				uuid: "",
				vat_exempt: false,
				items: [],
			}
		},

		initialize: function () {
			this.set('invoice.items', new invoice.collection.Items(null, { state:this }));
		}
	});

	/**
	 * ========================================================================
	 * COLLECTIONS
	 * ========================================================================
	 */

	/**
	 * invoice.collection.Items
	 *
	 * The collection for the line items.
	 *
	 * @param {null|Array} models List of Models.
	 * @param {Object} options Collection options.
	 */
	invoice.collection.Items = Backbone.Collection.extend({
		model: invoice.model.Item,

		preinitialize: function (models, options) {
			this.options = options;
		},
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	/**
	 * invoice.view.Items
	 *
	 * The view for the line items.
	 *
	 * @param {Object} options
	 */
	invoice.view.Items = Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-invoice-line-items',
	});

	/**
	 * invoice.view.Summary
	 *
	 * The view for the invoice summary.
	 *
	 * @param {Object} options
	 */
	invoice.view.Summary = Backbone.View.extend({
		el: '#eac-invoice-summary',
	});

	/**
	 * invoice.view.Frame
	 *
	 * Top-level view for the invoice screen.
	 */
	invoice.view.Frame = Backbone.View.extend({
		defaults: {
			invoice: {},
		},

		el: '#eac-invoice-form',

		render: function () {
			console.log(this.views);
			// this.views.add( new Summary( this.options ) );

			return this;
		},
	});

	/**
	 * Initialize the invoice UI.
	 */
	invoice.int = function () {
		var state;
		// Initialize the state model.
		state = new invoice.model.FrameState({
			columns: eac_invoices_vars.columns,
			invoice: eac_invoices_vars.invoice,
		});

		invoice.frame = new invoice.view.Frame({ state });
		invoice.frame.render();
	};

	// Initialize the invoice UI.
	$(invoice.int);

})(jQuery);
