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

	views.AddLineItemModal = wp.Backbone.View.extend({
		el: '#eac-invoice-add-item-modal-placeholder',

		template: wp.template('eac-invoice-add-item'),
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
			wp.Backbone.View.prototype.render.apply(this, arguments);
			// this.views.add( new views.AddLineItemModal(this.options) );
			return this;
		},

		onAddItem: function (e) {
			e.preventDefault();
			console.log('onAddItem');
			// now we need to open the modal.
			new views.AddLineItemModal(this.options).render();
		},
	});


	// views.AddTaxesModal = Modal.extend({});


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
