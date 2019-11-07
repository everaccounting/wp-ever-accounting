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
				prefix: (eAccountingi18n.localization.symbol_first) ? eAccountingi18n.localization.price_symbol : '',
				suffix: (eAccountingi18n.localization.symbol_first) ? '' : eAccountingi18n.localization.price_symbol
			});
		},
		init: function () {
			this.initializePlugins();
			$('.eaccouting-ajax-form').eAccountingAjaxForm()

		}
	};

	document.addEventListener('DOMContentLoaded', function () {
		eAccounting.init();
	})

})(jQuery, window, window.wp, document, undefined);


(function ($) {
	var defaultOptions = {}, methods = {
		init: function (parameters) {
			parameters = $.extend($.extend({}, defaultOptions), parameters);
			return this.each(function () {
				var $form = $(this), $submitButton = $(this).find('input[type="submit"]');

				function handleFormSubmit(e) {
					e.preventDefault();
					e.returnValue = false;
					disableForm();
					jQuery.ajax({
						url: window.ajaxurl,
						type: 'POST',
						data: $form.serializeArray(),
						success: function (response) {
							response.success && window.location.reload();
							!response.success && toastr.error(response.data.message, 'error');
							enableForm();
						},
						error: function (response) {
							console.log(response);
							enableForm();
						},
						complete: function () {
							enableForm();
						}
					});
				}

				function disableForm(){
					$submitButton.attr("disabled", "disabled");
				}

				function enableForm(){
					$submitButton.removeAttr("disabled");
				}

				$form.bind('submit', handleFormSubmit)
			})
		}
	};
	$.fn.eAccountingAjaxForm = function (method) {
		return methods.init.apply(this, arguments);
	};
})(window.jQuery);

