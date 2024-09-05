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
	 * invoice.model.Item
	 *
	 * The model for an invoice item.
	 *
	 * @param {Object} options
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
	});


	/**
	 * invoice.collection.Items
	 *
	 * The collection for the line items.
	 *
	 * @param {Object} options Collection options.
	 */
	invoice.collection.Items = Backbone.Collection.extend({
		model: invoice.model.Item,
	});

	/**
	 * invoice.view.Item
	 *
	 * The view for the line item.
	 *
	 * @param {Object} options
	 */
	invoice.view.Item = wp.Backbone.View.extend({
		tagName: 'tr',
		className: 'eac-invoice-item',

		template: wp.template('eac-invoice-item'),
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
		},
	});

	/**
	 * invoice.view.Frame
	 *
	 * Top-level view for the invoice screen.
	 *
	 * @param {Object} options
	 */
	invoice.view.Frame = wp.Backbone.View.extend({

		el: '#eac-invoice-form',

		events: {
			'change .add-line-item': 'addLineItem',
		},

		render: function () {
			console.log(this.options);

			return this;
		},

		addLineItem: function (e) {
			var self = this,
				$select = $(e.target),
				item_id = parseInt($select.val(), 10);

			// Bail if the item_id is not found.
			if (!item_id) {
				return;
			}

			$select.val('').trigger('change');
			const {state} = this.options;
			const items = state.get('items') || [];

			// Fetch the item.
			wp.apiRequest({
				path: '/eac/v1/items/' + item_id,
				method: 'GET',
			}).done(function (response) {
				var model = new invoice.model.Item({...response, id: undefined});
				items.add(model);
			});
		},
	});

	/**
	 * Initialize the invoice UI.
	 */
	invoice.int = function () {
		var state;

		// Initialize the state model.
		state = new invoice.model.FrameState({
			...eac_invoices_vars.invoice || {},
		});

		// Initialize the items collection.
		state.set({
			items: new invoice.collection.Items({state}),
		});

		// Initialize the frame view.
		invoice.frame = new invoice.view.Frame({state});
		invoice.frame.render();
	};

	// Initialize the invoice UI.
	$(invoice.int);

})(jQuery);
