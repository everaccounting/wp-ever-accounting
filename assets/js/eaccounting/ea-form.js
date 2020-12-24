jQuery(function ($) {
	'use strict';

	var maskInput = function (el, json) {
		console.log(json);
		$(el).inputmask('decimal', {
			alias: 'numeric',
			groupSeparator: json.thousand_separator,
			autoGroup: true,
			digits: json.precision,
			radixPoint: json.decimal_separator,
			digitsOptional: false,
			allowMinus: false,
			prefix: json.symbol,
			placeholder: '0.000',
			rightAlign: 0,
			autoUnmask: true
		});
	}

	var revenue_form = {
		init: function () {
			$(document)
				.on('change', '#ea-revenue-form #account_id', this.update_amount_input)
				.on('submit', '#ea-revenue-form', this.submit);
		},

		block: function () {
			$('#ea-revenue-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function () {
			$('#ea-revenue-form').unblock();
		},

		update_amount_input: function (e) {
			var account_id = parseInt(e.target.value, 10);
			console.log(account_id)
			if (isNaN(account_id)) {
				return false;
			}
			revenue_form.block();
			var data = {
				action: 'eaccounting_get_account_currency',
				account_id: account_id,
				_wpnonce: eaccounting_form_i10n.nonce.get_currency,
			}
			$.post(ajaxurl, data, function (json) {

				if (json.success) {
					maskInput($('#ea-revenue-form #amount'), json.data);
				}

			}).always(function (json) {
				revenue_form.unblock();
				$.eaccounting_notice(json);
			});
		},

		submit: function (e) {
			e.preventDefault();
			revenue_form.block();
			var data = $('#ea-revenue-form').serializeObject();
			$.post(ajaxurl, data, function (json) {
				$.eaccounting_redirect(json);
			}).always(function (json) {
				$.eaccounting_notice(json);
				revenue_form.unblock();
			});
		}
	}

	var payment_form = {
		init: function () {
			$(document)
				.on('change', '#ea-payment-form #account_id', this.update_amount_input)
				.on('submit', '#ea-payment-form', this.submit);
		},

		block: function () {
			$('#ea-payment-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function () {
			$('#ea-payment-form').unblock();
		},

		update_amount_input: function (e) {
			var account_id = parseInt(e.target.value, 10);
			console.log(account_id)
			if (isNaN(account_id)) {
				return false;
			}
			payment_form.block();
			var data = {
				action: 'eaccounting_get_account_currency',
				account_id: account_id,
				_wpnonce: eaccounting_form_i10n.nonce.get_currency,
			}
			$.post(ajaxurl, data, function (json) {

				if (json.success) {
					maskInput($('#ea-payment-form #amount'), json.data);
				}

			}).always(function (json) {
				payment_form.unblock();
				$.eaccounting_notice(json);
			});
		},

		submit: function (e) {
			e.preventDefault();
			payment_form.block();
			var data = $('#ea-payment-form').serializeObject();
			$.post(ajaxurl, data, function (json) {
				$.eaccounting_redirect(json);
			}).always(function (json) {
				$.eaccounting_notice(json);
				payment_form.unblock();
			});
		}
	}

	var account_form = {
		init: function () {
			$('#ea-account-form')
				.on('select2:select', '#currency_code', this.update_amount_input)
				.on('submit', this.submit);
		},

		block: function () {
			$('#ea-account-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function () {
			$('#ea-account-form').unblock();
		},

		update_amount_input: function (e) {
			var data = e.params.data;
			maskInput($('#ea-account-form #opening_balance'), data.item);
		},

		submit: function (e) {
			e.preventDefault();
			account_form.block();
			var data = $('#ea-account-form').serializeObject();
			$.post(ajaxurl, data, function (json) {
				$.eaccounting_redirect(json);
			}).always(function (json) {
				$.eaccounting_notice(json);
				account_form.unblock();
			});
		}
	}

	var transfer_form = {
		init: function () {
			$('#ea-transfer-form')
				.on('change', '#from_account_id', this.update_amount_input)
				.find('#from_account_id').trigger('change')
				.end()
				.on('submit', this.submit)
		},

		block: function () {
			$('#ea-customer-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function () {
			$('#ea-transfer-form').unblock();
		},

		update_amount_input: function (e) {
			var account_id = parseInt(e.target.value, 10);
			console.log(account_id)
			if (isNaN(account_id)) {
				return false;
			}
			transfer_form.block();
			var data = {
				action: $(this).data('ajax_action'),
				account_id: account_id,
				_wpnonce: $(this).data('nonce'),
			}
			$.post(ajaxurl, data, function (json) {
				if (json.success) {
					maskInput($('#ea-transfer-form #amount'), json.data);
				}

			}).always(function (json) {
				transfer_form.unblock();
				$.eaccounting_notice(json);
			});
		},

		submit: function (e) {
			e.preventDefault();
			transfer_form.block();
			var data = $('#ea-transfer-form').serializeObject();
			$.post(ajaxurl, data, function (json) {
				$.eaccounting_redirect(json);
			}).always(function (json) {
				$.eaccounting_notice(json);
				transfer_form.unblock();
			});
		}
	}

	var customer_form = {
		init: function () {
			$('#ea-customer-form')
				.on('submit', this.submit);
		},
		block: function () {
			$('#ea-customer-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function () {
			$('#ea-customer-form').unblock();
		},
		submit: function (e) {
			e.preventDefault();
			customer_form.block();
			var data = $('#ea-customer-form').serializeObject();
			$.post(ajaxurl, data, function (json) {
				$.eaccounting_redirect(json);
			}).always(function (json) {
				$.eaccounting_notice(json);
				customer_form.unblock();
			});
		}
	};

	var vendor_form = {
		init: function () {
			$('#ea-vendor-form')
				.on('submit', this.submit);
		},
		block: function () {
			$('#ea-vendor-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function () {
			$('#ea-vendor-form').unblock();
		},
		submit: function (e) {
			e.preventDefault();
			vendor_form.block();
			var data = $('#ea-vendor-form').serializeObject();
			$.post(ajaxurl, data, function (json) {
				$.eaccounting_redirect(json);
			}).always(function (json) {
				$.eaccounting_notice(json);
				vendor_form.unblock();
			});
		}
	};


	revenue_form.init();
	payment_form.init();
	account_form.init();
	transfer_form.init();
	customer_form.init();
	vendor_form.init();
});
