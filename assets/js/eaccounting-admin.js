(function ($, window, wp, document, undefined) {
	'use strict';
	var eAccounting = {
		ajaxForms: $('.eaccouting-ajax-form'),
		initializePlugins: function () {
			console.log(eAccountingi18n);
			$('select.ea-select2').select2();
			$('input.ea-price').maskMoney({
				thousands: eAccountingi18n.localization.thousands_separator,
				decimal: eAccountingi18n.localization.decimal_mark,
				precision: eAccountingi18n.localization.precision,
				allowZero: true,
				prefix: eAccountingi18n.localization.price_symbol,
			});
			$('input.ea-price').trigger('click');
		},
		init: function () {
			this.initializePlugins();
		}
	};

	document.addEventListener('DOMContentLoaded', function () {
		eAccounting.init();
	})

})(jQuery, window, window.wp, document, undefined);


