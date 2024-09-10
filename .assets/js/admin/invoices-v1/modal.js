export const Modal = wp.Backbone.View.extend({
	tagName: "div",

	className: 'eac-modal',

	template: wp.template("eac-modal"),


	prepare: function () {
		const {model} = this;
		return {
			...model,
		};
	},

	render: function () {
		wp.Backbone.View.prototype.render.apply(this, arguments);
		this.$el.attr('role', 'dialog');
		this.$el.attr('aria-hidden', 'true');
		this.$el.attr('id', _.uniqueId('eac-modal-'));

		jQuery('body').append(this.$el);

		return this;
	},

	open: function () {
		this.$el.addClass('is-open');
		return this;
	},

	close: function () {
		this.$el.removeClass('is-open');
		this.undelegateEvents();
		this.remove();
	},
});

export default Modal;
