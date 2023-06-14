/* global eac_admin_vars */
(function ($, window, document, undefined) {
	'use strict';
	var eac_admin = {
		get_conversion_rate: function (currency) {
			var base = eac_admin_vars.base_currency;
			var data = eac_admin_vars.currencies[currency] || eac_admin_vars.currencies[base];
			return data.rate;
		},
		get_currency_data: function (currency) {
			var base = eac_admin_vars.base_currency;
			return eac_admin_vars.currencies[currency] || eac_admin_vars.currencies[base];
		},
		input_mask: function ($el, currency) {
			var base = eac_admin_vars.base_currency;
			var data = eac_admin_vars.currencies[currency] || eac_admin_vars.currencies[base];
			var decimal_separator = data.decimal_separator;
			var thousand_separator = data.thousand_separator;
			var precision = data.precision;
			var position = data.position;
			var symbol = data.symbol;

			$el.inputmask('decimal', {
				alias: 'numeric',
				groupSeparator: thousand_separator,
				autoGroup: true,
				digits: precision,
				radixPoint: decimal_separator,
				digitsOptional: false,
				allowMinus: true,
				rightAlign: false,
				prefix: position === 'before' ? symbol : '',
				suffix: position === 'after' ? symbol : '',
			});
		},
		block_form: function ($form) {
			var $submit = $('[form="' + $form.attr('id') + '"]');
			$submit.removeAttr('disabled');
			$form.unblock();
		},
		unblock_form: function ($form) {
			var $submit = $('[form="' + $form.attr('id') + '"]');
			$submit.removeAttr('disabled');
			$form.unblock();
		},
		redirect: function (url) {
			if ('object' === typeof url) {
				if ('data' in url) {
					url = url.data;
				}

				if (!('redirect' in url)) {
					return;
				}
				url = url.redirect;
			}

			if (!url) {
				return;
			}
			url = url.trim();
			if (!url) {
				return false;
			}
			var ua = navigator.userAgent.toLowerCase(),
				isIE = ua.indexOf('msie') !== -1,
				version = parseInt(ua.substr(4, 2), 10);

			// Internet Explorer 8 and lower
			if (isIE && version < 9) {
				var link = document.createElement('a');
				link.href = url;
				document.body.appendChild(link);
				return link.click();
			}
			// All other browsers can use the standard window.location.href (they don't lose HTTP_REFERER like Internet Explorer 8 & lower does)
			window.location.href = url;
		},
		show_notification: function (message, type, options) {
			var defaults = {
				appendTo: 'body',
				stack: false,
				customClass: false,
				type: 'success',
				offset: {
					from: 'bottom',
					amount: 50,
				},
				align: 'right',
				minWidth: 250,
				maxWidth: 450,
				delay: 4000,
				spacing: 10,
			};
			if ('object' === typeof message) {
				if ('data' in message) {
					if (message.success && true === message.success) {
						type = 'success';
					} else {
						type = 'error';
					}
					message = message.data;
				}

				if (!('message' in message)) {
					return;
				}

				message = message.message;
			}
			if (!message) {
				return;
			}
			message = message.trim();
			if (!message) {
				return false;
			}
			options = $.extend(
				true,
				{},
				defaults,
				options
			);

			var html =
				'<div class="eac-notification notice notice-' +
				(type ? type : options.type) +
				' ' +
				(options.customClass ? options.customClass : '') +
				'">';
			html += message;
			html += '</div>';

			var offsetSum = options.offset.amount;
			if (!options.stack) {
				$('.eac-notification').each(function () {
					return (offsetSum = Math.max(
						offsetSum,
						parseInt($(this).css(options.offset.from)) +
						this.offsetHeight +
						options.spacing
					));
				});
			} else {
				$(options.appendTo)
					.find('.eac-notification')
					.each(function () {
						return (offsetSum = Math.max(
							offsetSum,
							parseInt($(this).css(options.offset.from)) +
							this.offsetHeight +
							options.spacing
						));
					});
			}

			var css = {
				position: options.appendTo === 'body' ? 'fixed' : 'absolute',
				margin: 0,
				'z-index': '9999',
				display: 'none',
				'min-width': options.minWidth,
				'max-width': options.maxWidth,
			};

			css[options.offset.from] = offsetSum + 'px';

			var $notice = $(html).css(css).appendTo(options.appendTo);

			switch (options.align) {
				case 'center':
					$notice.css({
						left: '50%',
						'margin-left': '-' + $notice.outerWidth() / 2 + 'px',
					});
					break;
				case 'left':
					$notice.css('left', '20px');
					break;
				default:
					$notice.css('right', '20px');
			}

			if ($notice.fadeIn) {
				$notice.fadeIn();
			} else {
				$notice.css({display: 'block', opacity: 1});
			}

			function removeAlert() {
				$.eac_notification.remove($notice);
			}

			if (options.delay > 0) {
				setTimeout(removeAlert, options.delay);
			}

			$notice.click(removeAlert);

			return $notice;
		},
	};

	// Init select2.
	$(document).on('init-select2', function () {
		$(':input.eac-select2').filter(':not(.enhanced)').each(function () {
			var select2_args = {
				allowClear: $(this).data('allow_clear') && !$(this).prop('multiple') || true,
				placeholder: $(this).data('placeholder') || $(this).attr('placeholder') || '',
				minimumInputLength: $(this).data('minimum_input_length') ? $(this).data('minimum_input_length') : 0,
				ajax: {
					url: eac_admin_vars.ajax_url,
					dataType: 'json',
					delay: 250,
					method: 'POST',
					data: function (params) {
						return {
							term: params.term,
							action: $(this).data('action'),
							type: $(this).data('type'),
							_wpnonce: eac_admin_vars.search_nonce,
							exclude: $(this).data('exclude'),
							include: $(this).data('include'),
							limit: $(this).data('limit'),
						};
					},
					processResults: function (data) {
						data.page = data.page || 1;
						return data;
					},
					cache: true
				}
			};
			// if data action is set then use ajax.
			if (!$(this).data('action')) {
				delete select2_args.ajax;
			}

			// if the select2 is within a drawer, then set the parent to the drawer.
			if ($(this).closest('.eac-form').length) {
				select2_args.dropdownParent = $(this).closest('.eac-form');
			}
			$(this).select2(select2_args).addClass('enhanced');
		});
	});

	// Init currency.
	$(document).on('init-form', function () {
		// Account form.
		$('.eac-ajax-form :input[name="currency"]').on('change', function () {
			var currency = $(this).val();
			var $form = $(this).closest('form');
			var $conversion_rate = $(':input[name="conversion_rate"]', $form);
			eac_admin.input_mask($(':input[name="opening_balance"]', $form), currency);
			eac_admin.input_mask($(':input[name="amount"]', $form), currency);
			eac_admin.input_mask($(':input[name="price"]', $form), currency);
			if ( currency === eac_admin_vars.base_currency ) {
				$conversion_rate.prop('readonly', true);
				$conversion_rate.closest('.eac-form-field').addClass('display-none');
				$conversion_rate.val(1);
				$('.eac-form-field__addon:last-child', $conversion_rate.closest('.eac-form-field')).text(eac_admin_vars.base_currency);
			}else {
				$conversion_rate.prop('readonly', false);
				$conversion_rate.closest('.eac-form-field').removeClass('display-none');
				$conversion_rate.val(eac_admin.get_conversion_rate(currency));
				console.log($('.eac-form-field__addon:last-child', $conversion_rate).text());
				$('.eac-form-field__addon:last-child', $conversion_rate.closest('.eac-form-field')).text(currency);
			}
		}).trigger('change');
		$('.eac-ajax-form :input[name="from_account_id"], .eac-ajax-form :input[name="account_id"]').on('change', function () {
			var $form = $(this).closest('form');
			var account_id = $(this).val();
			if (!account_id) {
				$(':input[name="currency"]', $form).val(eac_admin_vars.base_currency).trigger('change');
				return false;
			}
			eac_admin.block_form($form);
			var data = {
				action: 'eac_get_account',
				account_id: account_id,
				_wpnonce: eac_admin_vars.get_account_nonce,
			};
			$.post(eac_admin_vars.ajax_url, data, function (response) {
				console.log(response);
				if (response.success) {
					var currency = response.data.currency;
					$(':input[name="currency"]', $form).val(currency).trigger('change');
				}
			}).always(function () {
				eac_admin.unblock_form($form);
			});
		}).trigger('change');
	});

	// Init datepicker.
	$(document).on('init-datepicker', function () {
		$(':input.eac-datepicker').filter(':not(.enhanced)').each(function () {
			var datepicker_args = {
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				yearRange: '-100:+0',
			};
			$(this).datepicker(datepicker_args).addClass('enhanced');
		});
		$('#eac_financial_year_start').datepicker({
			dateFormat: 'mm-dd',
			changeMonth: true,
			changeYear: false,
			yearRange: '-100:+0',
		});
	});

	// Init tooltip.
	$(document).on('init-tooltip', function () {
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
	});

	// Init drawer.
	$(document).on('eac-drawer-ready', function () {
		eac_init();
	});

	// Submit form.
	$(document).on('submit', '.eac-ajax-form', function (e) {
		e.preventDefault();
		var $form = $(this);
		var data = $form.serializeAssoc();
		var $submit = $('[form="' + $form.attr('id') + '"]');
		// if submit button is disabled, do nothing.
		if ($submit.is(':disabled')) {
			return;
		}

		// block the form.
		eac_admin.block_form($form);

		$.post(eac_admin_vars.ajax_url, data, function (json) {
			if (json.success) {
				if ($form.closest('.eac-drawer').length) {
					$form.closest('.eac-drawer').data('eac_drawer').close();
				} else {
					eac_admin.redirect(json);
				}
			}
		}).always(function (json) {
			eac_admin.unblock_form($form);
			eac_admin.show_notification(json);
		});
	});

	function eac_init() {
		$(document).trigger('init-select2');
		$(document).trigger('init-datepicker');
		$(document).trigger('init-tooltip');
		$(document).trigger('init-form');
	}

	// Trigger events.
	$(document).ready(function () {
		eac_init();
	});

}(jQuery, window, document));


