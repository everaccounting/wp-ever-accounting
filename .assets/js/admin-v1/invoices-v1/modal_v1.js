export const Modal = wp.Backbone.View.extend({
	tagName: 'div',
	// id: 'wc-backbone-modal-dialog',
	// _target: undefined,
	// _string: undefined,

	initialize: function () {
		wp.Backbone.View.prototype.initialize.apply(this, arguments);
		_.bindAll( this, 'render' );
		this.render();
	},

	prepare: function () {
		console.log(this.model.toJSON());
		return this.model
			? {
				...this.model.toJSON(),
				// state: this.model.get( 'state' ).toJSON(),
			}
			: {};
	},

	render: function() {

		// var template = wp.template( this.template );
		//
		// this.$el.append( template( this.model.toJSON()) );

		// jQuery( document.body ).css({
		// 	'overflow': 'hidden'
		// }).append( this.$el );
		// wp.Backbone.View.prototype.render.apply( this, arguments );

		return this;
	},
});

export default Modal;
