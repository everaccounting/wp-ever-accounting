window.eac_invoice = window.eac_invoice || {};
import {Currency} from "@eac/currency";

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
	 * invoice.model.FrameState
	 *
	 * The model for the state of the invoice frame.
	 *
	 * @param {Object} options
	 */
	invoice.model.FrameState = Backbone.Model.extend({
		defaults: {
			id: null,
			number: '',
			date: '',
			due_date: '',
			status: 'draft',
			customer_id: null,
			customer_name: '',
			customer_email: '',
			customer_address: '',
			customer_phone: '',
			customer_vat: '',
			customer_note: '',
			currency_code: '',
			currency_rate: 1,
			subtotal: 0,
			subtotal_tax: 0,
			discount: 0,
			discount_tax: 0,
			tax_total: 0,
			total: 0,
			items: [],
		},
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
	 * @param {Object} options Collection options.
	 */
	invoice.collection.Items = Backbone.Collection.extend({
		model: invoice.model.Item,

		preinitialize: function (models, options) {
			console.log("COLLECTIONS ORDER ITEMS", options);
			this.options = options;
		},
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	/**
	 * invoice.view.Item
	 *
	 * The view for an invoice item.
	 *
	 * @param {Object} options
	 */
	invoice.view.Item = wp.Backbone.View.extend({
		tagName: 'tr',
		template: wp.template('eac-invoice-item'),

		initialize: function () {
			this.listenTo(this.model, 'change', this.render);
		},

		prepare: function () {
			const {model, options} = this;
			const {state} = options;
			console.log("VIEW ITEM", model, options);

			return {
				...model.toJSON(),
			}
		}
	});

	/**
	 * invoice.view.Items
	 *
	 * The view for the line items.
	 *
	 * @param {Object} options
	 */
	invoice.view.Items = wp.Backbone.View.extend({
		el: '#eac-invoice-items',

		initialize: function () {
			const {state} = this.options;
			const items = state.get('items');
			this.listenTo(items, 'add', this.render);
			this.listenTo(items, 'remove', this.render);
		},

		render: function () {
			const {state} = this.options;
			const items = state.get('items');

			this.views.remove();

			if (0 === items.length) {
				console.log('No items');
			} else {
				_.each(items.models, (model) => this.add(model));
			}

			return this;
		},

		add: function (model) {
			this.views.add(new invoice.view.Item({
				...this.options,
				model
			}));
		}
	});

	/**
	 * invoice.view.Frame
	 *
	 * Top-level view for the invoice screen.
	 */
	invoice.view.Frame = wp.Backbone.View.extend({
		el: '#eac-invoice-form',

		events: {
			'change .add-line-item': 'addLineItem',
		},

		render: function () {
			this.views.set('#eac-invoice-items', new invoice.view.Items(this.options));
			return this;
		},
		addLineItem: function (e) {
			var $select = $(e.target),
				item_id = parseInt($select.val(), 10);

			// Bail if the item_id is not found.
			if (isNaN(item_id)) {
				return;
			}

			// console.log(item_id);
			$select.val('').trigger('change');
			const {state} = this.options;
			const items = state.get('items') || [];

			// Fetch the item.
			wp.apiRequest({
				path: '/eac/v1/items/' + item_id,
				method: 'GET',
			}).done(function (response) {
				const model = new invoice.model.Item({
					...response,
					id: _.uniqueId('item_'),
					item_id: response?.id,
					description: response.description.length > 160 ? response.description.substring(0, 160) : response.description,
					subtotal: response.price
				});
				items.add(model);
			});
		},
	});

	/**
	 * Initialize the invoice UI.
	 */
	invoice.int = function () {

		const state = new invoice.model.FrameState({
			...eac_invoices_vars.invoice || {},
		});

		state.set({
			items: new invoice.collection.Items(null, {state}),
		});

		// Initialize the frame view.
		invoice.frame = new invoice.view.Frame({state});

		// Hydrate collections.
		// var items = eac_invoices_vars.invoice.items || [];
		// items.forEach(function (item) {
		// 	state.get('items').add(item);
		// });

		invoice.frame.render();
	};

	// Initialize the invoice UI.
	$(invoice.int);

})(jQuery);
