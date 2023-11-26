/* global eac_admin_vars */
(function ($, window, document, undefined) {
	'use strict';
	const eac = {
		vars: window.eac_admin_vars || {},
		cache: {},
		/**
		 * Functions.
		 * Some useful functions.
		 *
		 * @return {Object} - The functions.
		 * @since 1.0.0
		 */
		fn: {
			getVar: function (key) {
				return eac.vars[key] || null;
			},
			setVar: function (key, value) {
				eac.vars[key] = value;
			},
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
			ajax: async function (data, method, options = {}) {
				method = method || 'GET';
				return $.ajax({
					url: options.url || eac.vars.ajax_url,
					method: method,
					data: data,
					contentType: 'application/json',
					headers: {
						'X-WP-Nonce': eac.vars.nonce,
					},
				});
			},
			getFormData: function (form) {
				const $form = $(form);
				const data = {};
				$(':input', $form).each(function () {
					const name = $(this).attr('name');
					const value = $(this).val();
					if (name) {
						data[name] = value;
					}
				});
				return data;
			},
			blockForm: function (form) {
				const $form = $(form);
				const $submitBtn = $('[form="' + $form.attr('id') + '"]');
				$form.find(':input').prop('disabled', true);
				$form.find(':submit').attr('disabled', 'disabled');
				$submitBtn.attr('disabled', 'disabled');
				$form.block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6,
					},
				});
			},
			disableSubmit: function (form) {
				const $form = $(form);
				const $submitBtn = $('[form="' + $form.attr('id') + '"]');
				$form.find(':submit').attr('disabled', 'disabled');
				$submitBtn.attr('disabled', 'disabled');
			},
			enableSubmit: function (form) {
				const $form = $(form);
				const $submitBtn = $('[form="' + $form.attr('id') + '"]');
				$form.find(':submit').removeAttr('disabled');
				$submitBtn.removeAttr('disabled');
			},
			unblockForm: function (form) {
				const $form = $(form);
				const $submitBtn = $('[form="' + $form.attr('id') + '"]');
				$form.find(':input').prop('disabled', false);
				$form.find(':submit').removeAttr('disabled');
				$submitBtn.removeAttr('disabled');
				$form.unblock();
			},
			ajaxForm: function (form, options = {}) {
				const $form = $(form);
				$form.data('form_data', eac.fn.getFormData(form));
				// Watch for the form submit event.
				$form.on('submit', function (e) {
					e.preventDefault();
					eac.fn.blockForm(form);
					// Lock the form and submit it.
					if ('yes' === $form.attr('locked')) {
						return false;
					}
					$form.attr('locked', 'yes');

					const data = eac.fn.getFormData(form);
					const url = $form.attr('action') || eac.vars.ajax_url;
					$.post(url, data, function (json) {
						if ('function' === typeof options.onSuccess) {
							options.onSuccess(json);
						}
						if (json.success) {
							$form.trigger('eac_form_submit_success', [json]);
							if ($form.closest('.eac-drawer').length) {
								$form
									.closest('.eac-drawer')
									.data('eac_drawer')
									.close();
							} else {
								eac.fn.redirect(json);
							}
						}
					}).always(function (json) {
						eac.fn.unblockForm(form);
						eac.fn.notify(json);
						$form.removeAttr('locked');
					});
				});
			},
			notify: function (message, type) {
				const options = {
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

				let html =
					'<div class="eac-notification notice notice-' +
					(type ? type : options.type) +
					' ' +
					(options.customClass ? options.customClass : '') +
					'">';
				html += message;
				html += '</div>';

				let offsetSum = options.offset.amount;
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

				const css = {
					position:
						options.appendTo === 'body' ? 'fixed' : 'absolute',
					margin: 0,
					'z-index': '9999',
					display: 'none',
					'min-width': options.minWidth,
					'max-width': options.maxWidth,
				};

				css[options.offset.from] = offsetSum + 'px';

				const $notice = $(html).css(css).appendTo(options.appendTo);

				switch (options.align) {
					case 'center':
						$notice.css({
							left: '50%',
							'margin-left':
								'-' + $notice.outerWidth() / 2 + 'px',
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
					$notice.css({ display: 'block', opacity: 1 });
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
				const ua = navigator.userAgent.toLowerCase(),
					isIE = ua.indexOf('msie') !== -1,
					version = parseInt(ua.substr(4, 2), 10);

				// Internet Explorer 8 and lower
				if (isIE && version < 9) {
					const link = document.createElement('a');
					link.href = url;
					document.body.appendChild(link);
					return link.click();
				}
				// All other browsers can use the standard window.location.href (they don't lose HTTP_REFERER like Internet Explorer 8 & lower does)
				window.location.href = url;
			},
			formatMoney: function (amount, code) {
				if ('undefined' === typeof amount || !amount) {
					return '';
				}
				if ('undefined' === typeof code || !code) {
					code = eac_admin_vars.base_currency_code;
				}
				const currency = eac.api.getCurrency(code);
				if (!currency) {
					return '';
				}

				const symbol = currency.symbol;
				const decimal = currency.decimal_separator;
				const thousand = currency.thousand_separator;
				const precision = currency.decimal_places;
				const position = currency.position;
				amount = amount.toFixed(precision);

				const number = amount.split('.');
				number[0] = number[0].split(/(?=(?:\d{3})+$)/).join(thousand);
				amount = number.join(decimal);
				switch (position) {
					case 'before':
						amount = symbol + amount;
						break;
					case 'after':
						amount = amount + symbol;
						break;
				}

				return amount;
			},
			unFormatMoney: function (amount, code) {
				if ('undefined' === typeof amount || !amount) {
					return '';
				}
				if (!code) {
					code = eac.fn.getVar('base_currency_code');
				}
				eac.api
					.getCurrency(code)
					.then((currency) => {
						const symbol = currency.symbol;
						const decimal = currency.decimal_separator || '.';
						const thousand = currency.thousand_separator || ',';
						const precision = currency.precision || 2;

						amount = amount.replace(symbol, '');
						amount = amount.replace(
							new RegExp(
								'[^0-9\\' +
									decimal +
									'\\' +
									thousand +
									'\\-\\+]',
								'g'
							),
							''
						);
						amount = amount.replace(thousand, '');
						amount = amount.replace(decimal, '.');
						amount = parseFloat(amount).toFixed(precision);
						return amount;
					})
					.catch((error) => {
						eac.fn.notice(error, 'error');
					});
			},
			inputPrice: function (element, code, options = {}) {
				if ('undefined' === typeof code || !code) {
					code = eac.fn.getVar('base_currency_code');
				}

				console.log('inputPrice', element, code, options);

				eac.api.getCurrency(code).then((currency) => {
					const position = currency.position || 'before';
					const config = $.extend(
						{
							alias: 'numeric',
							groupSeparator: currency.thousand_separator || ',',
							autoGroup: true,
							digits: currency.decimal_places || 2,
							radixPoint: currency.decimal_separator || '.',
							rightAlign: false,
							digitsOptional: false,
							allowMinus: true,
							placeholder: '0',
							autoUnmask: true,
							unmaskAsNumber: true,
							prefix:
								position === 'before' ? currency.symbol : '',
							suffix: position === 'after' ? currency.symbol : '',
						},
						options
					);

					$(element).inputmask('decimal', config);
				});
			},
			select2: function (element, options = {}) {
				// Check if select2 is loaded otherwise warn the user.
				if ('undefined' === typeof $.fn.select2) {
					console.warn('Select2 is not loaded.');
					return;
				}
				const $element = $(element);
				const defaults = {
					allowClear:
						($element.data('allow_clear') &&
							!$element.prop('multiple')) ||
						true,
					placeholder:
						$element.data('placeholder') ||
						$element.attr('placeholder') ||
						'',
					minimumInputLength: $element.data('minimum_input_length')
						? $element.data('minimum_input_length')
						: 0,
					ajax: {
						url: eac_admin_vars.ajax_url,
						dataType: 'json',
						delay: 250,
						method: 'POST',
						data: function (params) {
							return {
								term: params.term,
								action: $element.data('action'),
								type: $element.data('type'),
								_wpnonce: eac_admin_vars.search_nonce,
								exclude: $element.data('exclude'),
								include: $element.data('include'),
								limit: $element.data('limit'),
							};
						},
						processResults: function (data) {
							data.page = data.page || 1;
							return data;
						},
						cache: true,
					},
				};
				const config = $.extend(defaults, options);
				// if data action is set then use ajax.
				if (!$element.data('action')) {
					delete config.ajax;
				}

				// if the select2 is within a drawer, then set the parent to the drawer.
				if ($element.closest('.eac-form').length) {
					config.dropdownParent = $element.closest('.eac-form');
				}

				$element.select2(config).addClass('enhanced');
			},
			tooltip: function (element, options = {}) {
				// check if tooltip is loaded otherwise warn the user.
				if ('undefined' === typeof $.tooltip) {
					console.warn('jQuery UI is not loaded.');
					return;
				}
				const $element = $(element);
				const defaults = {
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
				};
				const config = $.extend(defaults, options);
				$element.tooltip(config);
			},
			datepicker: function (element, options = {}) {
				// check if datepicker is loaded otherwise warn the user.
				if ('undefined' === typeof $.datepicker) {
					console.warn('jQuery UI Datepicker is not loaded.');
					return;
				}

				const $element = $(element);
				const defaults = {
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					changeYear: true,
					yearRange: '-100:+0',
					showButtonPanel: true,
					closeText: 'Clear',
					onClose: function (dateText, inst) {
						if (
							$(window.event.srcElement).hasClass(
								'ui-datepicker-close'
							)
						) {
							$element.val('');
						}
					},
				};
				const config = $.extend(defaults, options);
				$element.datepicker(config).addClass('enhanced');
			},
			convertMoney: function (price, from, to) {
				if ('undefined' === typeof from || !from) {
					from = eac.fn.getVar('base_currency_code');
				}
				if ('undefined' === typeof to || !to) {
					to = eac.fn.getVar('quote_currency_code');
				}

				price = eac.fn.unFormatMoney(price, from);

				if (from === to) {
					return price;
				}
				eac.api.getExchangeRate(from).then((fromRate) => {
					eac.api.getExchangeRate(to).then((toRate) => {
						return (price / fromRate) * toRate;
					});
				});
			},
		},
		/**
		 * Functions.
		 * Some useful functions.
		 *
		 * @return {Object} - The functions.
		 * @since 1.0.0
		 */
		api: {
			fetch: async function (endpoint, data, method) {
				method = method || 'GET';
				const baseUrl = eac.vars.rest_url.replace(/\/+$/, '');
				const url = baseUrl + endpoint;
				try {
					return await $.ajax({
						url: url,
						method: method,
						data: data,
						contentType: 'application/json',
						beforeSend: function (xhr) {
							xhr.setRequestHeader(
								'X-WP-Nonce',
								eac.vars.rest_nonce
							);
						},
					});
				} catch (error) {
					const response =
						error?.responseJSON?.message || error?.statusText;

					throw new Error(response);
				}
			},
			getCurrency: async function (code) {
				code = code || eac.vars.base_currency_code;
				try {
					const cached = eac.fn.getCache(code, 'currencies');
					if (cached) {
						return cached;
					}
					console.log('fetching because not cached');
					console.log(code);
					console.log(eac.cache.currencies);
					const res = await eac.api.fetch(
						`/eac/v1/currencies/${code}`
					);
					eac.fn.setCache(code, res, 'currencies');
					return res;
				} catch (error) {
					throw new Error(error);
				}
			},
			getBaseCurrency: async function () {
				return await eac.api.getCurrency();
			},
			getExchangeRate: async function (code) {
				const currency = await eac.api.getCurrency(code);
				return currency?.exchange_rate || 1;
			},
			getAccount: async function (id) {
				try {
					const cached = eac.fn.getCache(id, 'accounts');
					if (cached) {
						return cached;
					}

					const res = await eac.api.fetch(`/eac/v1/accounts/${id}`);
					eac.fn.setCache(res.id, res, 'accounts');
					return res;
				} catch (error) {
					throw new Error(error);
				}
			},
			getCustomer: async function (id) {
				try {
					const cached = eac.fn.getCache(id, 'customers');
					if (cached) {
						return cached;
					}

					const res = await eac.api.fetch(`/eac/v1/customers/${id}`);
					eac.fn.setCache(res.id, res, 'customers');
					return res;
				} catch (error) {
					throw new Error(error);
				}
			},
			getVendor: async function (id) {
				try {
					const cached = eac.fn.getCache(id, 'vendors');
					if (cached) {
						return cached;
					}

					const res = await eac.api.fetch(`/eac/v1/vendors/${id}`);
					eac.fn.setCache(res.id, res, 'vendors');
					return res;
				} catch (error) {
					throw new Error(error);
				}
			},
		},
	};

	// Check if window.eac is defined. If not, define it. Otherwise, extend it.
	window.eac = window.eac || {};
	$.extend(window.eac, eac);
})(jQuery, window, document);
