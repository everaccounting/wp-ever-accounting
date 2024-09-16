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

	const Form = eac.components.View.extend({
		el: '#eac-bill-form',

		// events: {
		// 	'change [name="contact_id"]': 'onContactChange',
		// },

		initialize() {
			console.log('--------initialize---------')
			console.log(this.form)
			this.listenTo(this.form, 'change', this.onContactChange);
		},

		render() {
			console.log(this.form)
			this.views.detach();
			this.views.add('.billing-address', new BillingAddress(this.options));
			$(document.body).trigger('eac_update_ui');
			return this;
		},

		onContactChange(e) {
			console.log('onContactChange')
			// e.preventDefault();
			// var state = this.options.state;
			// var contact_id = e.target.value || null;
			// if (contact_id === this.options.state.get('customer_id') || !contact_id) {
			// 	state.set('address', {});
			// 	return;
			// }
			// this.blockUI()
			// new eac.api.Vendor({id: contact_id}).fetch({
			// 	success: (model) => {
			// 		state.set('address', model.toJSON());
			// 		state.set('contact_id', model.get('id'));
			// 		this.unblockUI()
			// 	}
			// });

		},
	});


	var state = new eac.api.Bill();
	const form = new Form({state});
	form.render();
});
