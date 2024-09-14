/* global Backbone, _, $ */
import LineItem	 from "../models/line-item";

export const AddLineItem = wp.Backbone.View.extend( {

	tagName: "div",

	className: 'eac-modal',

	template: wp.template('eac-add-line-item'),

	events: {
		// 'click [data-micromodal-close]': 'close',
		'change .select-line-item': 'onChangeLineItem',
		'change .add-line-item-quantity': 'onChangeQuantity',
		'change .add-line-item-price': 'onChangePrice',
		'change .add-line-item-tax': 'onChangeTax',
	},

	initialize: function () {
		const { state } = this.options;
		const id = _.uniqueId('new-');

		this.model = new LineItem({
			id,
			state,
			quantity: 1,
			price: 0,
		});

		_.bindAll( this, 'render' );
	},

	prepare: function () {
		const { model, options } = this;
		const { state } = options;
		const data = this.model
			? {
				...this.model.toJSON(),
				state: this.model.get( 'state' ).toJSON(),
			}
			: {};
		// console.log(state);
		return {
			...data,
		};
	},

	render: function () {
		wp.Backbone.View.prototype.render.apply(this, arguments);

		jQuery( document.body ).css({
			'overflow': 'hidden'
		}).append( this.$el );

		jQuery(document.body).trigger('eac_update_ui');

		return this;
	},

	open: function () {
		this.$el.addClass('is-open');
		// this.$el.attr('role', 'dialog');
		// this.$el.attr('aria-hidden', 'false');
		// this.$el.attr('id', _.uniqueId('eac-modal-'));
		// jQuery('body').append(this.$el);
		// jQuery(document.body).trigger('eac_update_ui');
		return this;
	},

	close: function () {
		this.$el.removeClass('is-open');
		this.undelegateEvents();
		this.remove();
	},

	onChangeLineItem: function (e) {
		e.preventDefault();
		var that = this;
		const {state} = this.options;
		console.log(state);
		const items = state.get('items') || [];
		console.log(items);
		var id = parseInt(e.target.value);
		wp.apiRequest({
			path: '/eac/v1/items/' + id,
			method: 'GET',
		}).done(function (response) {
			var model = new LineItem({...response, id: undefined});
			items.push(model);
			that.close();
			// console.log(items);
			// that.model.set({
			// 	item_id: response.id,
			// 	name: response.name,
			// 	description: response.description,
			// 	price: response.price,
			// 	tax: response.tax,
			// });
		});
	},

	onChangeQuantity: function (e) {
		this.model.set('quantity', parseFloat(e.target.value));
	},

	onChangePrice: function (e) {
		this.model.set('price', parseFloat(e.target.value));
	},

	onChangeTax: function (e) {
		e.preventDefault();
		// multiple tax rates.
		console.log(e.target.value);
	},
} );


export default AddLineItem;
