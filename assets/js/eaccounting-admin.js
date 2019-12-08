(function ($, window, wp, document, undefined) {
	'use strict';
	var eAccounting = {
		$select2Control: $('.ea-select2-control'),
		$priceControl: $('.ea-price-control'),
		$colorControl: $('.ea-color-control'),
		$recurringControl: $('#recurring_frequency'),
		initializePlugins: function () {
			this.$select2Control.select2({
				theme: 'default eaccounting-select2'
			});
			this.$priceControl.maskMoney({
				thousands: eAccountingi18n.localization.thousands_separator,
				decimal: eAccountingi18n.localization.decimal_mark,
				precision: eAccountingi18n.localization.precision,
				allowZero: true,
				prefix: eAccountingi18n.localization.price_symbol
			});
			this.$colorControl.wpColorPicker();
			this.$priceControl.trigger('focus');
			this.$priceControl.trigger('blur');

			$('.ea-transaction-table').eAccountingInvoiceTable();

		},
		init: function () {
			this.initializePlugins();
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

