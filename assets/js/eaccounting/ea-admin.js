/* global eaccounting_admin_i10n */
jQuery(function ($) {
	// Tooltips
	var tooltips = $('.ea-help-tip');
	eaccounting_tooltips(tooltips);

	//initialize plugins
	$('.ea-input-date').datepicker({dateFormat: 'yy-mm-dd'});

	eaccounting.mask_amount('.ea-input-price');
	eaccounting.mask_amount('#opening_balance');
	//eaccounting.dropdown('.ea-dropdown');
	$(document.body).trigger('ea_select2_init');
	$('#quantity').on('change keyup', function (e) {
		e.target.value = e.target.value.replace(/[^0-9.]/g, '');
	});
	$(document.body).on('ea_modal_loaded', function () {
		$(document.body).trigger('ea_select2_init');
	});

	/**
	 * Media selector
	 * @type {boolean}
	 */
	var frame = false;
	$('.ea-attachment')
		.on('click', '.ea-attachment__upload', function (e) {
			e.preventDefault();
			var $button = $(this);
			if (frame) {
				frame.open();
				return false;
			}

			frame = wp.media({
				title: 'Select or upload attchment',
				button: {
					text: 'Select',
				},
				library: {
					type: 'image',
				},
				multiple: false,
				custom: 'custom'
			});

			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				$button
					.closest('.ea-attachment')
					.find('.ea-attachment__input').val(attachment.id)
					.end()
					.find('.ea-attachment__link').attr('href', attachment.url)
					.end()
					.find('.ea-attachment__image').attr('src', attachment.url)
					.end()
					.addClass('has--image');
			});

			frame.on('ready', function () {
				frame.uploader.options.uploader.params = {
					type: 'eaccounting_file'
				};
				console.log(frame.uploader.options.uploader.params);
			});

			frame.open();
		})
		.on('click', '.ea-attachment__remove', function (e) {
			e.preventDefault();
			var $button = $(this);
			$button
				.closest('.ea-attachment')
				.find('.ea-attachment__input').val(0)
				.end()
				.find('.ea-attachment__link').attr('href', '')
				.end()
				.find('.ea-attachment__image').attr('src', '')
				.end()
				.removeClass('has--image')

		});
});


function eaccounting_tooltips(selector) {
	// Tooltips
	selector.tooltip({
		content: function () {
			return jQuery(this).data('tip');
		},
		tooltipClass: 'ea-ui-tooltip',
		position: {
			my: 'center top',
			at: 'center bottom+10',
			collision: 'flipfit'
		},
		hide: {
			duration: 200
		},
		show: {
			duration: 200
		}
	});
}

function eaccounting_mask_amount(el, code) {
	if (!eaccountingi10n.hasOwnProperty('currencies')) {
		return false;
	}
	var currency = eaccountingi10n.currencies.find(function (currency) {
		console.log(currency.code);
		console.log(code);
		return currency.code === code;
	});
	if (!currency) {
		return false;
	}
	console.log(currency);
	jQuery(el).inputmask('decimal', {
		alias: 'numeric',
		groupSeparator: currency.thousand_separator,
		autoGroup: true,
		digits: currency.precision,
		radixPoint: currency.decimal_separator,
		digitsOptional: false,
		allowMinus: false,
		prefix: currency.symbol,
		placeholder: '0.000',
		rightAlign: 0,
		autoUnmask: true
	});
}
