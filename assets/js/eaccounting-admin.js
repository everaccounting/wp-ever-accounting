window.eAccounting = window.eAccounting || {};

(function($, window, wp, document, undefined) {
	'use strict';
	var eAccounting = {
		initializePlugins: function() {
			$('.ea-select2-control').select2({
				theme: 'default eaccounting-select2',
			});
			// $('.ea-price-control').inputmask('decimal', {
			// 	alias: 'numeric',
			// 	groupSeparator: eAccountingi18n.localization.thousand_separator,
			// 	autoGroup: true,
			// 	digits: eAccountingi18n.localization.precision,
			// 	radixPoint: eAccountingi18n.localization.decimal_separator,
			// 	digitsOptional: false,
			// 	allowMinus: false,
			// 	prefix: eAccountingi18n.localization.price_symbol,
			// 	placeholder: '0',
			// 	rightAlign: 0
			// });

			$('.ea-color-control').wpColorPicker();
			$('.ea-wp-file-upload-btn').on('click', eAccounting.handleMediaUpload);
		},
		handleMediaUpload: function(e) {
			e.preventDefault();
			var that = $(e.target);
			var image = wp
				.media({
					title: 'Upload Image',
					multiple: false,
				})
				.open()
				.on('select', function() {
					var uploaded_image = image
						.state()
						.get('selection')
						.first();
					var image_url = uploaded_image.toJSON().url;
					$(that)
						.closest('div')
						.find('input')
						.val(uploaded_image.toJSON().id);
					$(that)
						.closest('div')
						.css('background-image', 'url(' + image_url + ')');
				});
		},
		init: function() {
			this.initializePlugins();
			$(document).on('eAccountingInvoiceUpdated', this.initializePlugins);

			$('.open-eaccounting-modal').on('click', function(event) {
				event.preventDefault();
				console.log('clicked');
				$(this).WCBackboneModal({
					template: 'wc-modal-add-shipping-method',
					variable: {
						zone_id: 'data.zone_id',
					},
				});
			});
		},
	};

	document.addEventListener('DOMContentLoaded', function() {
		eAccounting.init();
	});
})(jQuery, window, window.wp, document, undefined);

/**
 * A nifty plugin to converty form to serialize object
 *
 * @link http://stackoverflow.com/questions/1184624/convert-form-data-to-js-object-with-jquery
 */
jQuery.fn.serializeObject = function() {
	var o = {};
	var a = this.serializeArray();
	jQuery.each(a, function() {
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
