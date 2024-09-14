import Modal from "./invoices/modal";

(function ($) {
	var invoice;

	/**
	 * Setup variables and initialize the invoice UI.
	 */
	var models = {}, collections = {}, views = {};

	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */
	models.LineItem = Backbone.Model.extend({
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
			taxes: [],
		},
	});

	models.LineTax = Backbone.Model.extend({
		defaults: {
			id: null,
			name: '',
			rate: 0,
			amount: 0,
			total: 0,
		},
	});

	models.State = Backbone.Model.extend({
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
	});

	/**
	 * ========================================================================
	 * COLLECTIONS
	 * ========================================================================
	 */

	collections.LineItems = Backbone.Collection.extend({
		model: models.LineItem,

		preinitialize: function (models, options) {
			this.options = options;
		},
	});

	collections.LineTaxes = Backbone.Collection.extend({
		model: models.LineTax,

		preinitialize: function (models, options) {
			this.options = options;
		},
	});


	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */


	views.AddLineItemModal = Modal.extend({
		/**
		 * @since 3.0
		 */
		// el: '#edd-admin-order-add-item-dialog',

		/**
		 * @since 3.0
		 */
		template: wp.template('wc-modal-tracking-setup'),

		events: {
			'change [name="quantity"]': 'onChangeQuantity',
			'change [name="price"]': 'onChangePrice',
		},
		//
		initialize: function () {

			// const { state } = this.options;
			// const id = uuid();

			// Create a fresh `OrderItem` to be added.
			this.model = new models.LineItem({
				error: false,
				quantity: 1,
				price: 0,
				subtotal: 0,
				// state,
			});
			// Listen for events.
			this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model, 'change', this.updateSubtotal);
			Modal.prototype.initialize.apply(this, arguments);
		},



		prepare: function () {
			console.log("AddLineItemModal prepare");
			const { model, options } = this;
			const { state } = options;

			// const { currency } = state.get('formatters');

			const defaults = Modal.prototype.prepare.apply(this, arguments);

			console.log(defaults)

			return {
				...defaults,
			};
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON()));
			$(document.body).trigger('eac_update_ui');
			jQuery( document.body ).css({
				'overflow': 'hidden'
			}).append( this.$el );

			return this;
		},


		onChangeQuantity: function (e) {
			const quantity = parseInt(e.target.value, 10);
			this.model.set('quantity', quantity);
			console.log('onChangeQuantity');
		},

		onChangePrice: function (e) {
			const price = parseFloat(e.target.value);
			this.model.set('price', price);
			console.log('onChangePrice');
		},

		updateSubtotal: function () {
			const { model } = this;
			const quantity = model.get('quantity');
			const price = model.get('price');
			const subtotal = quantity * price;
			model.set('subtotal', subtotal);
		}
	});


	views.LineItem = wp.Backbone.View.extend({
		tagName: 'tr',

		template: wp.template('invoice-line-item'),
	});

	views.LineItems = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-invoice-table__items',
	});

	views.NoItems = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-invoice-table__no-items',

		template: wp.template('eac-invoice-no-items'),
	});

	views.Totals = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-invoice-table__totals',

		template: wp.template('eac-invoice-totals'),
	});

	views.Actions = wp.Backbone.View.extend({
		tagName: 'tfoot',

		className: 'eac-invoice-table__actions',

		template: wp.template('eac-invoice-actions'),

		events: {
			'click .add-item': 'onAddItem',
			// 'click #add-discount': 'onAddOrderDiscount',
			// 'click #add-adjustment': 'onAddOrderAdjustment',
		},

		render: function () {
			wp.Backbone.View.prototype.render.apply( this, arguments );
			return this;
		},

		onAddItem: function (e) {
			e.preventDefault();
			new views.AddLineItemModal( this.options ).render();
			//$(document.body).trigger('eac_update_ui');
		},
	});

	views.Invoice = wp.Backbone.View.extend({
		el: '#eac-invoice-form',

		render: function () {
			const {state} = this.options;
			const items = state.get('items');
			if (!items.length) {
				this.views.add('.eac-invoice-table', new views.NoItems(this.options));
			}
			this.views.add('.eac-invoice-table', new views.Totals(this.options));
			this.views.add('.eac-invoice-table', new views.Actions(this.options));
			return this;
		},
	});


	/**
	 * Initialize the invoice UI.
	 */
	var int = function () {

		const state = new models.State({
			...eac_invoices_vars.invoice || {},
		});

		state.set({
			items: new collections.LineItems(null, {state}),
		});

		// Initialize the frame view.
		var frame = new views.Invoice({state});

		// Hydrate collections.
		// var items = eac_invoices_vars.invoice.items || [];
		// items.forEach(function (item) {
		// 	state.get('items').add(item);
		// });

		frame.render();
	}

	// Initialize the invoice UI.
	$(int);

})(jQuery);
