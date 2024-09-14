/* global Backbone, _, jQuery, eac_invoices_vars */

jQuery(document).ready(($) => {
	'use strict';


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

	var LineTaxModel = Backbone.Model.extend({
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

	var LineTaxesCollection = Backbone.Collection.extend({
		model: LineTaxModel,
	});


	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */
	var AddLineItemView = wp.Backbone.View.extend({});

	var LineItemView = wp.Backbone.View.extend({});

	var LineItemsView = wp.Backbone.View.extend({});

	var LineTaxView = wp.Backbone.View.extend({});

	var LineTaxesView = wp.Backbone.View.extend({});

	var AddLineItemModal = $.eacmodal.View.extend({

		template: wp.template('eac-modal-add-line-item'),

		events: {
			'change .select-line-item': 'onChangeLineItem',
			'change .add-line-item-quantity': 'onChangeQuantity',
			'change .add-line-item-price': 'onChangePrice',
			'change .add-line-item-tax': 'onChangeTax',
			'submit form': 'onSubmit',
		},

		initialize: function () {
			const { state } = this.options;
			const id = _.uniqueId('new-');

			this.model = new LineItemModel({
				id,
				state,
				quantity: 1,
				price: 0,
				taxes:[
					{
						id: 1,
						name: 'VAT',
						rate: 20,
						amount: 0,
						total: 0,
					},
					{
						id: 2,
						name: 'VAT',
						rate: 20,
						amount: 0,
						total: 0,
					},
					{
						id: 3,
						name: 'VAT',
						rate: 20,
						amount: 0,
						total: 0,
					},
				]
			});
			// $.eacmodal.View.prototype.initialize.apply(this, arguments);
		},

		onChangeQuantity: function (e) {
			this.model.set('quantity', parseFloat(e.target.value));
		},

		onSubmit: function (e) {
			e.preventDefault();
			this.options.state.get('items').add(this.model);
			this.close();
		}

	});

	var Form = wp.Backbone.View.extend({
		el: '#eac-invoice-form',


		events: {
			'click .add-line-item': 'onClickAddLineItem',
			'click .add-taxes': 'onClickAddTaxes',
		},

		initialize: function () {
			const {state} = this.options;
			const items = state.get('items');
			this.listenTo(items, 'add remove change', function () {
				console.log('Items changed');
			});
		},

		onClickAddLineItem: function (e) {
			e.preventDefault();
			new AddLineItemModal(this.options).render().open();
		},

		onClickAddTaxes: function (e) {
			e.preventDefault();
			var self = this;
			const {state} = this.options;
			const items = state.get('items');
			var model = new LineItemModel({
				id: _.uniqueId('new-'),
				item_id: null,
				quantity: 1,
				price: 0,
				state: this.options.state,
				taxes:[
					{
						id: 1,
						name: 'VAT',
						rate: 20,
						amount: 0,
						total: 0,
					},
					{
						id: 2,
						name: 'VAT',
						rate: 20,
						amount: 0,
						total: 0,
					},
					{
						id: 3,
						name: 'VAT',
						rate: 20,
						amount: 0,
						total: 0,
					},
				]
			});
			this.$el.eacmodal({
				template: 'eac-modal-add-line-item',
				model: model.toJSON(),
				events: {
					'change .select-line-item': function (e, that) {
						// console.log(e);
						// console.log(that);
						model.set('item_id', parseInt(e.target.value));
						state.set('item_id', parseInt(e.target.value));
						// e.$el.find('.add-line-item-price').val(100);
					},
					'change .add-line-item-quantity': function (e) {
						model.set('quantity', parseFloat(e.target.value));
					},
					'change .add-line-item-price': function (e) {
						model.set('price', parseFloat(e.target.value));
					},
					'submit form': function (e) {
						e.preventDefault();
						// items.add(model);
						self.$el.eacmodal('close');
					},
				},
			})
		},
	});


	/**
	 * Initialize the invoice UI.
	 */
	var int = function () {
		var state = new State({
			...eac_invoice_vars?.invoice || {},
		});
		state.set({items: new LineItemsCollection(null, {state})});

		var form = new Form({state});
		form.render();
	}

	$(int);
})
