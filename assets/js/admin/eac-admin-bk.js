/* global eac_admin_vars */
(function ($, window, document, undefined) {
	'use strict';
	var eac_admin = {
		get_account: function (account_id, callback, always) {
			var data = {
				action: 'eac_get_account',
				account_id: account_id,
				_wpnonce: eac_admin_vars.get_account_nonce,
			};
			$.post(eac_admin_vars.ajax_url, data, function (response) {
				if (response.success && true === response.success) {
					callback(response.data);
				}
			}).always(function () {
				if ('function' === typeof always) {
					always();
				}
			});
		},
		get_currency: function (currency_code, callback, always) {
			//setup cache, if found in cache, return it.
			var data = {
				action: 'eac_get_currency',
				currency_code: currency_code,
				_wpnonce: eac_admin_vars.get_currency_nonce,
			};
			$.post(eac_admin_vars.ajax_url, data, function (response) {
				if (response.success && true === response.success) {
					callback(response.data);
				}
			}).always(function () {
				if ('function' === typeof always) {
					always();
				}
			});
		},
		get_contact: function (contact_id, callback, always) {
			var data = {
				action: 'eac_get_contact',
				contact_id: contact_id,
				_wpnonce: eac_admin_vars.get_contact_nonce,
			};
			$.post(eac_admin_vars.ajax_url, data, function (response) {
				if (response.success && true === response.success) {
					callback(response.data);
				}
			}).always(function () {
				if ('function' === typeof always) {
					always();
				}
			});
		},
		input_mask: function ($el, currency) {
			var base = eac_admin_vars.base_currency;
			var data = currency || {};
			var decimal_separator = data.decimal_separator;
			var thousands_separator = data.thousands_separator;
			var precision = data.precision;
			var position = data.position;
			var symbol = data.symbol;

			$el.inputmask('decimal', {
				alias: 'numeric',
				groupSeparator: thousands_separator,
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
			$form.addClass('processing');
			$form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
		},
		unblock_form: function ($form) {
			var $submit = $('[form="' + $form.attr('id') + '"]');
			$submit.removeAttr('disabled');
			$form.removeClass('processing');
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

			$notice.on('timeout', function () {
				$notice.remove();
			});


			if (options.delay > 0) {
				setTimeout(function () {
					$notice.trigger('timeout');
				}, options.delay);
			}

			$notice.click(function () {
				$notice.trigger('timeout');
			});

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
		$('.eac-ajax-form :input[name="from_account_id"], .eac-ajax-form :input[name="account_id"]').on('change', function () {
			var $field = $(this);
			var $form = $(this).closest('form');
			var account_id = $(this).val();
			if (!account_id) {
				$(':input[name="currency_code"]', $form).val(eac_admin_vars.base_currency).trigger('change');
				return false;
			}
			eac_admin.block_form($form);
			eac_admin.get_account(account_id, function (account) {
				$(':input[name="currency_code"]', $form).val(account.currency_code).trigger('change');
			}, function () {
				eac_admin.unblock_form($form);
			});
		});

		// Account form.
		$('.eac-ajax-form :input[name="currency_code"]').on('change', function () {
			var currency_code = $(this).val();
			var $form = $(this).closest('form');
			if (!currency_code) {
				return;
			}
			eac_admin.block_form($form);
			eac_admin.get_currency(currency_code, function (currency) {
				var $exchange_rate = $(':input[name="exchange_rate"]', $form);
				eac_admin.input_mask($(':input[name="opening_balance"]', $form), currency);
				eac_admin.input_mask($(':input[name="amount"]', $form), currency);
				eac_admin.input_mask($(':input[name="price"]', $form), currency);
				if (currency.code === eac_admin_vars.base_currency) {
					$exchange_rate.prop('readonly', true);
					$exchange_rate.closest('.eac-form-field').addClass('display-none');
					$exchange_rate.val(1);
					$('.eac-form-field__addon:last-child', $exchange_rate.closest('.eac-form-field')).text(eac_admin_vars.base_currency);
				} else {
					$exchange_rate.prop('readonly', false);
					$exchange_rate.closest('.eac-form-field').removeClass('display-none');
					$exchange_rate.val(currency.exchange_rate);
					$('.eac-form-field__addon:last-child', $exchange_rate.closest('.eac-form-field')).text(currency.code);
				}
			}, function () {
				eac_admin.unblock_form($form);
			});
		});
		$('.eac-ajax-form :input[class*="eac-input-decimal"]').on('keyup change', function () {
			var val = $(this).val();
			val = val.replace(/[^0-9.]/g, '');
			$(this).val(val);
		});
		$('form.eac-document-form a.edit_billing_address').on('click', function (e) {
			e.preventDefault();
			var $form = $(this).closest('form');
			// if the item have display-none class then return.
			if ($(this).hasClass('display-none')) {
				return;
			}
			$(this).toggleClass('display-none');
			$('.billing-fields, .billing-data', $form).toggleClass('display-none');
			$('.load_billing_address', $form).toggle();
		});
		$('form.eac-document-form :input[name="contact_id"]').on('change', function () {
			var $form = $(this).closest('form');
			var contact_id = $(this).val();
			if (!contact_id) {
				return;
			}
			eac_admin.block_form($form);
			eac_admin.get_contact(contact_id, function (response) {
				// loop through the response and set the values.
				$.each(response, function (key, value) {
					$(':input[name="billing_' + key + '"]', $form).val(value).trigger('change');
				});
			}, function () {
				eac_admin.unblock_form($form);
				$form.trigger('update');
			});
		});
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
		$('#eac_year_start_date').datepicker({
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

	// Init dropdown.
	$(document).on('click', '.eac-dropdown .eac-dropdown__button', function (e) {
		e.preventDefault();
		$(this).closest('.eac-dropdown').toggleClass('is--open');
	});
	// Close dropdown on click outside.
	$(document).on('click', function (e) {
		if ($(e.target).closest('.eac-dropdown__button').length === 0) {
			$('.eac-dropdown').removeClass('is--open');
		}
	});

	// Document item update.
	$(document).on('change', 'form.eac-document-form :input[class*="trigger-update"]', function () {
		var $form = $(this).closest('form');
		$form.trigger('update');
	});

	// Document item remove.
	$(document).on('click', 'form.eac-document-form a.remove-line-item', function (e) {
		e.preventDefault();
		var $form = $(this).closest('form');
		$(this).closest('tr').remove();
		$form.trigger('update');
	});

	// Document item add.
	$(document).on('click', 'form.eac-document-form a.add-line-item', function (e) {
		e.preventDefault();
		var $form = $(this).closest('form');
		$('#new-line-item', $form).removeClass('display-none');
	});

	// Force update.
	$(document).on('click', 'form.eac-document-form a.calculate-totals', function (e) {
		e.preventDefault();
		var $form = $(this).closest('form');
		// append a random number to the url to force update.
		$form.data('hash', '');
		$form.trigger('update');
	});

	// Update form.eac-document-form
	$(document).on('update', 'form.eac-document-form', function () {
		var $form = $(this);
		// build data object from form.
		var data = {};
		$(':input', $form).each(function () {
			var name = $(this).attr('name');
			var value = $(this).val();
			if (name) {
				data[name] = value;
			}
		});

		// Remove empty values.
		var hash = JSON.stringify(data);
		if (hash === $form.data('hash')) {
			return;
		}
		$form.data('hash', hash);
		$form.trigger('block');
		data.action = 'eac_calculate_invoice';
		$form.load(eac_admin_vars.ajax_url, data, function () {
			$form.trigger('unblock');
			eac_init();
		}).trigger('unblock');
	});

	// Submit form.
	// $(document).on('submit', '.eac-ajax-form', function (e) {
	// 	e.preventDefault();
	// 	var $form = $(this);
	// 	var data = {};
	// 	$(':input', $form).each(function () {
	// 		var name = $(this).attr('name');
	// 		var value = $(this).val();
	// 		if (name) {
	// 			data[name] = value;
	// 		}
	// 	});
	// 	// var hash = JSON.stringify(data);
	// 	// if (hash === $form.data('hash')) {
	// 	// 	return;
	// 	// }
	// 	// $form.data('hash', hash);
	// 	var $submit = $('[form="' + $form.attr('id') + '"]');
	// 	// if submit button is disabled, do nothing.
	// 	if ($submit.is(':disabled')) {
	// 		return;
	// 	}
	//
	// 	// block the form.
	// 	eac_admin.block_form($form);
	//
	// 	$.post(eac_admin_vars.ajax_url, data, function (json) {
	// 		if (json.success) {
	// 			if ($form.closest('.eac-drawer').length) {
	// 				$form.closest('.eac-drawer').data('eac_drawer').close();
	// 			} else {
	// 				eac_admin.redirect(json);
	// 			}
	// 		}
	// 	}).always(function (json) {
	// 		eac_admin.unblock_form($form);
	// 		eac_admin.show_notification(json);
	// 	});
	// });

	// Confirm on delete.
	// $(document).on('click', 'a.del, a.delete', function () {
	// 	if (!confirm(eac_admin_vars.i18n.confirm_delete)) {
	// 		return false;
	// 	}
	// });


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


