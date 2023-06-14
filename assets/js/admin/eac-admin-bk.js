(function ($, window, document, undefined) {
	'use strict';

	var eac_admin = {
		init: function () {
			$(document)
				.on('click', '.eac-dropdown__button', this.toggle_dropdown)
				.on('click', '.del', this.handle_delete)
				.on('block', 'form.eac-form', this.block_form)
				.on('unblock', 'form.eac-form', this.unblock_form)
				.on('submit', 'form.eac-form', this.submit_form)
				.on('change', '.eac-form [name="is_taxable"]', this.toggle_tax_fields)
				.on('eac_drawer_ready', this.triggerEvents)
				.on('init-select2', this.init_select2)
				.on('init-datepicker', this.init_datepicker)
				.on('init-price-input', this.init_price_input)
				.on('init-tooltip', this.init_tooltip)
				.on('ready', this.triggerEvents)
		},
		triggerEvents: function () {
			// Events that need to be bound on page load or after ajax.
			$(document)
				.trigger('init-select2')
				.trigger('init-datepicker')
				.trigger('init-price-input')
				.trigger('init-tooltip');

			console.log('bindEvents');
		},
		init_select2: function () {
			$('.eac-select2,select[data-ajax-type]').each(function () {
				var $el = $(this);
				var options = {
					allowClear: $el.data('allow_clear') && !$el.prop('multiple') || true,
					placeholder: $el.data('placeholder') || '',
					minimumInputLength: $el.data('minimum_input_length') ? $el.data('minimum_input_length') : 0,
					minimumResultsForSearch: 10,
				}
				// if the select2 is within a drawer, then set the parent to the drawer.
				if ($el.closest('.eac-form').length) {
					this.options.dropdownParent = $el.closest('.eac-form');
				}

				// If data-eac-select2 is set, then we will add ajax options.
				if ($el.data('ajax-type')) {
					options.ajax = {
						url: eac_admin_vars.ajax_url,
						dataType: 'json',
						method: 'POST',
						delay: 250,
						data: function (params) {
							return {
								action: 'eac_json_search',
								type: $(this).data('ajax-type'),
								_wpnonce: eac_admin_vars.json_search,
								term: params.term,
								page: params.page,
							};
						},
						processResults: function (data, params) {
							params.page = params.page || 1;
							return data;
						},
						cache: true,
					};
				}


				// init the select2.
				return $el.select2(options);
			});
		},
		init_datepicker: function () {
			$('.eac-field-date').datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				yearRange: '-100:+0',
			});
			$('#eac_financial_year_start').datepicker({
				dateFormat: 'mm-dd',
				changeMonth: true,
				changeYear: false,
				yearRange: '-100:+0',
			});
		},
		init_price_input: function () {
			var base_currency = eac_admin_vars.base_currency || 'USD';
			var currencies = eac_admin_vars.currencies || {};
			$('form.eac-form').each(function () {
				var $form = $(this);
				var $currency_input = $form.find('.eac-select-currency');
				var $amount_input = $form.find('.eac-field-money');
				var $rate_input = $form.find('input[name="currency_rate"]');
				var currency_code = $currency_input.val() || base_currency;
				var currency_data = currencies[currency_code] || {};
				var currency_rate = currency_data['rate'] || 1;
				// If the currency input field value is base currency, then hide the rate input otherwise show it.
				// Based on the currency input field value, set the rate input value.
				// Based on the currency input field format, set the amount input format.

				// If rate input is exists, then set the rate input value.
				if ($rate_input.length) {
					$rate_input.val(currency_rate);
					if (currency_code === base_currency) {
						$rate_input.closest('.eac-field').hide();
					} else {
						$rate_input.closest('.eac-field').show();
					}
				}

				// If amount input is exists, then set the amount input format.
				if ($amount_input.length) {
					$amount_input.inputmask('decimal', {
						alias: 'numeric',
						groupSeparator: currency_data.thousand_sep,
						autoGroup: true,
						digits: currency_data.precision,
						radixPoint: currency_data.decimal_sep,
						digitsOptional: false,
						allowMinus: true,
						rightAlign: false,
						prefix: currency_data.position === 'before' ? currency_data.symbol : '',
						suffix: currency_data.position === 'after' ? currency_data.symbol : '',
					});
				}

				// If there is any currency field, then we will update the price input mask when the currency field value changed
				if ($currency_input.length) {
					$currency_input.on('change', function () {
						$(document).trigger('init-price-input');
					});
				}
			});

		},
		init_tooltip: function () {
			$('.eac-tooltip').tooltip({
				tooltipClass: 'eac-ui-tooltip',
				position: {
					my: 'center top',
					at: 'center bottom+10',
					collision: 'flipfit',
				},
				hide: {
					duration: 200,
				},
				show: {
					duration: 200,
				},
			});
		},
		handle_delete: function () {
			// confirmation alert.
			if (!confirm(eac_admin_vars.i18n.delete_confirmation)) {
				e.preventDefault();
				return false;
			}
		},
		block_form: function () {
			var $form = $(this);
			var $submit = $('[form="' + $form.attr('id') + '"]');
			$submit.attr('disabled', 'disabled');
			$form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
		},
		unblock_form: function () {
			var $form = $(this);
			var $submit = $('[form="' + $form.attr('id') + '"]');
			$submit.removeAttr('disabled');
			$form.unblock();
		},
		submit_form: function (e) {
			e.preventDefault();
			var $form = $(this);
			var data = $form.serializeAssoc();
			var $submit = $('[form="' + $form.attr('id') + '"]');
			// if submit button is disabled, do nothing.
			if ($submit.is(':disabled')) {
				return;
			}

			// block the form.
			$form.trigger('block');

			$.post(eac_admin_vars.ajax_url, data, function (json) {
				if (json.success) {
					if ($form.closest('.eac-drawer').length) {
						$form.closest('.eac-drawer').data('eac_drawer').close();
					} else {
						$.eac_redirect(json)
					}
				}
			}).always(function (json) {
				$form.trigger('unblock');
				$.eac_notification(json);
			});
		},
		toggle_dropdown: function () {
			e.preventDefault();
			e.stopPropagation();
			$(this).closest('.eac-dropdown').toggleClass('is--open');

			$(document).on('click', function () {
				$('.eac-dropdown').removeClass('is--open');
			});
		},
		toggle_tax_fields: function () {
			var $this = $(this);
			var $form = $this.closest('.eac-form');
			var $tax_fields = $form.find('.eac-field--tax_ids');
			if ($this.val() === 'yes') {
				$tax_fields.show();
			} else {
				$tax_fields.hide();
			}
		},
	};

	window.eac_admin = eac_admin;

	eac_admin.init();


}(jQuery, window, document));
