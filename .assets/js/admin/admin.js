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
			frame.on( 'ready', function () {
				frame.uploader.options.uploader.params = {
					type: 'eac_file',
				};
			} );
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
//
// (function (document, window, $) {
// 	'use strict';
//
// 	var app = {
// 		events: {
// 			'click .wp-heading-inline': 'addItem',
// 		},
//
// 		addItem: function (e) {
// 			e.preventDefault();
// 			console.log('--Item added--');
// 			console.log(this);
// 			console.log('Item added');
// 		},
//
//
// 		init: function () {
// 			var events = this.events || {};
// 			for (var key in events) {
// 				var method = events[key];
// 				if (typeof method !== 'function') method = this[method];
// 				if (!method) continue;
// 				var match = key.match(/^(\S+)\s*(.*)$/);
// 				var eventName = match[1];
// 				var selector = match[2];
// 				$(document).on(eventName, selector, method.bind(this));
// 			}
// 		},
// 	};
//
// 	// app.init();
//
// }(document, window, jQuery));
//
// (function (document, window, $) {
// 	'use strict';
//
// 	var app = {
// 		el: document,
// 		init: function () {
// 			console.log('App initialized');
// 			$(this.el).on('click', '.wp-heading-inline', this.bind(this.addItem));
// 		},
//
// 		addItem: function (e) {
// 			e.preventDefault();
// 			console.log('--Item added--');
// 			console.log(this);
// 			console.log('Item added');
// 		},
// 	};
//
// 	app.init();
//
// }(document, window, jQuery));

