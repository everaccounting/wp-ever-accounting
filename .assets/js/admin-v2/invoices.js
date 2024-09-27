const {Money} = eac.money;

(function (document, wp, $) {
	'use strict';

	/**
	 * Invoice state model.
	 *
	 * @type {Backbone.Model}
	 * @since 1.0.0
	 */
	var State  = eac.api.Invoice.extend({
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
		},

		render() {
			this.views.detach();
			this.views.add('.billing-address', new Invoice.BillingAddress(this.options));
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
				state.set('address', {});
				return;
			}
			// this.blockUI()
			new eac.api.Customer({id: contact_id}).fetch({
				success: (model) => {
					state.set('address', model.toJSON());
					state.set('contact_id', model.get('id'));
					console.log(model.toJSON())
					// this.unblockUI()
				}
			});
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
			this.listenTo(state, 'address', this.render);
		},

		prepare() {
			const {state} = this.options;

			return {
				...state.get('address'),
			}
		},
	});

	/**
	 * Invoice Items View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	Invoice.Items = wp.Backbone.View.extend({});

	/**
	 * Invoice Items No Items View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	Invoice.NoItems = wp.Backbone.View.extend({});

	/**
	 * Invoice Items Item View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	Invoice.Item = wp.Backbone.View.extend({});

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
			}
		}
	});

	/**
	 * Run the invoice editor.
	 *
	 * @since 1.0.0
	 */
	Invoice.init = function () {
		const state = new State({
			...window.eac_invoice_vars || {},
			money: new Money({code: window.eac_invoice_vars?.currency}),
		});

		// Hydrate collections.
		var items = eac_invoice_vars?.items || [];
		items.forEach(function (item) {
			var taxes = item.taxes || [];
			taxes.forEach(function (tax) {
				item.get('taxes').add(tax);
			});
			state.get('items').add(item);
		});

		new Invoice({state}).render();
	};

	$(Invoice.init);

}(document, wp, jQuery));
