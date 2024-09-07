const {money} = window.eac;
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
	invoice.model.InvoiceItem = Backbone.Model.extend({
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
	invoice.model.State = Backbone.Model.extend({
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
			tax_enabled: 'yes'
		},

		/**
		 * Determine if calculating tax.
		 *
		 * @return Boolean
		 */
		is_calculating_tax: function () {
			const {state} = this;
			return 'yes' !== state.get('tax_enabled') && !state.get('vat_exempt');
		}
	});

	/**
	 * ========================================================================
	 * COLLECTIONS
	 * ========================================================================
	 */

	/**
	 * invoice.collection.InvoiceItems
	 *
	 * The collection for the line items.
	 *
	 * @param {Object} options Collection options.
	 */
	invoice.collection.InvoiceItems = Backbone.Collection.extend({
		model: invoice.model.InvoiceItem,

		preinitialize: function (models, options) {
			this.options = options;
		},

		updateAmounts: function () {
			const {options} = this;
			const {state} = options;

			const items = state.get('items');
			// Keep track of all jQuery Promises.
			const promises = [];

			items.models.forEach((item) => {

			});

			return $.when.apply($, promises);
		},
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	/**
	 * invoice.view.InvoiceItem
	 *
	 * The view for an invoice item.
	 *
	 * @param {Object} options
	 */
	invoice.view.InvoiceItem = wp.Backbone.View.extend({
		tagName: 'tr',

		template: wp.template('eac-invoice-item'),

		events: {
			'change .line-price': 'onChangePrice',
			'change .line-quantity': 'onChangeQuantity',
			'click .remove-line': 'onDelete',
		},

		initialize: function () {
			this.listenTo(this.model, 'change', this.render);
		},

		prepare: function () {
			invoice.log('Preparing item:', this.model);
			const {model} = this;

			// Calculate the subtotal.
			var quantity = parseFloat(model.get('quantity'));
			var price = parseFloat(model.get('price'));
			var subtotal = (quantity * price);
			console.log(money.format(subtotal, '$'));
			return {
				...model.toJSON(),
				subtotal: subtotal,
				//formatted_subtotal: money.format(subtotal),
			}
		},

		render: function () {
			wp.Backbone.View.prototype.render.apply(this, arguments);
			this.$('.eac_select2').select2();
			return this;
		},

		onChangePrice: function (e) {
			this.model.set('price', parseFloat(e.target.value) || 0);
		},

		onChangeQuantity: function (e) {
			this.model.set('quantity', parseInt(e.target.value) || 1);
		},

		onDelete: function (e) {
			e.preventDefault();
			invoice.log('Deleting item:', this.model);
			const {model, options} = this;
			const {state} = options;

			state.get('items').remove(model);
			state.get('items').updateAmounts();
		},
	});

	/**
	 * invoice.view.InvoiceItems
	 *
	 * The view for the line items.
	 *
	 * @param {Object} options
	 */
	invoice.view.InvoiceItems = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-invoice-table__items',

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
			this.views.add(new invoice.view.InvoiceItem({
				...this.options,
				model
			}));
		}
	});

	/**
	 * invoice.view.InvoiceActions
	 *
	 * The view for the invoice actions.
	 *
	 * @param {Object} options
	 */
	invoice.view.InvoiceActions = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-invoice-table__actions',

		template: wp.template('eac-invoice-actions'),

		prepare: function () {
			const {state} = this.options;
			return state.toJSON();
		},

		render: function () {
			this.$el.html(this.template(this.prepare()));
			return this;
		}
	});

	/**
	 * invoice.view.InvoiceTotals
	 *
	 * The view for the invoice totals.
	 *
	 * @param {Object} options
	 */
	invoice.view.InvoiceTotals = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-invoice-table__totals',

		template: wp.template('eac-invoice-totals'),
	});

	/**
	 * invoice.view.Frame
	 *
	 * Top-level view for the invoice screen.
	 */
	invoice.view.Invoice = wp.Backbone.View.extend({
		el: '#eac-invoice-form',

		events: {
			'change .add-line-item': 'addLineItem',
			'change #contact_id': 'onCustomerChange',
			'change #currency_code': 'onCurrencyChange',
			'change #vat_exempt': 'onVatExemptChange',
		},

		render: function () {
			this.views.add('.eac-invoice-table', new invoice.view.InvoiceItems(this.options));
			this.views.add('.eac-invoice-table', new invoice.view.InvoiceActions(this.options));
			// this.views.add('.eac-invoice-table', new invoice.view.InvoiceTotals(this.options));

			$(document.body).trigger('eac_update_ui');
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
			}).done(function (item) {
				invoice.log('Fetched item:', item);
				const model = new invoice.model.InvoiceItem({
					id: _.uniqueId('new_'),
					item_id: item.id,
					name: item.name,
					price: item.price,
					quantity: 1,
					subtotal: item.price,
					description: item.description.substring(0, 160),
				});

				// silently add the model to the collection.
				items.add(model, {silent: true});

				items.updateAmounts().done(function () {
					items.trigger('add', model);
				});
			});
		},

		onCustomerChange: function (e) {
			e.preventDefault();
			console.log('Customer changed');
		},

		onCurrencyChange: function (e) {
			e.preventDefault();
			console.log('Currency changed');
		},

		onVatExemptChange: function (e) {
			e.preventDefault();
			console.log('VAT Exempt changed');
		}
	});

	/**
	 * Initialize the invoice UI.
	 */
	invoice.int = function () {
		invoice.log('Initializing the invoice UI.');

		const state = new invoice.model.State({
			...eac_invoices_vars.invoice || {},
		});

		state.set({
			items: new invoice.collection.InvoiceItems(null, {state}),
		});

		// Initialize the frame view.
		invoice.frame = new invoice.view.Invoice({state});

		// Hydrate collections.
		// var items = eac_invoices_vars.invoice.items || [];
		// items.forEach(function (item) {
		// 	state.get('items').add(item);
		// });

		invoice.frame.render();
	}

	// Initialize the invoice UI.
	// $(invoice.int);

})(jQuery);
