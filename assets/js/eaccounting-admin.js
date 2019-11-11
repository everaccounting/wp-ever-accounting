(function ($, window, wp, document, undefined) {
	'use strict';
	var eAccounting = {
		$select2Control:$('.ea-select2-control'),
		$priceControl:$('.ea-price-control'),
		$colorControl:$('.ea-color-control'),
		$recurringControl:$('#recurring_frequency'),
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
		handleRecurring:function(){
			var value = eAccounting.$recurringControl.val() || '';
			var recurring_frequency = $('#recurring_frequency').parent().parent();
			var recurring_count = $('#recurring_count').parent();

			if (value === 'no' || value === '') {
				recurring_frequency.removeClass('ea-col-10').removeClass('ea-col-4').addClass('ea-col-12');
				recurring_count.addClass('ea-hidden');
			} else {
				recurring_frequency.removeClass('ea-col-12').removeClass('ea-col-4').addClass('ea-col-10');
				recurring_count.removeClass('ea-hidden');
			}
		},
		init: function () {
			this.initializePlugins();
			this.handleRecurring();
			$(document).on('change', '#recurring_frequency', this.handleRecurring);
		}
	};

	document.addEventListener('DOMContentLoaded', function () {
		eAccounting.init();
	})

})(jQuery, window, window.wp, document, undefined);

