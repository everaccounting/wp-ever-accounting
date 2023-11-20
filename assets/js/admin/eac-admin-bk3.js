/* global eac_admin_vars */
(function ($, window, document, undefined) {
	'use strict';
	window.eac_admin_vars = window.eac_admin_vars || {};
	window.eac = window.eac || {};
	$.extend(window.eac, {
		vars:window.eac_admin_vars || {},
		cache: {},
		fn:{
			getCache: function (key, group) {
				group = group || 'default';
				if (!(group in eac.cache)) {
					eac.cache[group] = {};
				}
				return eac.cache[group][key];
			},
			setCache: function (key, value, group) {
				group = group || 'default';
				if (!(group in eac.cache)) {
					eac.cache[group] = {};
				}
				eac.cache[group][key] = value;
			},
			removeCache: function (key, group) {
				group = group || 'default';
				if (group in eac.cache) {
					delete eac.cache[group][key];
				}
			},
			flushCache: function (group) {
				group = group || 'default';
				if (group in eac.cache) {
					delete eac.cache[group];
				}
			},
			notify: function (message, type) {
				var options = {
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
			confirm: function (message, callback) {},
			confirmDelete: function (message, callback) {},
			ajax: function (data, callback, always, options={}){
				const method = options.method || 'POST';
				const dataType = options.dataType || 'json';
				const url = options.url || eac.vars.ajaxurl;
				$.ajax({
					method: method,
					dataType: dataType,
					url: url,
					data: data,
					success: function (response) {
						if (callback) {
							callback(response);
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						eac.fn.notify(errorThrown, 'error');
					},
					always: function (response) {
						if (always) {
							always(response);
						}
					}
				});
			},
			fetch: function (endpoint, callback, params={}, options={}) {
				const method = options.method || 'GET';
				const dataType = options.dataType || 'json';
				const body = options.body || {};
				const url = options.url || eac.fn.formatURL(eac.vars.rest_url + endpoint, params);
				$.ajax({
					method: method,
					dataType: dataType,
					url: url,
					data: body,
					beforeSend: function (xhr) {
						xhr.setRequestHeader('X-WP-Nonce', eac.vars.rest_nonce);
					},
					success: function (response) {
						if (callback) {
							callback(response);
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						eac.fn.notify(errorThrown, 'error');
					},
					always: function (response) {
						if (options.always) {
							options.always(response);
						}
					}
				});
			},
			getFormData: function (form) {
				const $form = $(form);
				const data = {};
				$(':input', $form).each(function () {
					var name = $(this).attr('name');
					var value = $(this).val();
					if (name) {
						data[name] = value;
					}
				});
				return data;
			},
			formatURL: function (url, params={}) {
				var queryString = '';
				// clean up the url.
				url = url.replace(/\/+$/, '');

				// Check if the URL already contains a query string
				if (url.indexOf('?') !== -1) {
					queryString += '&';
				} else {
					queryString += '?';
				}

				for (var key in params) {
					if (params.hasOwnProperty(key)) {
						queryString += encodeURIComponent(key) + '=' + encodeURIComponent(params[key]) + '&';
					}
				}

				// Remove the extra '&' character at the end of the query string
				queryString = queryString.slice(0, -1);

				return url + queryString;
			},
			blockForm: function (form) {
				var $form = $(form);
				var $submit_button = $('[form="' + $form.attr('id') + '"]');
				$form.find(':input').prop('disabled', true);
				$form.find(':submit').attr('disabled', 'disabled');
				$submit_button.attr('disabled', 'disabled');
				$form.block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6,
					},
				});
			},
			unblockForm: function (form) {
				var $form = $(form);
				var $submit_button = $('[form="' + $form.attr('id') + '"]');
				$form.find(':input').prop('disabled', false);
				$form.find(':submit').removeAttr('disabled');
				$submit_button.removeAttr('disabled');
				$form.unblock();
			},
			maskInput: function (input, options={}) {
				var $input = $(input);
				var currency = options.currency || eac.fn.getBaseCurrency().
				$input.inputmask('decimal', {
					alias: 'numeric',
					groupSeparator: currency.thousand_separator || ',',
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
			getBaseCurrency: async function () {
				await eac.fn.getCurrency(eac.vars.base_currency, function (currency) {
					return currency;
				});
			},
			convertCurrency: function (amount, from, to, callback) {

			},
			getAccount: function (id, callback) {
				const account = eac.fn.getCache( id, 'accounts' );
				if ( account ) {
					return callback( account );
				}
				eac.fn.fetch('accounts/' + id, function (response) {
					eac.fn.setCache( response.id, response, 'accounts' );
					callback(response);
				});
			},
			getCurrency: async function (id) {
				// This is an async function. First check if the currency is in the cache.
				// If it is, return it. If not, fetch it from the server and return it after caching it.
				// Even though this is an async function, it is not awaited. The caller should await it.

				const currency = eac.fn.getCache( id, 'currencies' );
				if ( currency ) {
					return currency;
				}

				// const response = await eac.fn.promise(function (resolve, reject) {
				// 	eac.fn.fetch('currencies/' + id, function (response) {
				// 		eac.fn.setCache( response.id, response, 'currencies' );
				// 		resolve(response);
				// 	});
				// }



				// const currency = eac.fn.getCache( id, 'currencies' );
				// if ( currency ) {
				// 	return callback( currency );
				// }
				// eac.fn.fetch('currencies/' + id, function (response) {
				// 	eac.fn.setCache( response.id, response, 'currencies' );
				// 	if (callback) {
				// 		callback(response);
				// 	}
				// });
			},
			promise: function (callback) {
				return new Promise(function (resolve, reject) {
					callback(resolve, reject);
				});
			}
		}
	});

	window.eac = eac;

	$(document).ready(function () {
		$(document)
			.on('init-select2', function(){
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
			})
			.on('init-datepicker', function () {
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
			})
			.on('init-tooltip', function () {
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
			})
			.on('submit', '.eac-ajax-form', function (e) {
				e.preventDefault();
				var $form = $(this);
				var data = eac.fn.getFormData($form);
				eac.fn.blockForm($form);

				$.post(eac_admin_vars.ajax_url, data, function (json) {
					if (json.success) {
						if ($form.closest('.eac-drawer').length) {
							$form.closest('.eac-drawer').data('eac_drawer').close();
						} else {
							eac.fn.redirect(json);
						}
					}
				}).always(function (json) {
					eac.fn.unblockForm($form);
					eac.fn.notify(json);
				});
			})
			.on('click', 'a.del, a.delete', function (e) {
			if (!confirm(eac_admin_vars.i18n.confirm_delete)) {
				e.stopPropagation();
				return false;
			}
		})
			.on('block', 'form', function () {
				eac.fn.blockForm(this);
			})
			.on('unblock', 'form', function () {
				eac.fn.unblockForm(this);
			})
			.on('eac-drawer-ready', function () {
				eac_init();
			});

		// Common Form.
		$(document).on('init-form', '.eac-ajax-form', function () {
			// var $form = $(this);
			// $(':input[name="currency_code"]', $form).on('change', function () {
			// 	var currency_code = $(this).val();
			// 	var $exchange_rate = $(':input[name="exchange_rate"]', $form);
			// 	if( !currency_code || currency_code === eac_admin_vars.base_currency ){
			// 		$exchange_rate.val(1).trigger('change');
			// 		$exchange_rate.closest('.eac-form-field').hide();
			// 		$('.eac-form-field__addon:last-child', $exchange_rate.closest('.eac-form-field')).text(eac_admin_vars.base_currency);
			// 		return;
			// 	}
			//
			// 	eac.fn.blockForm($form);
			// 	eac.fn.getCurrency(currency_code, function (currency) {
			// 		$exchange_rate.val(currency.exchange_rate).trigger('change');
			// 		$exchange_rate.closest('.eac-form-field').show();
			// 		$('.eac-form-field__addon:last-child', $exchange_rate.closest('.eac-form-field')).text(currency.currency_code);
			// 		eac.fn.unblockForm($form);
			// 	});
			// }).trigger('change');
		});

		// Item Form.
		$(document).on('init-form', '#eac-item-form', function () {
			var $form = $(this);
			$(':input[name="taxable"]', $form).on('change', function () {
				var is_taxable = $(this).val() === '1';
				var taxes = $('#field-tax_ids', $form);
				if (is_taxable) {
					taxes.show();
				}else{
					taxes.hide();
				}
			}).trigger('change');
		});

		// Payment Form.
		$(document).on('init-form', '#eac-payment-form, #eac-expense-form', function () {
			// var $form = $(this);
			// $(':input[name="account_id"]', $form).on('change', function () {
			// 	var account_id = $(this).val();
			// 	if (!account_id) {
			// 		$(':input[name="currency_code"]', $form).val(eac_admin_vars.base_currency).trigger('change');
			// 		return false;
			// 	}
			// 	eac.fn.blockForm($form);
			// 	eac.fn.getAccount(account_id, function (account) {
			// 		$(':input[name="currency_code"]', $form).val(account.currency_code).trigger('change');
			// 		eac.fn.unblockForm($form);
			// 	});
			// }).trigger('change');

			// $(':input[name="currency_code"]', $form).on('change', function () {
			// 	var currency_code = $(this).val();
			//
			// 	if( !currency_code || currency_code === eac_admin_vars.base_currency ){
			// 		$(':input[name="exchange_rate"]', $form).val(1).trigger('change');
			// 		$('#field-exchange_rate', $form).hide();
			// 		return;
			// 	}
			// 	eac.fn.blockForm($form);
			// 	eac.fn.getCurrency(currency_code, function (currency) {
			// 		$(':input[name="exchange_rate"]', $form).val(currency.exchange_rate).trigger('change');
			// 		$('#field-exchange_rate', $form).show();
			// 		eac.fn.unblockForm($form);
			// 	});
			// });


			// $(':input[name="account_id"]', $form).on('change', function () {
			// 	var account_id = $(this).val();
			// 	if (!account_id) {
			// 		return;
			// 	}
			// 	eac.fn.getAccount(account_id, function (account) {
			// 		$(':input[name="currency_code"]', $form).val(account.currency_code).trigger('change');
			// 	});
			// }).trigger('change');
			// $(':input[name="currency_code"]', $form).on('change', function () {
			// 	var currency_code = $(this).val();
			// 	// if currency_code is empty, then return.
			// 	if (!currency_code) {
			// 		return;
			// 	}
			// 	if( currency_code === eac_admin_vars.base_currency_code ){
			// 		$(':input[name="exchange_rate"]', $form).val(1);
			// 		$('#field-exchange_rate', $form).hide();
			// 	}else {
			// 		$('#field-exchange_rate', $form).show();
			// 		eac.fn.getCurrency(currency_code, function (currency) {
			// 			$(':input[name="exchange_rate"]', $form).val(currency.exchange_rate);
			// 		});
			// 	}
			// }).trigger('change');
		});

		// function eac_init() {
		// 	$(document).trigger('init-select2');
		// 	$(document).trigger('init-datepicker');
		// 	$(document).trigger('init-tooltip');
		// 	$('.eac-ajax-form').filter(':not(.initiated)').each(function () {
		// 		var $form = $(this);
		// 		$form.addClass('initiated');
		// 		$form.trigger('init-form');
		// 	});
		// }
		// eac_init();

	});

}(jQuery, window, document));


