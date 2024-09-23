/* global eac_admin_vars */

/**
 * ========================================================================
 * ADMIN UI
 * ========================================================================
 */
jQuery(document).ready(($) => {
	'use strict';

	var initializeUI = function () {
		// Select2.
		$('.eac_select2').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			const options = {
				allowClear: $this.data('allow-clear') && !$this.prop('multiple') || true,
				placeholder: $this.data('placeholder') || '',
				width: '100%',
				minimumInputLength: $this.data('minimum-input-length') || 0,
				readOnly: $this.data('readonly') || false,
				ajax: {
					url: eac_admin_vars.ajax_url,
					dataType: 'json',
					delay: 250,
					method: 'POST',
					data: function (params) {
						return {
							term: params.term,
							action: $this.data('action'),
							type: $this.data('type'),
							subtype: $this.data('subtype'),
							_wpnonce: eac_admin_vars.search_nonce,
							exclude: $this.data('exclude'),
							include: $this.data('include'),
							limit: $this.data('limit'),
						};
					},
					processResults: function (data) {
						data.page = data.page || 1;
						return data;
					},
					cache: true
				}
			}
			// if data-action is not defined then return.
			if (!$this.data('action')) {
				delete options.ajax;
			}
			$this.addClass('enhanced').selectWoo(options);
		});

		// Datepicker.
		$('.eac_datepicker').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			const options = {
				dateFormat: $this.data('format') || 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				showOtherMonths: true,
				selectOtherMonths: true,
				yearRange: '-100:+10',
			};
			$this.addClass('enhanced').datepicker(options);
		});

		// Tooltip.
		$('.eac_tooltip').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			const options = {
				position: {
					my: 'center bottom-15',
					at: 'center top',
				},
				tooltipClass: 'eac_tooltip',
			};
			$this.addClass('enhanced').tooltip(options);
		});

		// currency.
		$(':input.eac_amount').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			const currency = $this.data('currency') || eac_admin_vars.base_currency;
			const precision = eac_admin_vars.currencies[currency].precision || 2;
			const symbol = eac_admin_vars.currencies[currency].symbol || '';
			const position = eac_admin_vars.currencies[currency].position || 'before';
			$this.inputmask({
				alias: 'currency',
				placeholder: '0.00',
				rightAlign: false,
				allowMinus: true,
				digits: precision,
				prefix: 'before' === position ? symbol : '',
				suffix: 'after' === position ? symbol : '',
				removeMaskOnSubmit: true
			}).addClass('enhanced');
		});

		// inputMask.
		$('.eac_inputmask').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			const options = {
				alias: $this.data('alias') || '',
				placeholder: $this.data('placeholder') || '',
				clearIncomplete: $this.data('clear-incomplete') || false,
			};
			$this.addClass('enhanced').inputmask(options);
		});

		// Number Input.
		$('.eac_number_input').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			$this.addClass('enhanced').on('input', function () {
				$this.value = $this.value.replace(/[^0-9]/g, '');
			});
		});

		// Decimal Input.
		$('.eac_decimal_input').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			$this.addClass('enhanced').on('input', function () {
				var val = $(this).val();
				val = val.replace(/[^0-9.]/g, '');
				$this.val(val);
			});
		});

		// Polyfill for card padding for firefox.
		$('.eac-card').each(function () {
			if (!$(this).children('[class*="eac-card__"]').length && !parseInt($(this).css('padding'))) {
				$(this).css('padding', '8px 12px');
			}
		});

		// Any form id starts with eac- have currency and exchange rate input field name.
		$('form[id^="eac-"]').filter(function () {
			return $(this).find(':input[name="currency"]').length && $(this).find(':input[name="exchange_rate"]').length;
		}).filter(':not(.enhanced)').each(function () {
			const $form = $(this);
			const $currency = $form.find('[name="currency"]');
			const $exchange_rate = $form.find('[name="exchange_rate"]');
			const hide_exchange_rate = () => $exchange_rate.closest('.eac-form-field').hide();
			const show_exchange_rate = () => $exchange_rate.closest('.eac-form-field').show();
			if (eac_admin_vars.base_currency === $currency.val()) {
				hide_exchange_rate();
			}
			// remove change event to avoid multiple event binding.
			$currency.on('change', function () {
				const currency = $(this).val();
				const rate = eac_admin_vars.currencies[currency]['rate'] || 1;
				$exchange_rate.val(rate);
				$exchange_rate.next('.eac-form-field__addon').text(currency);
				if (eac_admin_vars.base_currency === currency) {
					hide_exchange_rate();
				} else {
					show_exchange_rate();
				}
			});

			$form.addClass('enhanced');
		});
	}

	// Initialize UI.
	initializeUI();

	// Reinitialize UI when document body triggers 'eac-update-ui'.
	$(document.body).on('eac_update_ui', initializeUI);

	// Media Uploader.
	$('.eac-file-upload').filter(':not(.enhanced)').each(function () {
		const $this = $(this);
		const $button = $this.find('.eac-file-upload__button');
		const $value = $this.find('.eac-file-upload__value');
		const $preview = $this.find('.eac-file-upload__icon img');
		const $name = $this.find('.eac-file-upload__name a');
		const $size = $this.find('.eac-file-upload__size');
		const $remove = $this.find('a.eac-file-upload__remove');

		$button.on('click', function (e) {
			e.preventDefault();
			const frame = wp.media({
				title: $button.data('uploader-title'),
				multiple: false
			});
			frame.on('ready', function () {
				frame.uploader.options.uploader.params = {
					type: 'eac_file',
				};
			});
			frame.on('select', function () {
				const attachment = frame.state().get('selection').first().toJSON();
				const src = attachment.type === 'image' ? attachment.url : attachment.icon;
				$value.val(attachment.id);
				$preview.attr('src', src).show();
				$preview.attr('alt', attachment.filename);
				$name.text(attachment.filename).attr('href', attachment.url);
				$size.text(attachment.filesizeHumanReadable);
				$remove.show();
				$this.addClass('has--file');
			});
			frame.open();
		});

		$remove.on('click', function (e) {
			e.preventDefault();
			$this.removeClass('has--file');
			$value.val('');
			$preview.attr('src', '').hide();
			$name.text('').attr('href', '');
			$size.text('');
		});
	});
});
