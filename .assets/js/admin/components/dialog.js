import $ from 'jquery';

export default wp.Backbone.View.extend( {
	/**
	 * "Modal" view.
	 *
	 * @since 1.0.0
	 *
	 * @constructs Modal
	 * @augments wp.Backbone.View
	 */
	initialize() {
		this.$el.dialog( {
			position: {
				my: 'top center',
				at: 'center center-25%',
			},
			classes: {
				'ui-dialog': 'eac-dialog',
			},
			closeText: eac_admin_var.i18n.closeText,
			width: '350px',
			modal: true,
			resizable: false,
			draggable: false,
			autoOpen: false,
			create: function() {
				$( this ).css( 'maxWidth', '90vw' );
			},
		} );
	},

	/**
	 * Opens the jQuery UI Dialog containing this view.
	 *
	 * @since 1.0.0
	 *
	 * @return {Modal} Current view.
	 */
	open() {
		this.$el.dialog( 'open' );

		return this;
	},

	/**
	 * Closes the jQuery UI Dialog containing this view.
	 *
	 * @since 1.0.0
	 *
	 * @return {Modal} Current view.
	 */
	close() {
		this.$el.dialog( 'close' );

		return this;
	},
} );
