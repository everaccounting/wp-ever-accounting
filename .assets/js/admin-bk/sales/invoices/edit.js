jQuery(function ($) {
	'use strict';
	const FORM_ID = '#eac-invoice-form';
	const debug = true;

	/**
	 * A debugging utility. Works only when a
	 * debug flag is on and the browser supports it.
	 */
	const log = function () {
		if (window.console && debug) {
			console.log('=== log() ===');
			window.console.log.apply(window.console, arguments);
		}
	}

	if (!$(FORM_ID).length || undefined === window.eac_invoice_edit_vars) {
		return;
	}

	var BillingAddress = wp.Backbone.View.extend({
		el: '.billing-address',

		template: wp.template('eac-invoice-billing-address'),

		initialize() {
			const {state} = this.options;
			this.listenTo(state, 'change:contact_id', this.render);
			this.listenTo(state, 'address', this.render);
		},

		prepare() {
			const {state} = this.options;

			return {
				...state.get('address'),
			}
		},
	});

	var Items = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-items__items',

		initialize() {
			const {state} = this.options;
			const items = state.get('items');
			this.listenTo(items, 'add', this.render);
			this.listenTo(items, 'remove', this.render);
			this.listenTo(items, 'add', this.scrollToBottom);
		},

		render() {
			console.log('=== SummaryItems.render() ===');
			this.views.detach();
			const {state} = this.options;
			const items = state.get('items');
			items.each(model => {
				this.views.add(new Item({...this.options, model}));
			});
			// if no items, add a blank row.
			if (0 === items.length) {
				this.views.add(new NoItems(this.options));
			}
			$(document.body).trigger('eac_update_ui');
			return this;
		},

		scrollToBottom() {
			var $el = this.$el.closest('tbody').find('tr:last-child');
			$el.find('.item-price').focus();
			// Now we need to scroll to the bottom of the table.
			var $table = this.$el.closest('table');
			$('html, body').animate({
				scrollTop: $el.offset().top - $table.offset().top + $table.scrollTop()
			}, 500);
		}
	});

	var Item = wp.Backbone.View.extend({
		tagName: 'tr',

		className: 'eac-document-items__item',

		template: wp.template('eac-invoice-item'),

		events: {
			'change .item-quantity': 'onQuantityChange',
			'change .item-price': 'onPriceChange',
			'select2:select .item-taxes': 'onAddTax',
			'select2:unselect .item-taxes': 'onRemoveTax',
			'click .remove-item': 'onRemoveLineItem',
		},

		initialize() {
			this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model.get('taxes'), 'add remove change', this.render);
		},

		prepare() {
			const {model} = this.options;
			return {
				...model.toJSON(),
				tax: model.get('taxes').reduce((acc, tax) => acc + tax.get('amount'), 0),
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
			var value = parseFloat(e.target.value, 10);
			if( ! value ){
				this.onRemoveLineItem(e);
				return;
			}
			this.model.set('quantity', value);
			this.options.state.updateAmounts();
		},

		onPriceChange(e) {
			e.preventDefault();
			var value = parseFloat(e.target.value, 10);
			this.model.set('price', value);
			this.options.state.updateAmounts();
		},

		onAddTax(e) {
			e.preventDefault();
			var data = e.params.data;
			var tax_id = parseInt(data.id, 10) || null;
			if (!tax_id) {
				return;
			}
			var tax = new eac.api.Tax({id: tax_id});
			tax.fetch({
				success: (model) => {
					this.model.get('taxes').add({
						...model.toJSON(),
						tax_id: model.get('id'),
						id: _.uniqueId('tax_'),
					});
					this.options.state.updateAmounts();
				}
			});
		},

		onRemoveTax(e) {
			log('=== onRemoveTax() ===');
			e.preventDefault();
			var data = e.params.data;
			var tax_id = parseInt(data.id, 10) || null;
			if (tax_id) {
				var tax = this.model.get('taxes').findWhere({tax_id: tax_id});
				if (tax){
					this.model.get('taxes').remove(tax);
					this.options.state.updateAmounts();
				}
			}
		},

		onRemoveLineItem(e) {
			e.preventDefault();
			this.options.state.get('items').remove(this.model);
			this.options.state.updateAmounts();
		}
	});

	var NoItems = wp.Backbone.View.extend({
		tagName: 'tr',

		className: 'eac-document-items__no-items',

		template: wp.template('eac-invoice-no-items'),
	});

	var Actions = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-items__actions',

		template: wp.template('eac-invoice-items-actions'),

		events: {
			'select2:select .add-item': 'onAddItem',
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

		onAddItem(e) {
			e.preventDefault();
			const {state} = this.options;
			const item_id = parseInt(e.params.data.id, 10) || null;
			if (!item_id) {
				return;
			}
			$(e.target).val(null).trigger('change');
			const item = new eac.api.Item({id: item_id});
			item.fetch({
				success: (model) => {
					const id = _.uniqueId('item_');
					state.get('items').add({
						...model.toJSON(),
						quantity: 1,
						subtotal: 1 * model.get('price'),
						tax: 0,
						discount: 0,
						total: 1 * model.get('price'),
						item_id: model.get('id'),
						id: id,
						// convert taxes to a collection.
						taxes: new eac.api.DocumentTaxes(
							model.get('taxes').map((tax) => ({
								...tax,
								id: _.uniqueId('tax_'),
								tax_id: tax.id,
								item_id: id,
							})),
						),
					});
					state.updateAmounts();
				}
			});
		},
	});

	var Totals = wp.Backbone.View.extend({
		tagName: 'tfoot',

		className: 'eac-document-items__totals',

		template: wp.template('eac-invoice-items-totals'),

		initialize() {
			const {state} = this.options;
			this.listenTo(state, 'change', this.render);
		},

		prepare() {
			const {state} = this.options;
			return {
				...state.toJSON(),
				itemized_taxes: state.getItemizedTaxes(),
			}
		},
	});

	var Invoice = wp.Backbone.View.extend({
		el: FORM_ID,

		events: {
			'change [name="contact_id"]': 'onContactChange',
			'change [name="currency_code"]': 'onCurrencyCodeChange',
			'change [name="vat_exempt"]': 'onVatExemptChange',
			'change [name="discount_amount"]': 'onDiscountAmountChange',
			'change [name="discount_type"]': 'onDiscountTypeChange',
		},

		initialize() {
			const {state} = this.options;
		},

		render() {
			this.views.detach();
			this.views.add('.billing-address', new BillingAddress(this.options));
			this.views.add('table.eac-document-items', new Items(this.options));
			this.views.add('table.eac-document-items', new Actions(this.options));
			this.views.add('table.eac-document-items', new Totals(this.options));
			$(document.body).trigger('eac_update_ui');
			return this;
		},

		onContactChange(e) {
			e.preventDefault();
			var state = this.options.state;
			var contact_id = e.target.value || null;
			if (contact_id === this.options.state.get('customer_id') || !contact_id) {
				state.set('address', {});
				return;
			}

			new eac.api.Customer({id: contact_id}).fetch({
				success: (model) => {
					state.set('address', model.toJSON());
					state.set('contact_id', model.get('id'));
				}
			});

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
			var value = e.target.value === 'yes';
			state.set('vat_exempt', value);
			// If vat_exempt is true, remove all taxes.
			if (value) {
				state.get('items').each((item) => {
					item.get('taxes').reset();
				});
				state.updateAmounts();
			}
		},

		onDiscountAmountChange(e) {
			log('=== onDiscountAmountChange() ===');
			e.preventDefault();
			var state = this.options.state;
			var value = parseFloat(e.target.value, 10);
			// if type is percent and amount is greater than 100, set to 100.
			if (state.get('discount_type') === 'percent' && value > 100) {
				value = 100;
				this.$('[name="discount_amount"]').val(100);
			}

			state.set('discount_amount', value);
		},

		onDiscountTypeChange(e) {
			log('=== onDiscountTypeChange() ===');
			e.preventDefault();
			var state = this.options.state;
			var value = e.target.value;

			//if type is percent and amount is greater than 100, set to 100.
			if (value === 'percent' && state.get('discount_amount') > 100) {
				state.set('discount_amount', 100);
				this.$('[name="discount_amount"]').val(100);
			}
			state.set('discount_type', value);
		},

	});

	/**
	 * Set up the invoice UI.
	 */
	var int = function () {
		const state = new eac.api.Invoice({
			...window.eac_invoice_edit_vars?.invoice || {},
			settings: window.eac_invoice_edit_vars?.settings || {},
		});

		// Hydrate collections.
		var items = eac_invoice_edit_vars?.invoice?.items || [];
		items.forEach(function (item) {
			state.get('items').add(item);
		});


		(new Invoice({state})).render();
	};

	// Initialize the invoice UI.
	$(int);
});