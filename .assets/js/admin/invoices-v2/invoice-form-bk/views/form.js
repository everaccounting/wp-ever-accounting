import $ from 'jquery';
import AddLineItemDialog from './add-line-item-dialog';

export default wp.Backbone.View.extend({
	el: '#eac-invoice-form',

	events: {
		'click .add-line-item': 'onAddLineItem',
		// 'submit #eac-form-invoice-add-line-item': 'onSubmitAddLineItem',
	},

	initialize() {
		_.bindAll(this, 'render');
	},

	onAddLineItem(e) {
		var self = this;
		e.preventDefault();
		console.log('onAddLineItem');
		this.$el.eacmodal({
			template: 'eac-modal-add-line',
			variables: {
				quantity: 1,
				price: 100,
			},
			onOpen: function() {
				console.log(this);
			},
			events:{
				'submit': function (e){
					e.preventDefault();
					console.log('submit');
				}
			}
		});
	},
});
