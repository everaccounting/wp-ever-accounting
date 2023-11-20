/* global eac_admin_vars */
(function ($, window, document, undefined) {
	'use strict';
	window.eac_admin_vars = window.eac_admin_vars || {};
	window.eac = window.eac || {};
	Object.assign(eac, {
		vars: window.eac_admin_vars || {},
		cache: {},
		/**
		 * Functions.
		 * Some useful functions.
		 *
		 * @returns {Object} - The functions.
		 * @since 1.0.0
		 */
		fn: {
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
			fetch: async function (endpoint, data, method) {
				method = method || 'GET';
				const base_url = eac.vars.rest_url.replace(/\/+$/, '');
				const url = base_url + endpoint;
				return new Promise((resolve, reject) => {
					$.ajax({
						url: url,
						method: method,
						data: JSON.stringify(data),
						contentType: 'application/json',
						beforeSend: function (xhr) {
							xhr.setRequestHeader(
								'X-WP-Nonce',
								eac.vars.rest_nonce
							);
						},
						success: function (response) {
							resolve(response);
						},
						error: function (xhr, status, error) {
							reject(new Error(error));
						},
					});
				});
			},
			ajax: async function (data, method, options = {}) {
				method = method || 'GET';
				return $.ajax({
					url: options.url || eac.vars.ajax_url,
					method: method,
					data: JSON.stringify(data),
					contentType: 'application/json',
					headers: {
						'X-WP-Nonce': eac.vars.nonce,
					},
				});
			},
			getCurrency: async function (currency) {
				// If the currency is already cached, return it promise.
				const cached_currency = eac.fn.getCache(currency, 'currencies');
				if (cached_currency) {
					return Promise.resolve(cached_currency);
				}
				return eac.fn
					.fetch(`/eac/v1/currencies/${currency}`)
					.then((currency) => {
						eac.fn.setCache(currency.code, currency, 'currencies');
						return currency;
					});
			},
			getBaseCurrency: async function () {
				return eac.fn.getCurrency(eac_admin_vars.base_currency);
			},
			getExchangeRate: async function (code) {
				const currency = await eac.fn.getCurrency(code);
				return currency.exchange_rate || 1;
			},
			getAccount: async function (id) {
				// If the account is already cached, return it promise.
				const cached_account = eac.fn.getCache(id, 'accounts');
				if (cached_account) {
					return Promise.resolve(cached_account);
				}
				return eac.fn
					.fetch(`/eac/v1/accounts/${id}`)
					.then((account) => {
						eac.fn.setCache(account.id, account, 'accounts');
						return account;
					});
			},
			getCustomer: async function (id) {
				// If the customer is already cached, return it promise.
				const cached_customer = eac.fn.getCache(id, 'customers');
				if (cached_customer) {
					return Promise.resolve(cached_customer);
				}
				return eac.fn
					.fetch(`/eac/v1/customers/${id}`)
					.then((customer) => {
						eac.fn.setCache(customer.id, customer, 'customers');
						return customer;
					});
			},
			getVendor: async function (id) {
				// If the vendor is already cached, return it promise.
				const cached_vendor = eac.fn.getCache(id, 'vendors');
				if (cached_vendor) {
					return Promise.resolve(cached_vendor);
				}
				return eac.fn.fetch(`/eac/v1/vendors/${id}`).then((vendor) => {
					eac.fn.setCache(vendor.id, vendor, 'vendors');
					return vendor;
				});
			},
			getItem: async function (id) {
				// If the item is already cached, return it promise.
				const cached_item = eac.fn.getCache(id, 'items');
				if (cached_item) {
					return Promise.resolve(cached_item);
				}
				return eac.fn.fetch(`/eac/v1/items/${id}`).then((item) => {
					eac.fn.setCache(item.id, item, 'items');
					return item;
				});
			},
			getTax: async function (id) {
				// If the tax is already cached, return it promise.
				const cached_tax = eac.fn.getCache(id, 'taxes');
				if (cached_tax) {
					return Promise.resolve(cached_tax);
				}
				return eac.fn.fetch(`/eac/v1/taxes/${id}`).then((tax) => {
					eac.fn.setCache(tax.id, tax, 'taxes');
					return tax;
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
					position:
						options.appendTo === 'body' ? 'fixed' : 'absolute',
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
			formatMoney: function (amount, code) {
				if ('undefined' === typeof amount || !amount) {
					return '';
				}
				if ('undefined' === typeof code || !code) {
					code = eac_admin_vars.base_currency;
				}
				var currency = eac.fn.getCurrency(code);
				if (!currency) {
					return '';
				}

				var symbol = currency.symbol;
				var decimal = currency.decimal_separator;
				var thousand = currency.thousand_separator;
				var precision = currency.decimal_places;
				var position = currency.position;
				amount = amount.toFixed(precision);

				var number = amount.split('.');
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
			unFormatMoney: function (amount, currency) {
				if ('undefined' === typeof amount || !amount) {
					return '';
				}
				if ('undefined' === typeof currency || !currency) {
					currency = eac_admin_vars.base_currency;
				}
				var symbol = eac.fn.getCurrency(currency).symbol;
				var decimal = eac.fn.getCurrency(currency).decimal_separator;
				var thousand = eac.fn.getCurrency(currency).thousand_separator;
				var precision = eac.fn.getCurrency(currency).decimal_places;
				var position = eac.fn.getCurrency(currency).position;
				amount = amount.replace(symbol, '');
				amount = amount.replace(thousand, '');
				amount = amount.replace(decimal, '.');
				amount = parseFloat(amount);
				return amount;
			},
			priceInput: function (element, code, options = {}) {
				if (!code) {
					code = eac.fn.getVar('base_currency_code');
				}

				var currency = eac.fn.getCurrency(code);
				var position = currency.position || 'before';
				var config = $.extend(
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
						onUnMask: function (maskedValue, unmaskedValue) {
							return eac.fn.unFormatMoney(unmaskedValue, code);
						},
						prefix: position === 'before' ? currency.symbol : '',
						suffix: position === 'after' ? currency.symbol : '',
					},
					options
				);

				$(element).inputmask('decimal', config);
			},
			select2: function (element, options = {}) {
				// Check if select2 is loaded otherwise warn the user.
				if ('undefined' === typeof $.fn.select2) {
					console.warn('Select2 is not loaded.');
					return;
				}
				var $element = $(element);
				var defaults = {
					allowClear: $element.data('allow_clear') && !$element.prop('multiple') || true,
					placeholder: $element.data('placeholder') || $element.attr('placeholder') || '',
					minimumInputLength: $element.data('minimum_input_length') ? $element.data('minimum_input_length') : 0,
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
						cache: true
					}
				};
				var config = $.extend(defaults, options);
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
			datePicker: function (element, options = {}) {
				// check if datepicker is loaded otherwise warn the user.
				if ('undefined' === typeof $.datepicker) {
					console.warn('jQuery UI Datepicker is not loaded.');
					return;
				}

				var $element = $(element);
				var defaults = {
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					changeYear: true,
					yearRange: '-100:+0',
					showButtonPanel: true,
					closeText: 'Clear',
					onClose: function (dateText, inst) {
						if ($(window.event.srcElement).hasClass('ui-datepicker-close')) {
							$element.val('');
						}
					},
				};
				var config = $.extend(defaults, options);
				$element.datepicker(config).addClass('enhanced');
			}
		},
	});
})(jQuery, window, document);
