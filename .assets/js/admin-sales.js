const {Money} = eac.money;
import apiFetch from '@wordpress/api-fetch';

(function (document, wp, $) {
	'use strict';
	/**
	 * ========================================================================
	 * PAYMENT FORM
	 * ========================================================================
	 */

	$('#eac-payment-form').eac_form({
		events: {
			'ready': 'handleExchangeRate',
			'change :input[name="account_id"]': 'handleExchangeRate',
		},
		handleExchangeRate: function () {
			var self = this;
			var $amount = this.$(':input[name="amount"]');
			var $exchange_rate = this.$(':input[name="exchange_rate"]');
			var account_id = this.$(':input[name="account_id"]').val();

			if (!account_id) {
				$exchange_rate.attr('readonly', true).val(1.00);
				$exchange_rate.next('.eac-form-field__addon').text(eac_admin_vars.base_currency);
				return;
			}

			self.block();
			var Account = new eac.api.Account({id: account_id});
			Account.fetch({
				success: function (account) {
					var new_currency = account.get('currency') || eac_admin_vars.base_currency;
					$amount.data('currency', new_currency).removeClass('enhanced');
					$exchange_rate.val(eac_admin_vars.currencies[new_currency].rate || 1.00);
					$exchange_rate.next('.eac-form-field__addon').text(new_currency);
					$exchange_rate.attr('readonly', new_currency === eac_admin_vars.base_currency);
					$(document.body).trigger('eac_update_ui');
				}
			}).then(function () {
				self.unblock();
			});
		},
	});


	/**
	 * ========================================================================
	 * INVOICE FORM
	 * ========================================================================
	 */

	/**
	 * Invoice state model.
	 *
	 * @type {Backbone.Model}
	 * @since 1.0.0
	 */
	var InvoiceState = eac.api.Invoice.extend({
		defaults: Object.assign({}, eac.api.Invoice.prototype.defaults, {
			isFetching: false,
		})
	});

	/**
	 * Invoice editor view.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	var Invoice = wp.Backbone.View.extend({
		el: '#eac-invoice-form',

		events: {
			'change [name="contact_id"]': 'onContactChange',
			'change [name="currency_code"]': 'onCurrencyCodeChange',
			'change [name="discount_value"]': 'onDiscountValueChange',
			'change [name="discount_type"]': 'onDiscountTypeChange',
		},

		render() {
			this.views.detach();
			this.views.add('.billing-address', new Invoice.BillingAddress(this.options));
			this.views.add('table.eac-document-items', new Invoice.Items(this.options));
			this.views.add('table.eac-document-items', new Invoice.Toolbar(this.options));
			this.views.add('table.eac-document-items', new Invoice.Totals(this.options));

			$(document.body).trigger('eac_update_ui');
			return this;
		},

		onContactChange(e) {
			e.preventDefault();
			var state = this.options.state;
			var contact_id = e.target.value || null;
			if (contact_id === this.options.state.get('customer_id') || !contact_id) {
				state.set({
					contact_id: null,
					contact_name: '',
					contact_email: '',
					contact_phone: '',
					contact_address: '',
					contact_city: '',
					contact_state: '',
					contact_zip: '',
					contact_country: '',
					contact_tax_number: '',
				});
			}

			new eac.api.Customer({id: contact_id}).fetch({
				success: (model) => {
					state.set('contact_id', model.get('id'));
					state.set({
						contact_name: model.get('name'),
						contact_email: model.get('email'),
						contact_phone: model.get('phone'),
						contact_address: model.get('address'),
						contact_city: model.get('city'),
						contact_state: model.get('state'),
						contact_zip: model.get('zip'),
						contact_country: model.get('country'),
						contact_tax_number: model.get('tax_number'),
					});
				}
			});
		},

		onCurrencyCodeChange(e) {
			e.preventDefault();
			var code = e.target.value;
			var currency = eac_admin_vars.currencies[code] || eac_admin_vars.base_currency;
			this.options.state.set('money', new Money(currency));
			this.options.state.set('currency_code', code);
		},

		onDiscountValueChange(e) {
			e.preventDefault();
			var state = this.options.state;
			var value = parseFloat(e.target.value, 10);
			state.set('discount_value', value);
			state.updateAmounts();
		},

		onDiscountTypeChange(e) {
			var state = this.options.state;
			var value = e.target.value;
			state.set('discount_type', value);
			state.updateAmounts();
		},
	});

	/**
	 * Invoice billing address view.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	Invoice.BillingAddress = wp.Backbone.View.extend({
		el: '.billing-address',

		template: wp.template('eac-invoice-billing-address'),

		initialize() {
			const {state} = this.options;
			this.listenTo(state, 'change:contact_id', this.render);
		},

		prepare() {
			const {state} = this.options;

			return {
				...state.toJSON(),
			}
		},
	});

	/**
	 * Invoice Items View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	Invoice.Items = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-items__items',

		initialize() {
			const {state} = this.options;
			console.log(state);
			this.listenTo(state.get('items'), 'add', this.render);
			this.listenTo(state.get('items'), 'remove', this.render);
			this.listenTo(state.get('items'), 'add', this.scrollToBottom);
		},

		render() {
			this.views.detach();
			const {state} = this.options;
			const items = state.get('items');
			if (!items.length) {
				this.views.add(new Invoice.NoItems(this.options));
			} else {
				items.each((model) => {
					this.views.add(new Invoice.Item({...this.options, model}));
				});
			}
			$(document.body).trigger('eac_update_ui');
			return this
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

	/**
	 * Invoice Items No Items View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	Invoice.NoItems = wp.Backbone.View.extend({
		tagName: 'tr',

		className: 'eac-document-items__no-items',

		template: wp.template('eac-invoice-no-items'),
	});

	/**
	 * Invoice Items Item View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	Invoice.Item = wp.Backbone.View.extend({
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
			const {model, state} = this.options;
			console.log(state);
			return {
				...model.toJSON(),
				formatted_subtotal: state.get('money').format(model.get('subtotal')),
				formatted_tax: state.get('money').format(model.get('tax')),
				tax: model.get('taxes').reduce((acc, tax) => acc + tax.get('amount'), 0),
				taxes: model.get('taxes').toJSON(),
			}
		},

		render() {
			wp.Backbone.View.prototype.render.apply(this, arguments);
			$(document.body).trigger('eac_update_ui');
			return this;
		},

		onQuantityChange(e) {
			e.preventDefault();
			var value = parseFloat(e.target.value, 10);
			if (!value) {
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

			// any of the taxes already exists with the same tax_id then skip.
			if (this.model.get('taxes').findWhere({tax_id})) {
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
			e.preventDefault();
			var data = e.params.data;
			var tax_id = parseInt(data.id, 10) || null;
			if (tax_id) {
				var tax = this.model.get('taxes').findWhere({tax_id: tax_id});
				if (tax) {
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

	/**
	 * Invoice toolbar view.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	Invoice.Toolbar = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-items__toolbar',

		template: wp.template('eac-invoice-toolbar'),

		events: {
			'select2:select .add-item': 'onAddItem',
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
						item_id: model.get('id'),
						id: id,
						// convert taxes to a collection.
						taxes: new eac.api.DocumentTaxes(
							model.get('taxes').map((tax) => ({
								...tax,
								price: model.get('cost') || model.get('price'),
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

	/**
	 * Invoice Totals View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	Invoice.Totals = wp.Backbone.View.extend({
		tagName: 'tfoot',

		className: 'eac-document-items__totals',

		template: wp.template('eac-invoice-totals'),

		initialize() {
			const {state} = this.options;
			this.listenTo(state, 'change', this.render);
		},

		prepare() {
			const {state} = this.options;
			return {
				...state.toJSON(),
				formatted_subtotal: state.get('money').format(state.get('subtotal')),
				formatted_discount: state.get('money').format(state.get('discount')),
				formatted_tax: state.get('money').format(state.get('tax')),
				formatted_total: state.get('money').format(state.get('total')),
			}
		}
	});

	/**
	 * Run the invoice editor.
	 *
	 * @since 1.0.0
	 */
	Invoice.init = function () {
		// if eac_invoice_vars is not defined, return.
		if (!window.eac_invoice_vars) {
			return;
		}

		const state = new InvoiceState({
			...window?.eac_invoice_vars || {},
			items: new eac.api.DocumentItems(null, {state}),
			money: new Money({code: window?.eac_invoice_vars?.currency}),
		});

		// Hydrate collections.
		var items = eac_invoice_vars?.items || [];
		items.forEach(function (_item) {
			var tax_ids = (_item.taxes || []).map(tax => tax.tax_id);
			var taxes = new eac.api.Taxes();
			taxes.fetch({data: {include: tax_ids}});
			var item = new eac.api.Item({
				..._item,
				taxes,
			});
			_.each(_item.taxes || [], function (tax) {
				tax.id = tax.tax_id;
				item.get('taxes').add(tax);
			});
			state.get('items').add(item);
		});
		state.updateAmounts();
		new Invoice({state}).render();
	};

	$(Invoice.init);

}(document, wp, jQuery));
