jQuery( function($){
	'use strict';
	const FORM_ID = '#eac-bill-form';

	var BillingAddress = wp.Backbone.View.extend({
		el: '.vendor-address',

		template: wp.template('eac-bill-vendor-address'),

		initialize() {
			const {state} = this.options;
			this.listenTo(state, 'change:contact_id', this.render);
		},

		prepare: function () {
			const {state} = this.options;

			return {
				...wp.Backbone.View.prototype.prepare.apply(this, arguments),
				...state.toJSON(),
			}
		},
	});


	var SummaryHead = wp.Backbone.View.extend({
		tagName: 'thead',

		className: 'eac-document-summary__head',

		template: wp.template('eac-bill-summary-head'),
	});

	var SummaryItem = wp.Backbone.View.extend({
		tagName: 'tr',

		className: 'eac-document-summary__item',

		template: wp.template('eac-bill-summary-item'),

		events: {
			'change .line-quantity': 'onQuantityChange',
			'change .line-price': 'onPriceChange',
			'select2:select .line-taxes': 'onAddTax',
			'select2:unselect .line-taxes': 'onRemoveTax',
			'click .remove-line-item': 'onRemoveLineItem',
		},

		initialize() {
			console.log('=== SummaryItem.initialize() ===');
			this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model, 'change', this.render);
			wp.Backbone.View.prototype.initialize.apply(this, arguments);
		},

		prepare() {
			const { model} = this.options;
			return {
				...model.toJSON(),
				taxes: model.get('taxes').toJSON(),
			}
		},

		render() {
			console.log('=== SummaryItem.render() ===');
			wp.Backbone.View.prototype.render.apply(this, arguments);
			$(document.body).trigger('eac_update_ui');
			return this;
		},

		onQuantityChange(e) {
			e.preventDefault();
			const {model} = this.options;
			const value = e.target.value;
			model.set('quantity', value);
			const {state} = this.options;
			state.updateAmounts();
		},

		onPriceChange(e) {
			e.preventDefault();
			const value = e.target.value;
			this.model.set('price', value);
			const {state} = this.options;
			state.updateAmounts();
		},

		onAddTax(e) {
			e.preventDefault();
			const {model} = this.options;
			var data = e.params.data;
			var tax_id = parseInt(data.id, 10);
			// bail if the tax_id is not found.
			if (isNaN(tax_id)) {
				return;
			}
			if (!model.get('taxes').findWhere({tax_id: tax_id})) {
				(new eac.api.models.Tax({id: tax_id})).fetch({
					success: function (tax) {
						model.get('taxes').add(new eac.api.models.LineTax({
							...tax.toJSON(),
							tax_id: tax.id,
							id: _.uniqueId('tax_'),
						}));
					}
				})
			}
		},

		onRemoveLineItem(e) {
			e.preventDefault();
			const {state, model} = this.options;
			state.get('items').remove(model);
			state.updateAmounts();
		},
	});

	var SummaryEmptyItem = wp.Backbone.View.extend({
		tagName: 'tr',

		className: 'eac-document-summary__item is--empty',

		template: wp.template('eac-bill-summary-item-empty'),
	});

	var SummaryItems = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-summary__items',

		initialize() {
			const {state} = this.options;
			const items = state.get('items');
			this.listenTo(items, 'add', this.render);
			this.listenTo(items, 'remove', this.render);
			this.listenTo(items, 'add', this.scrollToBottom);
		},

		render(){
			console.log('=== SummaryItems.render() ===');
			this.views.detach();
			const {state} = this.options;
			const items = state.get('items');
			items.each( model => {
				this.views.add(new SummaryItem({...this.options, model}));
			});
			// if no items, add a blank row.
			if ( 0 === items.length ) {
				this.views.add(new SummaryEmptyItem(this.options));
			}
			$(document.body).trigger('eac_update_ui');
			return this;
		},

		scrollToBottom() {
			console.log('=== SummaryItems.scrollToBottom() ===');
			// scroll to the bottom of the table when a new item is added. then focus on the new item.
			// $('html, body').animate({
			// 	scrollTop: $(document).height()
			// }, 500);
			var $el = this.$el.closest('tbody').find('tr:last-child');
			$el.find('.line-price').focus();
			// Now we need to scroll to the bottom of the table.
			var $table = this.$el.closest('table');
			$('html, body').animate({
				scrollTop: $el.offset().top - $table.offset().top + $table.scrollTop()
			}, 500);
		}
	});

	var SummaryTotals = wp.Backbone.View.extend({
		tagName: 'tfoot',

		className: 'eac-document-summary__totals',

		template: wp.template('eac-bill-summary-totals'),

		initialize() {
			console.log('=== SummaryTotals.initialize() ===');
			const {state} = this.options;
			this.listenTo(state, 'change:subtotal', this.render);
		},

		prepare() {
			const {state} = this.options;
			return {
				...state.toJSON(),
			}
		}
	});

	var SummaryActions = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-summary__actions',

		template: wp.template('eac-bill-summary-actions'),

		events: {
			'select2:select .add-line-item': 'onAddLineItem',
		},

		initialize() {
			const {state} = this.options;
			this.listenTo(state, 'change:is_fetching', this.render);
		},

		prepare() {
			const {state} = this.options;
			return {
				...state.toJSON(),
			}
		},

		render() {
			console.log('=== SummaryActions.render() ===');
			this.views.detach();
			wp.Backbone.View.prototype.render.apply(this, arguments);
			$(document.body).trigger('eac_update_ui');
		},

		onAddLineItem(e) {
			e.preventDefault();
			const {state} = this.options;
			const item_id = e.target.value;
			state.addItem(item_id);
		}
	});

	var Summary = wp.Backbone.View.extend({
		tagName: 'table',

		className: 'eac-document-summary',

		render() {
			console.log('=== Summary.render() ===');
			this.views.detach();
			this.views.add(new SummaryHead(this.options));
			this.views.add(new SummaryItems(this.options));
			this.views.add(new SummaryActions(this.options));
			this.views.add(new SummaryTotals(this.options));
			return this;
		}
	});

	var Form = wp.Backbone.View.extend({
		el: FORM_ID,

		events: {
			'change [name="currency_code"]': 'onCurrencyCodeChange',
			'change [name="vat_exempt"]': 'onVatExemptChange',
			'change [name="discount_amount"]': 'onDiscountAmountChange',
			'change [name="discount_type"]': 'onDiscountTypeChange',
		},

		initialize() {
			const {state} = this.options;
			this.listenTo(state, 'change:currency_code', this.onCurrencyUpdate);
			//this.listenTo(state.get('items'), 'add remove change', this.updateAmounts);
		},

		render() {
			console.log('=== Form.render() ===');
			this.views.detach();
			this.views.add('.vendor-address', new BillingAddress(this.options));
			this.views.add('.document-summary', new Summary(this.options));
			$(document.body).trigger('eac_update_ui');
		},

		onCurrencyCodeChange(e) {
			e.preventDefault();
			var state = this.options.state;
			var value = e.target.value;
			// bail if the value is the same or empty.
			if (value === state.get('currency_code') || !value) {
				return;
			}

			state.set('currency_code', value);
		},

		onVatExemptChange(e) {
			e.preventDefault();
			var state = this.options.state;
			var value = e.target.value === 'yes' ? 'yes' : 'no';
			state.set('vat_exempt', value);
		},

		onDiscountAmountChange(e) {
			e.preventDefault();
			var state = this.options.state;
			var value = parseFloat(e.target.value, 10);
			state.set('discount_amount', value);
			state.updateAmounts();
		},

		onDiscountTypeChange(e) {
			e.preventDefault();
			var state = this.options.state;
			var value = e.target.value;
			state.set('discount_type', value);
			state.updateAmounts();
		},

		onCurrencyUpdate() {
			console.log('=== Form.onCurrencyUpdate() ===');
			const state = this.options.state;
			state.set('billing_name', 'Jane Doe');
			state.set('contact_id', 2);

		},
		UpdateAmounts() {
			console.log('=== Form.updateAmount() ===');
			const state = this.options.state;
			state.updateAmounts();
		}
	});

	/**
	 * Initialize the invoice UI.
	 */
	var int = function () {
		if (!$(FORM_ID).length) {
			return;
		}

		const state = new eac.api.models.Bill({
			billing_name: 'John Doe',
			billing_address: '123 Main St',
			billing_city: 'Anytown',
			billing_state: 'CA',
			billing_postcode: '12345',
			billing_country: 'US',
			billing_phone: '123-456-7890',
			billing_email: 'john@doe.com',
			billing_vat: '123456789',
			money: window.accounting,
			...eac_bill_form_vars || {},
			settings: eac_bill_form_vars?.settings || {},
		});

		state.set({
			items: new eac.api.collections.LineItems(null, {state}),
		});

		var form = new Form({state});

		// Hydrate collections.
		var items = eac_bill_form_vars?.bill?.items || [];
		items.forEach(function (item) {
			state.get('items').add(item);
		});

		form.render();
	}

	int();
});
