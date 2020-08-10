/*global jQuery, eaccounting_form_i10n */
(function ($) {
	if ('undefined' === typeof eaccounting_form_i10n) {
		return;
	}
	$.fn.eaccounting_form = function (options) {
		return this.each(function () {
			(new $.eaccounting_form(this, options));
		});
	};
	$.eaccounting_form = function (el, options) {
		var form = {};
		form.el = el;
		form.$el = $(el);
		form.options = options;

		//fields
		form.account_id = $('#account_id, #from_account_id', form.$el);
		form.currency_code = $('#currency_code', form.$el);
		form.amount = $('#amount, #opening_balance', form.$el);

		form.block = function () {
			form.$el.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		};

		form.unblock = function () {
			form.$el.unblock();
		}

		form.onError = function (error) {
			console.warn(error);
			form.unblock();
		}

		form.maskAmount = function (currency) {
			console.log(currency);
			form.amount.inputmask('decimal', {
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
		};

		form.getCurrency = function (code, onSuccess, onError) {
			console.log(code);
			wp.ajax.send('eaccounting_get_currency', {
				data: {
					code: code,
					_wpnonce: eaccounting_form_i10n.nonce.get_currency
				},
				success: onSuccess,
				error: onError
			});
		};

		form.getAccount = function (id, onSuccess, onError) {
			wp.ajax.send('eaccounting_get_account', {
				data: {
					id: id,
					_wpnonce: eaccounting_form_i10n.nonce.get_account
				},
				success: onSuccess,
				error: onError
			});
		};

		//bind events
		form.$el.on('submit', function (e) {
			e.preventDefault();
			form.block();
			wp.ajax.post(form.$el.serializeArray())
				.then(function (result) {
					result = $.extend(true, {}, {message: '', redirect: ''}, result)
					form.unblock();
					$.eaccounting_notice(result.message, 'success');
					eaccounting.redirect(result.redirect);
				})
				.fail(function (error) {
					console.log(error);
					form.unblock();
					$.eaccounting_notice(error.message, 'error');
				});
		});

		//on currency change
		form.currency_code.on('change', function () {
			if (form.amount.length) {
				var code = form.currency_code.val();
				form.block();
				form.getCurrency(code, function (res) {
					form.maskAmount(res);
					form.unblock();
				}, form.onError);
			}
		});

		//on account change
		form.account_id.on('change', function () {
			if (form.amount.length) {
				var account_id = form.account_id.val();
				var id = parseInt(account_id, 10);
				if (!id) {
					return;
				}
				form.block();
				form.getAccount(id, function (res) {
					form.getCurrency(res.currency_code, function (code) {
						form.maskAmount(code);
						form.unblock();
					}, form.onError)
				}, form.onError);
			}
		});

		//change on first load
		form.account_id.trigger('change');
		form.currency_code.trigger('change');
	};

	$(document).ready(function () {
		$('#ea-account-form, #ea-revenue-form, #ea-payment-form').eaccounting_form();
	});


})(jQuery);
