/* global Backbone, _, jQuery, eac_invoices_vars, eac */
/**
 * ========================================================================
 * INVOICE FORM HANDLER
 * ========================================================================
 */
jQuery(document).ready(($) => {
	'use strict';
	const FORM_ID = '#eac-invoice-form';

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
			const {state} = this.options;
			const items = state.get('items') || [];
			this.views.detach();
			if (0 === items.length) {
				this.views.add(new NoLineItemsView(this.options));
			} else {
				items.each((model) => this.views.add(new LineItemView({...this.options, model})));
			}
			$(document.body).trigger('eac_update_ui');
			return this;
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
			'select2:select .line-taxes': 'onAddTax',
			'select2:unselect .line-taxes': 'onRemoveTax',
			'click .remove-line-item': 'onRemoveLineItem',
		},

		initialize() {
			// Listen for events.
			this.listenTo(this.model.get('taxes'), 'change', this.onChaneTaxes);
			this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model.get('taxes'), 'change', this.render);
		},

		prepare() {
			const {model, options} = this;
			const {state} = options;
			const quantity = model.get('quantity');
			const price = model.get('price');
			const subtotal = quantity * price;

			//taxes.
			const taxes = model.get('taxes') || [];

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
			const price = parseFloat(e.target.value) || 0;
			this.model.set('price', price);
		},

		onChangeDescription(e) {
			const description = e.target.value;
			this.model.set('description', description);
		},

		onAddTax(e) {
			const self = this;
			var data = e.params.data;
			var tax_id = parseInt(data.id, 10);
			// bail if the tax_id is not found.
			if (isNaN(tax_id)) {
				return;
			}
			var tax = new eac.api.models.Tax({id: tax_id});
			var lineTaxes = self.model.get('taxes');
			tax.fetch({
				success: function (model) {
					// todo check if the tax is already added.

					lineTaxes.add(new eac.api.models.LineTax({
						...model.toJSON(),
						id: _.uniqueId('tax_'),
					}), {merge: true, silent: true});
				}
			});

			lineTaxes.trigger('change');
		},

		onRemoveTax(e) {
			e.preventDefault();
			var data = e.params.data;
			var tax_id = parseInt(data.id, 10);
			// bail if the tax_id is not found.
			if (isNaN(tax_id)) {
				return;
			}
			var lineTaxes = this.model.get('taxes');
			var tax = lineTaxes.findWhere({tax_id});
			lineTaxes.remove(tax);
		},

		onRemoveLineItem(e) {
			e.preventDefault();
			const {model, options} = this;
			const {state} = options;

			// Remove OrderItem.
			state.get('items').remove(model);
		},

		onChaneTaxes() {
			const {state} = this.options;
			state.get('items').updateAmounts();
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
			const item = new eac.api.models.Item({id: item_id});
			this.startSpinner();
			item.fetch({
				success: function (model) {
					model.getTaxes().done(function (taxes) {
						const lineTaxes = new eac.api.collections.LineTaxes(null, {state});
						_.each(taxes, function (tax) {
							lineTaxes.add(new eac.api.models.LineTax({
								...tax,
								id: _.uniqueId('tax_'),
							}));
						});

						const lineItem = new eac.api.models.LineItem({
							id: _.uniqueId('item_'),
							item_id: item_id,
							name: model.get('name'),
							price: model.get('price'),
							subtotal: model.get('price') * 1,
							total: model.get('price') * 1,
							description: model.get('description').length > 160 ? model.get('description').substring(0, 160) : model.get('description'),
							quantity: 1,
							taxes: lineTaxes,
						});

						// todo improve this.
						const existingItem = items.findWhere(lineItem);
						if (existingItem) {
							existingItem.set('quantity', existingItem.get('quantity') + 1);
						} else {
							items.add(lineItem);
						}

						items.updateAmounts();
					});
				},
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

		initialize() {
			const {state} = this.options;
			const items = state.get('items');
			this.listenTo(items, 'add', this.render);
			this.listenTo(items, 'remove', this.render);
			this.listenTo(items, 'change', this.render);
		},

		prepare() {
			const {state} = this.options;
			console.log(state.toJSON());
			return {
				...state.toJSON(),
			};
		}
	});

	var Form = wp.Backbone.View.extend({
		el: FORM_ID,

		initialize(options) {
			this.options = options;
			this.listenTo(this.options.state.get('items'), 'change', this.updateAmounts);
			this.listenTo(this.options.state, 'change:discount_amount', this.updateAmounts);
			this.listenTo(this.options.state, 'change:discount_type', this.updateAmounts);
			this.listenTo(this.options.state, 'change:vat_exempt', this.updateAmounts);
			this.listenTo(this.options.state, 'change:currency_code', this.updateAmounts);
		},

		events: {
			'change [name="discount_amount"]': 'onChangeDiscountAmount',
			'change [name="discount_type"]': 'onChangeDiscountType',
		},

		render() {
			this.views.detach();
			this.views.add('.eac-document-summary', new LineItemsView(this.options));
			this.views.add('.eac-document-summary', new ActionsView(this.options));
			this.views.add('.eac-document-summary', new TotalsView(this.options));
			$(document.body).trigger('eac_update_ui');
			return this;
		},

		onChangeDiscountAmount(e) {
			const {state} = this.options;
			const amount = parseFloat(e.target.value, 10);
			state.set('discount_amount', amount);
		},

		onChangeDiscountType(e) {
			const {state} = this.options;
			const type = e.target.value;
			state.set('discount_type', type);
		},

		updateAmounts() {
			console.log('updateAmounts');
			const {state} = this.options;
			const items = state.get('items');
			items.updateAmounts();
		}
	});

	/**
	 * Initialize the invoice UI.
	 */
	var int = function () {
		if (!$(FORM_ID).length) {
			return;
		}

		const state = new eac.api.models.Invoice({
			...eac_invoice_form_vars.invoice || {},
			settings: eac_invoice_form_vars.settings || {},
		});

		state.set({
			items: new eac.api.collections.LineItems(null, {state}),
		});

		window.invoiceState = state;

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
