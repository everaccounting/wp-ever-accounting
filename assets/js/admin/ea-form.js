/*global jQuery, Backbone, _, woocommerce_admin_api_keys, wcSetClipboard, wcClearClipboard */
(function ($) {

	var Accounting_Form = Backbone.View.extend({
		/**
		 * Events
		 *
		 * @type {Object}
		 */
		events: {
			'submit': 'onSubmit',
			'change select#account_id': 'onAccountChange',
			'change select#currency_code': 'onCurrencyChange'
		},

		/**
		 * Initialize actions
		 */
		initialize: function () {
			this.currency = $('#currency_code', this.$el);
			this.account = $('#account_id', this.$el);
			this.amount = $('#amount, #opening_balance', this.$el);
			_.bindAll(this, 'onSubmit', 'onAccountChange', 'onCurrencyChange', 'maskAmount', 'unblock');
		},

		/**
		 * Block UI
		 */
		block: function () {
			this.$el.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		/**
		 * UnBlock UI
		 */
		unblock: function () {
			this.$el.unblock();
		},
		/**
		 * Mask money input
		 * @param currency
		 */
		maskAmount: function (currency) {
			this.amount.inputmask('decimal', {
				alias: 'numeric',
				groupSeparator: currency.thousand_separator,
				autoGroup: true,
				digits: currency.precision,
				radixPoint: currency.decimal_separator,
				digitsOptional: false,
				allowMinus: false,
				prefix: currency.symbol,
				placeholder: '0.000',
				rightAlign: 0
			});
		},
		getCurrency: function (code, onSuccess, onError) {
			wp.ajax.send('eaccounting_get_currency', {
				data: {
					code: code,
				},
				success: onSuccess,
				error: onError
			});
		},
		getAccount: function (id, onSuccess, onError) {
			wp.ajax.send('eaccounting_get_account', {
				data: {
					id: id,
				},
				success: onSuccess,
				error: onError
			});
		},
		onAccountChange: function () {
			var id = parseInt(this.account.val(), 10);
			if (!id) {
				return;
			}
			var self = this;
			self.block();
			this.getAccount(id, function (account) {
				self.getCurrency(account.currency_code, self.maskAmount, self.unblock);
				self.unblock();
			}, self.unblock)
		},

		onCurrencyChange: function () {
			if (this.amount.length) {
				var currency = this.currency.val();
				var self = this;
				self.block();
				self.getCurrency(currency, self.maskAmount, self.unblock);
				self.unblock();
			}
		},

		/**
		 * Save API Key using ajax
		 *
		 * @param {Object} e
		 */
		onSubmit: function (e) {
			e.preventDefault();
			// $('.notice', this.el).closest('#tab_container').remove();
			// this.$el.closest('#tab_container').prepend('<div class="notice updated"><p>lorem ipsum dolor sit amet</p></div>');

			var formData = this.$el.serializeArray();
			console.log(formData);
		}
	});


	jQuery(document).ready(function () {
		new Accounting_Form({
			el: '#ea-account-form, #ea-revenue-form',
		});
	})

})(jQuery);
