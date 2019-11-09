(function ($, window, wp, document, undefined) {
	'use strict';
	var eAccounting = {
		$select2Control:$('.ea-select2-control'),
		$priceControl:$('.ea-price-control'),
		$colorControl:$('.ea-color-control'),
		initializePlugins: function () {
			this.$select2Control.select2();
			this.$priceControl.maskMoney({
				thousands: eAccountingi18n.localization.thousands_separator,
				decimal: eAccountingi18n.localization.decimal_mark,
				precision: eAccountingi18n.localization.precision,
				allowZero: true,
				prefix: eAccountingi18n.localization.price_symbol,
			});
			this.$colorControl.wpColorPicker();
			this.$priceControl.trigger('focus');
			this.$priceControl.trigger('blur');
		},
		init: function () {
			this.initializePlugins();
		}
	};

	document.addEventListener('DOMContentLoaded', function () {
		eAccounting.init();
	})

})(jQuery, window, window.wp, document, undefined);


