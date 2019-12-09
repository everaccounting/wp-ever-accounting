window.eAccounting =  window.eAccounting|| {} ;

(function ($, window, wp, document, undefined) {
	'use strict';
	var eAccounting = {
		initializePlugins: function () {
			$('.ea-select2-control').select2({
				theme: 'default eaccounting-select2'
			});
			$('.ea-price-control').maskMoney({
				thousands: eAccountingi18n.localization.thousands_separator,
				decimal: eAccountingi18n.localization.decimal_mark,
				precision: eAccountingi18n.localization.precision,
				allowZero: true,
				prefix: eAccountingi18n.localization.price_symbol
			});

			$('.ea-color-control').wpColorPicker();
			$('.ea-price-control').trigger('focus');
			$('.ea-price-control').trigger('blur');
		},
		init: function () {
			this.initializePlugins();
			$(document).on('eAccountingInvoiceUpdated', this.initializePlugins);
		}
	};

	document.addEventListener('DOMContentLoaded', function () {
		eAccounting.init();
	});

})(jQuery, window, window.wp, document, undefined);

/**
 * A nifty plugin to converty form to serialize object
 *
 * @link http://stackoverflow.com/questions/1184624/convert-form-data-to-js-object-with-jquery
 */
jQuery.fn.serializeObject = function () {
	var o = {};
	var a = this.serializeArray();
	jQuery.each(a, function () {
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

