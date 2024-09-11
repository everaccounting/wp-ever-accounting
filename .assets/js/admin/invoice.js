import {LineItemsCollection, LineItemModel, ItemModel} from "@eac/store";

/* global Backbone, _, jQuery, eac_invoices_vars */
/**
 * ========================================================================
 * INVOICE FORM HANDLER
 * ========================================================================
 */
jQuery(document).ready(($) => {
	'use strict';
	const FORM_ID = '#eac-invoice-form';

	var utils = {
		calculateTaxes: function (amount, rates, inclusive = false) {
			const defaultData = {
				tax_id: 0,
				rate: 0,
				is_compound: 'no',
				amount: 0
			};

			rates = rates.filter(rate => {
				if (typeof rate === 'object' && rate !== null) {
					return !(rate.tax_id === undefined || rate.tax_id === null);

				}
				return false;
			}).map(rate => {
				return {...defaultData, ...rate};
			});

			if (inclusive) {
				let nonCompounded = amount;
				for (let rate of rates) {
					if (rate.is_compound !== 'yes') {
						continue;
					}
					const taxAmount = nonCompounded - (nonCompounded / (1 + (rate.rate / 100)));
					rate.amount = taxAmount;
					nonCompounded -= taxAmount;
				}

				const regularTaxRate = 1 + (rates.filter(rate => rate.is_compound === 'no')
					.reduce((sum, rate) => sum + rate.rate, 0) / 100);

				for (let rate of rates) {
					if (rate.is_compound === 'yes') {
						continue;
					}
					const theRate = rate.rate / 100 / regularTaxRate;
					const netPrice = amount - (theRate * nonCompounded);
					rate.amount += amount - netPrice;
				}
			} else {
				for (let rate of rates) {
					if (rate.is_compound === 'yes') {
						continue;
					}
					rate.amount = amount * (rate.rate / 100);
				}

				let preCompounded = rates.reduce((sum, rate) => sum + (rate.is_compound === 'no' ? rate.amount : 0), 0);
				for (let rate of rates) {
					if (rate.is_compound !== 'yes') {
						continue;
					}
					const taxAmount = (amount + preCompounded) * (rate.rate / 100);
					rate.amount += taxAmount;
					preCompounded += taxAmount;
				}
			}

			return rates.reduce((acc, rate) => {
				acc[rate.tax_id] = rate.amount;
				return acc;
			}, {});
		}
	}

	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */
	// var LineItemModel = wp.api.WPApiBaseModel.extend({
	// 	urlRoot: '/eac/v1/items',
	//
	// 	defaults: {
	// 		id: null,
	// 		type: 'standard',
	// 		name: '',
	// 		price: 0,
	// 		quantity: 1,
	// 		subtotal: 0,
	// 		subtotal_tax: 0,
	// 		discount: 0,
	// 		discount_tax: 0,
	// 		tax_total: 0,
	// 		total: 0,
	// 		taxable: false,
	// 		description: '',
	// 		unit: '',
	// 		item_id: null,
	// 		taxes: [],
	// 	},
	//
	// 	getDiscountedSubtotal() {
	// 		return this.getSubtotal() - this.getDiscount();
	// 	},
	//
	// 	getSubtotal() {
	// 		return this.get('quantity') * this.get('price');
	// 	},
	//
	// 	getTotal() {
	// 		return this.get('quantity') * this.get('price');
	// 	},
	//
	// 	add_tax: function (tax) {
	//
	// 	},
	// });

	var LineItemTaxModel = Backbone.Model.extend({
		defaults: {
			id: null,
			name: '',
			rate: 0,
			amount: 0,
			total: 0,
		},
	});

	var State = eac.store.InvoiceModel.extend({
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
			tax_enabled: 'yes',
			inclusive_taxes: 'no',
		},

		getSubtotal() {
			const {models: items} = this.get('items');

			return items.reduce(
				(amount, item) => {
					return amount += +item.getSubtotal();
				},
				0
			);
		},

		getTotal() {
			const {models: items} = this.get('items');

			return items.reduce(
				(amount, item) => {
					return amount += +item.getTotal();
				},
				0
			);
		},

		recalculate() {
			const {models: items} = this.get('items');

			// calculate line subtotals.
			const subtotal = items.reduce( (amount, item) => {
				var price = item.get('price');
				var quantity = item.get('quantity');
				var subtotal = price * quantity;
				var taxes = item.get('taxes').map( tax => tax.toJSON() );
				var subtotal_tax = utils.calculateTaxes(subtotal, taxes, this.get('inclusive_taxes') === 'yes').reduce((sum, tax) => sum + tax, 0);
				if (this.get('inclusive_taxes') === 'yes') {
					subtotal -= subtotal_tax;
				}
				subtotal = Math.max(0, subtotal);
				item.set('subtotal', subtotal);
				item.set('subtotal_tax', subtotal_tax);

			}, 0);
		}
	});

	/**
	 * ========================================================================
	 * COLLECTIONS
	 * ========================================================================
	 */

	// var LineItemsCollection = wp.api.WPApiBaseCollection.extend({
	// 	urlRoot: '/eac/v1/items',
	//
	// 	model: LineItemModel,
	//
	// 	// preinitialize: function (models, options) {
	// 	// 	this.options = options;
	// 	// },
	//
	// 	url: function () {
	// 		return '/eac/v1/items';
	// 	}
	// });

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

	var LineItemsView = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-summary__items',

		template: wp.template('eac-invoice-line-items'),

		initialize() {
			const {state} = this.options;

			const items = state.get('items');

			// Listen for events.
			this.listenTo(items, 'add', this.render);
			this.listenTo(items, 'remove', this.render);
			this.listenTo(items, 'change', this.render);
		},

		render() {
			console.log("rendering line items");
			const {state} = this.options;
			const items = state.get('items') || [];

			this.views.remove();

			if (0 === items.length) {
				this.views.add(new NoLineItemsView(this.options));
			} else {
				items.each((model) => this.views.add(new LineItemView({...this.options, model})));
			}
			$(document.body).trigger('eac_update_ui');
			return this;
		},

		add(model) {
			this.views.add(new LineItemView({
				...this.options,
				model,
			}));
		},

		remove(model) {
			let subview = null;
			console.log(this.views);
			this.views.each((view) => {
				if (view.model === model) {
					subview = view;
				}
			});
			if (null !== subview) {
				subview.remove();
			}
		},


	});

	var NoLineItemsView = wp.Backbone.View.extend({
		tagName: 'tr',

		template: wp.template('eac-invoice-no-line-items'),
	});

	var LineItemView = wp.Backbone.View.extend({
		tagName: 'tr',

		template: wp.template('eac-invoice-line-item'),

		events: {
			'change .line-quantity': 'onChangeQuantity',
			'change .line-price': 'onChangePrice',
			'change .line-description': 'onChangeDescription',
			'change .line-taxes': 'onChangeTaxes',
			'click .remove-line-item': 'onRemoveLineItem',
		},

		initialize() {
			// Listen for events.
			this.listenTo(this.model, 'change', this.render);
		},

		prepare() {
			const {model, options} = this;
			const {state} = options;
			const quantity = model.get('quantity');
			const price = model.get('price');
			const subtotal = quantity * price;

			//taxes.
			const taxes = model.get('taxes') || [];
			console.log(taxes);

			return {
				...model.toJSON(),
				taxes: taxes.map(tax => tax.toJSON()),
				subtotal,
				state: state.toJSON(),
			}
		},

		onChangeQuantity(e) {
			const quantity = parseFloat(e.target.value, 10);
			this.model.set('quantity', quantity);
			// if quantity is 0 or nan then remove the line item.
			if (quantity === 0 || isNaN(quantity)) {
				this.onRemoveLineItem(e);
			}
		},

		onChangePrice(e) {
			const price = parseFloat(e.target.value);
			this.model.set('price', price);
		},

		onChangeDescription(e) {
			const description = e.target.value;
			this.model.set('description', description);
		},

		onChangeTaxes(e) {
			e.preventDefault();
			var $select = $(e.target);
			var tax_ids = $select.val().filter((value, index, self) => {
				return self.indexOf(value) === index && !isNaN(value);
			}).map(value => parseInt(value, 10));

			// if empty we will remove all the taxes. otherwise we will add the selected taxes.
			if (tax_ids.length === 0) {
				this.model.set('taxes', []);
				return;
			}

			// ignore any tax that is already added.
			const taxes = this.model.get('taxes') || [];
			const new_tax_ids = tax_ids.filter(tax_id => taxes.findIndex(tax => tax.id === tax_id) === -1);

			// bail if no new taxes are found.
			if (new_tax_ids.length === 0) {
				return;
			}

			// fetch the taxes from the server.
			wp.apiRequest({
				path: '/eac/v1/taxes',
				method: 'GET',
				data: {
					include: new_tax_ids.join(',')
				},
			}).done((response) => {
				const new_taxes = response.map(tax => {
					return new LineItemTaxModel({
						id: tax.id,
						name: tax.name,
						rate: tax.rate,
						amount: 0,
						total: 0,
					});
				});
				this.model.set('taxes', [...taxes, ...new_taxes]);
			});
		},

		onRemoveLineItem(e) {
			e.preventDefault();
			const {model, options} = this;
			const {state} = options;

			// Remove OrderItem.
			state.get('items').remove(model);
		}
	});

	var ActionsView = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-summary__actions',

		template: wp.template('eac-invoice-actions'),

		events: {
			'change .add-line-item': 'onAddLineItem',
		},

		initialize() {
			const {state} = this.options;
			console.log(state);
			this.listenTo(state.get('items'), 'add', this.stopSpinner);
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
			const item = new ItemModel({id:item_id});
			item.fetch();


			// this.startSpinner();
			// wp.apiRequest({
			// 	path: '/eac/v1/items/' + item_id,
			// 	method: 'GET',
			// }).done(function (response) {
			// 	const model = new LineItemModel({
			// 		...response,
			// 		id: _.uniqueId('item_'),
			// 		item_id: response?.id,
			// 		description: response.description.length > 160 ? response.description.substring(0, 160) : response.description,
			// 		subtotal: response.price
			// 	});
			//
			// 	// if response has taxes then we will add them to the model.
			// 	if (response.taxes) {
			// 		const taxes = response.taxes.map(tax => {
			// 			return new LineItemTaxModel({
			// 				id: tax.id,
			// 				name: tax.name,
			// 				rate: tax.rate,
			// 				amount: 0,
			// 				total: 0,
			// 			});
			// 		});
			// 		model.set('taxes', taxes);
			// 	}
			//
			// 	items.add(model);
			// });
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

		initialize() {
			const {state} = this.options;
			const items = state.get('items');
			this.listenTo(items, 'add', this.render);
			this.listenTo(items, 'remove', this.render);
			this.listenTo(items, 'change', this.render);
		},

		prepare() {
			console.log("TotalsView prepare");
			const {state} = this.options;
			// const subtotal = state.getSubtotal();
			// const total = state.getTotal();

			return {
				...state.toJSON(),
				// subtotal,
				// total,
			};
		}
	});

	var Form = wp.Backbone.View.extend({
		el: FORM_ID,

		render() {
			// this.views.add('.eac-document-summary', new NoLineItemsView(this.options));
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
		if (!$(FORM_ID).length) {
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
