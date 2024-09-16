/**
 * ========================================================================
 * BILL FORM
 * ========================================================================
 */
jQuery(document).ready(($) => {
	'use strict';

	const BillingAddress = wp.Backbone.View.extend({
		el: '.billing-address',

		template: wp.template('eac-billing-address'),

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

	const NoItems = wp.Backbone.View.extend({
		tagName: 'tr',

		className: 'eac-document-items__no-items',

		template: wp.template('eac-items-empty'),
	});

	const Item = wp.Backbone.View.extend({
		tagName: 'tr',

		className: 'eac-document-items__item',

		template: wp.template('eac-items-item'),

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
			console.log(model.toJSON());
			return {
				...model.toJSON(),
				// tax: model.get('taxes').reduce((acc, tax) => acc + tax.get('amount'), 0),
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

	const Items = wp.Backbone.View.extend({
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

	const Actions = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-items__actions',

		template: wp.template('eac-items-actions'),

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

	const Totals = wp.Backbone.View.extend({
		tagName: 'tfoot',

		className: 'eac-document-items__totals',

		template: wp.template('eac-items-totals'),

		initialize() {
			const {state} = this.options;
			this.listenTo(state, 'change', this.render);
		},

		prepare() {
			const {state} = this.options;
			console.log(state.toJSON())
			return {
				...state.toJSON(),
				// itemized_taxes: state.getItemizedTaxes(),
			}
		},
	});

	const Form = wp.Backbone.View.extend({
		el: '#eac-bill-form',

		events: {
			'change [name="contact_id"]': 'onContactChange',
			'change [name="currency_code"]': 'onCurrencyCodeChange',
			'change [name="discount_value"]': 'onDiscountValueChange',
			'change [name="discount_type"]': 'onDiscountTypeChange',
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
			this.blockUI()
			new eac.api.Vendor({id: contact_id}).fetch({
				success: (model) => {
					state.set('address', model.toJSON());
					state.set('contact_id', model.get('id'));
					this.unblockUI()
				}
			});
		},

		onCurrencyCodeChange(e) {
			e.preventDefault();
			this.options.state.set('currency_code', e.target.value);
		},

		onDiscountValueChange(e) {
			e.preventDefault();
			var state = this.options.state;
			var value = parseFloat(e.target.value, 10);

			console.log('discount_value', value);
			// if type is percent and amount is greater than 100, set to 100.
			if (state.get('discount_type') === 'percent' && value > 100) {
				console.log('resetting discount_value to 100');
				value = 100;
				this.$('[name="discount_value"]').val(100);
			}

			state.set('discount_value', value);
			state.updateAmounts();
		},

		onDiscountTypeChange(e) {
			var state = this.options.state;
			var value = e.target.value;

			//if type is percent and amount is greater than 100, set to 100.
			if (value === 'percent' && state.get('discount_value') > 100) {
				state.set('discount_value', 100);
				this.$('[name="discount_value"]').val(100);
			}
			state.set('discount_type', value);
			state.updateAmounts();
		},
	});

	/**
	 * Set up the Bill UI.
	 */
	var int = function () {
		const state = new eac.api.Bill({
			...window.eac_bill_edit_vars?.bill || {},
			settings: window.eac_bill_edit_vars?.settings || {},
		});

		// Hydrate collections.
		var items = eac_bill_edit_vars?.bill?.items || [];
		items.forEach(function (item) {
			state.get('items').add(item);
		});

		new Form({state}).render();
	}

	// Initialize the Bill UI.
	$(int);
});
