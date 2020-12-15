window.eaccounting = window.eaccounting || {}

jQuery(function ($) {
	'use strict';

	/**
	 * A nifty plugin to converting form to serialize object
	 */
	$.fn.serializeObject = function () {
		var o = {};
		var a = this.serializeArray();
		$.each(a, function () {
			if (o[this.name] !== undefined) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};

	/**
	 * A plugin for converting form to serializeAssoc
	 * @returns {{}}
	 */
	$.fn.serializeAssoc = function () {
		var data = {};
		$.each(this.serializeArray(), function (key, obj) {
			var a = obj.name.match(/(.*?)\[(.*?)\]/);
			if (a !== null) {
				var subName = a[1];
				var subKey = a[2];

				if (!data[subName]) {
					data[subName] = [];
				}

				if (!subKey.length) {
					subKey = data[subName].length;
				}

				if (data[subName][subKey]) {
					if (Array.isArray(data[subName][subKey])) {
						data[subName][subKey].push(obj.value);
					} else {
						data[subName][subKey] = [];
						data[subName][subKey].push(obj.value);
					}
				} else {
					data[subName][subKey] = obj.value;
				}
			} else {
				if (data[obj.name]) {
					if (Array.isArray(data[obj.name])) {
						data[obj.name].push(obj.value);
					} else {
						data[obj.name] = [];
						data[obj.name].push(obj.value);
					}
				} else {
					data[obj.name] = obj.value;
				}
			}
		});
		return data;
	};

	$.fn.eaccounting = function () {
		return this.each(function () {
			new $.eaccounting();
		});
	};

	$.eaccounting = function () {
		/**
		 * Block element.
		 *
		 * @param el
		 */
		this.block = function (el) {
			$(el).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		}

		/**
		 * Unblock element.
		 *
		 * @param el
		 */
		this.unblock = function (el) {
			$(el).unblock();
		}

		/**
		 * Make a dropdown
		 * @param el
		 */
		this.dropdown = function (el) {
			$(document)
				.on('click', function () {
					$(el).removeClass('open');
				})
				.on('click', el, function (e) {
					e.preventDefault();
					e.stopPropagation();
					$(el).removeClass('open');
					$(this).closest(el).toggleClass('open');
				});
		}

		/**
		 * Redirect based of object.
		 *
		 * @param url
		 * @returns {boolean|void}
		 */
		this.redirect = function (url) {
			if ('object' === typeof url) {
				if (('data' in url)) {
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
		}

		/**
		 * Get form values.
		 *
		 * @param form
		 * @returns {jQuery|{}}
		 */
		this.get_values = function (form) {
			return $(form).serializeObject();
		}

		/**
		 * Mask input
		 * @param input
		 * @param currency
		 */
		this.mask_amount = function (input, currency) {
			currency = currency || {};
			$(input).inputmask('decimal', {
				alias: 'numeric',
				groupSeparator: currency.thousand_separator || ',',
				autoGroup: true,
				digits: currency.precision || 2,
				radixPoint: currency.decimal_separator || '.',
				digitsOptional: false,
				allowMinus: false,
				prefix: currency.symbol || '',
				placeholder: '0.000',
				rightAlign: 0,
			})
		}

		/**
		 * Validate only decimal data.
		 *
		 * @param number
		 * @returns {*}
		 */
		this.validate_decimal = function (number) {
			return number.replace(/[^0-9.]/g, '');
		}

		/**
		 * Render notice.
		 *
		 * @param message
		 * @param type
		 * @param options
		 * @returns {undefined|boolean}
		 */
		this.notice = function (message, type, options) {
			return $.eaccounting_notice(message, type, options);
		}

		return this;
	};

	window.eaccounting = $.eaccounting();
})
