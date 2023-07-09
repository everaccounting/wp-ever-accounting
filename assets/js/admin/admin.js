(function ($) {
	'use strict';
	// if eac is not defined then return.
	if (typeof eac === 'undefined') {
		console.warn('eac is not defined');
		return;
	}

	$(document).ready(function () {
		$(document)
			.on('init-select2', function () {
				$(':input.eac-select2')
					.filter(':not(.enhanced)')
					.each(function () {
						eac.fn.select2($(this));
					});
			})
			.on('init-datepicker', function () {
				$(':input.eac-datepicker')
					.filter(':not(.enhanced)')
					.each(function () {
						eac.fn.datepicker($(this));
					});
				eac.fn.datepicker(
					$('#eac_year_start_date', {
						dateFormat: 'mm-dd',
						changeYear: false,
					})
				);
			})
			.on('init-tooltip', function () {
				$('.eac-tooltip')
					.filter(':not(.enhanced)')
					.each(function () {
						eac.fn.tooltip($(this));
					});
			})
			.on('click', 'a.del, a.delete', function (e) {
				if (!confirm(eac_admin_vars.i18n.confirm_delete)) {
					e.stopPropagation();
					return false;
				}
			})
			.on('block', '.eac-ajax-form', function () {
				eac.fn.blockForm(this);
			})
			.on('unblock', '.eac-ajax-form', function () {
				eac.fn.unblockForm(this);
			})
			.on('eac-drawer-ready', function () {
				eac_init();
			})
			.on('init-form', '.eac-ajax-form', function () {
				// If the form contains exchange_rate field, then we need to update the exchange rate when the currency is changed.
				var $form = $(this);
				var $exchange_rate = $(':input[name="exchange_rate"]', $form);
				var $currency_code = $(':input[name="currency_code"]', $form);

				if ($exchange_rate.length && $currency_code.length) {
					$currency_code
						.on('change', function () {
							var $this = $(this);
							var currency_code = $this.val();
							$(
								'.eac-form-field__addon:last-child',
								$exchange_rate.closest('.eac-form-field')
							).text(currency_code);
							eac.api
								.getExchangeRate(currency_code)
								.then(function (exchange_rate) {
									console.log(exchange_rate)
									$exchange_rate.val(exchange_rate);
									// If the currency code is base currency then hide the exchange rate field. Otherwise, show it.
									if (
										currency_code ===
										eac.fn.getVar('base_currency_code')
									) {
										$exchange_rate
											.closest('.eac-form-field')
											.addClass('d-none')
									} else {
										$exchange_rate
											.closest('.eac-form-field')
											.removeClass('d-none')
									}
								});
						});
				}

				$(':input[class*="eac-input-decimal"]', $form).on('keyup change', function () {
					var val = $(this).val();
					val = val.replace(/[^0-9.]/g, '');
					$(this).val(val);
				});
			})
			.on('init-form', '#eac-item-form', function () {
				eac.fn.inputPrice($(':input[name="price"]', this));
			})
			.on('init-form', '#eac-payment-form, #eac-expense-form', function () {
					var $form = $(this);
					var $currency_code = $(
						':input[name="currency_code"]',
						$form
					);
					var $account_id = $(':input[name="account_id"]', $form);
					var $amount = $(':input[name="amount"]', $form);
					$currency_code
						.on('change', function () {
							const currency_code = $(this).val();
							console.log(currency_code);
							eac.fn.inputPrice($amount, currency_code);
						})
						.trigger('change');
					$account_id
						.on('change', function () {
							const account_id = $(this).val();
							eac.api
								.getAccount(account_id)
								.then(function (account) {
									$currency_code
										.val(account.currency_code)
										.trigger('change');
								});
						})
						.trigger('change');
				})
			.on('init-form', '#eac-account-form', function () {
				var $form = $(this);
				$(':input[name="currency_code"]', $form)
					.on('change', function () {
						const currency_code = $(this).val();
						console.log(currency_code);
						eac.fn.inputPrice(
							$(':input[name="opening_balance"]', $form),
							currency_code
						);
					})
					.trigger('change');
			})
			.on('init-form', '#eac-transfer-form', function () {
				var $form = $(this);
				var $account_id = $(':input[name="from_account_id"]', $form);
				var $currency_code = $(':input[name="currency_code"]', $form);
				var $amount = $(':input[name="amount"]', $form);

				$account_id
					.on('change', function () {
						const account_id = $(this).val();
						eac.api.getAccount(account_id).then(function (account) {
							$currency_code
								.val(account.currency_code)
								.trigger('change');
						});
					})
					.trigger('change');

				$currency_code
					.on('change', function () {
						const currency_code = $(this).val();
						console.log(currency_code);
						eac.fn.inputPrice($amount, currency_code);
					})
					.trigger('change');
			})
			.on('init-form', '#eac-invoice-form', function () {
				var $form = $(this);
				$(':input[name="contact_id"]', $form).on('change', function () {
					const contact_id = $(this).val();
					eac.api.getCustomer(contact_id).then(function (contact) {
						$.each(contact, function (key, value) {
							console.log(key, value);
							$(':input[name="billing_' + key + '"]', $form).val(value);
						});
						$form.trigger('update');
					});
				});
			})
			.on('init-form', '#eac-bill-form', function () {

			})
			.on('init-form', '.eac-document-form', function () {
				var $form = $(this);
				$('a.edit_billing_address', $form).on('click', function (e) {
					e.preventDefault();
					if ($(this).hasClass('d-none')) {
						return;
					}
					$(this).toggleClass('d-none');
					$('.billing-fields, .billing-data', $form).toggleClass('d-none');
					$('.load_billing_address', $form).toggle();
				});
				$('a.add-line-item', $form).on('click', function (e) {
					e.preventDefault();
					e.preventDefault();
					var $form = $(this).closest('form');
					$('#new-line-item', $form).removeClass('d-none');
				});
				$('a.remove-line-item', $form).on('click', function (e) {
					e.preventDefault();
					$(this).closest('tr').remove();
					$form.trigger('update');
				});
				$('a.calculate-totals', $form).on('click', function (e) {
					e.preventDefault();
					$form.data('form_data', null);
					$form.trigger('update');
				});
				$(':input[class*="trigger-update"]', $form).on('change', function () {
					$form.trigger('update');
				});
			})
			.on('update', '#eac-bill-form', function () {
				var $form = $(this);
				var form_data = eac.fn.getFormData($form);
				var form_id = $form.attr('id');
				var original_data = $form.data('form_data');
				var current_data = eac.fn.getFormData($form);

				if (_.isEqual(original_data, current_data)) {
					return;
				}
				eac.fn.blockForm($form);
				current_data.action = 'eac_calculate_bill_totals';
				$form.load(eac.vars.ajax_url, current_data, function () {
					eac.fn.unblockForm($form);
					$form.data('form_data', form_data);
					$form.removeClass('initiated');
					eac_init();
				});
			})
			.on('update', '#eac-invoice-form', function () {
				var $form = $(this);
				var form_data = eac.fn.getFormData($form);
				var form_id = $form.attr('id');
				var original_data = $form.data('form_data');
				var current_data = eac.fn.getFormData($form);

				if (_.isEqual(original_data, current_data)) {
					return;
				}
				eac.fn.blockForm($form);
				current_data.action = 'eac_calculate_invoice_totals';
				$form.load(eac.vars.ajax_url, current_data, function () {
					eac.fn.unblockForm($form);
					$form.data('form_data', form_data);
					$form.removeClass('initiated');
					eac_init();
				});
			});

		function eac_init() {
			$(document).trigger('init-select2');
			$(document).trigger('init-datepicker');
			$(document).trigger('init-tooltip');
			$('.eac-ajax-form')
				.filter(':not(.initiated)')
				.each(function () {
					var $form = $(this);
					$form.addClass('initiated');
					$form.trigger('init-form');
					eac.fn.ajaxForm(this);
				});
		}

		eac_init();
	});
})(jQuery);
