/*global jQuery, Backbone, _, woocommerce_admin_api_keys, wcSetClipboard, wcClearClipboard */
(function ($) {

	var Revenue_Form = Backbone.View.extend({
		/**
		 * Element
		 *
		 * @param {Object} '#ea-revenue-form'
		 */
		el: '#ea-revenue-form',

		/**
		 * Events
		 *
		 * @type {Object}
		 */
		events: {
			'submit': 'onSubmit',
			'change select#account_id': 'onAccountChange'
		},

		/**
		 * Initialize actions
		 */
		initialize: function () {
			this.account = $('#account_id', this.$el);
			this.amount = $('#amount', this.$el);
			_.bindAll(this, 'onSubmit', 'onAccountChange');
		},

		block: function () {
			this.$el.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function () {
			this.$el.unblock();
		},

		onAccountChange: function () {
			var id = parseInt(this.account.val(), 10);
			if (!id) {
				return;
			}
			var self = this;
			self.block();
			var nonce = this.account.data('nonce');
			wp.ajax.send('eaccounting_get_account', {
				data: {
					id: id,
					_wpnonce: nonce,
				},
				success: function (res) {
					console.log(res);
					self.unblock();
				},
				error: function (res) {
					self.unblock();
				}
			})
		},

		/**
		 * Save API Key using ajax
		 *
		 * @param {Object} e
		 */
		onSubmit: function (e) {
			e.preventDefault();
			// $('.notice', this.el).closest('#tab_container').remove();
			this.$el.closest('#tab_container').prepend( '<div class="notice updated"><p>lorem ipsum dolor sit amet</p></div>' );

			var formData = this.$el.serializeArray();
			console.log(formData);
		}
	});


	jQuery(document).ready(function () {
		$('.select2-results__option:contains("2413")').css('color', 'red');
		new Revenue_Form();
	})

})(jQuery);
