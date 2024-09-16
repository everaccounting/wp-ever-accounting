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

	const Items = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-items__items',

		render() {
			this.$el.html('');
			this.collection.each((model) => {
				const view = new Row({model});
				this.$el.append(view.render().el);
			});
			return this;
		}
	});

	const Item = wp.Backbone.View.extend({
		tagName: 'tr',

		className: 'eac-document-items__item',

		template: wp.template('eac-items-item'),
	});

	const NoItems = wp.Backbone.View.extend({
		tagName: 'tr',

		className: 'eac-document-items__no-items',

		template: wp.template('eac-items-no-items'),
	});

	const Totals = wp.Backbone.View.extend({
		tagName: 'tfoot',

		className: 'eac-document-items__totals',

		template: wp.template('eac-bill-totals'),

		initialize() {
			const {state} = this.options;
			this.listenTo(state, 'change', this.render);
		},

		prepare() {
			const {state} = this.options;
			return {
				...state.toJSON(),
				// itemized_taxes: state.getItemizedTaxes(),
			}
		},
	});

	const Form = eac.components.View.extend({
		el: '#eac-bill-form',

		events: {
			'change [name="contact_id"]': 'onContactChange',
		},

		render() {
			this.views.detach();
			this.views.add('.billing-address', new BillingAddress(this.options));
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
	});


	var state = new eac.api.Bill();
	const form = new Form({state});
	form.render();
});
