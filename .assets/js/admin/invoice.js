/* global Backbone, _, jQuery, eac_invoices_vars */


/**
 * ========================================================================
 * INVOICE FORM HANDLER
 * ========================================================================
 */
jQuery(document).ready(($) => {
	'use strict';
	const FORM_ID = '#eac-invoice-form';

	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */
	var LineItemModel = Backbone.Model.extend({
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

	var LineItemTaxModel = Backbone.Model.extend({
		defaults: {
			id: null,
			name: '',
			rate: 0,
			amount: 0,
			total: 0,
		},
	});

	var State = Backbone.Model.extend({
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

	var LineItemsCollection = Backbone.Collection.extend({
		model: LineItemModel,

		preinitialize: function (models, options) {
			this.options = options;
		},
	});

	var LineItemTaxesCollection = Backbone.Collection.extend({
		model: LineItemTaxModel,

		preinitialize: function (models, options) {
			this.options = options;
		},
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	var NoLineItemsView = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'no-items',

		template: wp.template('eac-invoice-no-line-items'),
	});

	var LineItemsView = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-summary__items',

		template: wp.template('eac-invoice-line-items'),

		initialize() {
			const { state } = this.options;

			const items = state.get( 'items' );

			// Listen for events.
			// this.listenTo( items, 'add', this.render );
			// this.listenTo( items, 'remove', this.remove );
		},

		render() {
			const {state} = this.options;
			const items = state.get('items');
			this.views.remove();
			_.each( items.models, ( model ) => this.add( model ) );
		},

		add(model) {
			this.views.add(new LineItemView({
				...this.options,
				model,
			}));
		},

		remove(model) {
			let subview = null;

			this.views.each((view) => {
				if (view.model === model) {
					subview = view;
				}
			});
			if ( null !== subview ) {
				subview.remove();
			}
		}
	});

	var LineItemView = wp.Backbone.View.extend({
		tagName: 'tr',

		template: wp.template('eac-invoice-line-item'),
	});

	var ActionsView = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-summary__actions',

		template: wp.template('eac-invoice-actions'),

		events: {
			'change .add-line-item': 'onAddLineItem',
		},

		initialize(){
			const { state } = this.options;
			this.listenTo( state.get( 'items' ), 'add', this.stopSpinner );
		},

		onAddLineItem(e) {
			e.preventDefault();
			var $select = $(e.target),
				item_id = parseInt($select.val(), 10);

			// Bail if the item_id is not found.
			if (isNaN(item_id)) {
				return;
			}

			$select.val('').trigger('change');
			const {state} = this.options;
			const items = state.get('items') || [];
			this.startSpinner();
			wp.apiRequest({
				path: '/eac/v1/items/' + item_id,
				method: 'GET',
			}).done(function (response) {
				const model = new LineItemModel({
					...response,
					id: _.uniqueId('item_'),
					item_id: response?.id,
					description: response.description.length > 160 ? response.description.substring(0, 160) : response.description,
					subtotal: response.price
				});
				items.add(model);
			});
		},

		startSpinner() {
			this.$el.find('.spinner').addClass('is-active');
		},

		stopSpinner() {
			this.$el.find('.spinner').removeClass('is-active');
		}
	});

	var TotalsView = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-summary__totals',

		template: wp.template('eac-invoice-totals'),
	});

	var Form = wp.Backbone.View.extend({
		el: FORM_ID,

		initialize(){
			const { state } = this.options;
			this.listenTo( state.get( 'items' ), 'add, remove', this.render );
		},

		render() {
			console.log("Form view render");
			const { state } = this.options;
			if (state)
			this.views.add('.eac-document-summary', new NoLineItemsView(this.options));
			this.views.add('.eac-document-summary', new LineItemsView(this.options));
			this.views.add('.eac-document-summary', new ActionsView(this.options));
			this.views.add('.eac-document-summary', new TotalsView(this.options));
			$(document.body).trigger('eac_update_ui');
			return this;
		}
	});

	/**
	 * Initialize the invoice UI.
	 */
	var int = function () {
		if (!$(FORM_ID).length ){
			return;
		}

		const state = new State({
			...eac_invoice_form_vars.invoice || {},
		});

		state.set({
			items: new LineItemsCollection(null, {state}),
		});

		var formView = new Form({state});

		// Hydrate collections.
		var items = eac_invoice_form_vars.invoice.items || [];
		items.forEach(function (item) {
			state.get('items').add(item);
		});

		formView.render();
	};

	$(int);
});
