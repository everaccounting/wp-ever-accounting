/* global eaccounting_admin_i10n */
jQuery(function ($) {
	// Tooltips
	var tooltips = $('.ea-help-tip');
	eaccounting_tooltips(tooltips);

	//initialize plugins
	$('.ea-input-date').datepicker({dateFormat: 'yy-mm-dd'});
	$('body').on('click','.ea-modal__body .ea-input-date',function(e){
		$(this).datepicker({dateFormat: 'yy-mm-dd'});
	});

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

	$('#eaccounting_settings[financial_year_start]').datepicker({dateFormat: 'yy-mm-dd'});

	//dropdwown
	$( document )
		.on( 'click', function () {
			$( '.ea-dropdown' ).removeClass( 'open' );
		} )
		.on( 'click', '.ea-dropdown-trigger', function ( e ) {
			e.preventDefault();
			e.stopPropagation();
			$( '.ea-dropdown' ).removeClass( 'open' );
			$( this ).closest( '.ea-dropdown' ).toggleClass( 'open' );
		} );

	/**
	 * Media selector
	 * @type {boolean}
	 */
	var frame = false;
	$('.ea-attachment')
		.on('click', '.ea-attachment__upload', function (e) {
			e.preventDefault();
			var $button = $(this),
			allowed_types = $(this).data('allowed-types').split(',');
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
				var subtype = attachment.subtype;
				console.log(allowed_types);
				console.log(subtype);
				if( !allowed_types.includes(subtype) ){
					alert('Unsupported Media');
					return false;
				}
				$button
					.closest('.ea-attachment')
					.find('.ea-attachment__input').val(attachment.id)
					.end()
					.find('.ea-attachment__link').attr('href', attachment.url)
					.end()
					.find('.ea-attachment__image').attr('src', ['jpeg', 'png', 'jpg'].includes(subtype)? attachment.url :attachment.icon)
					.end()
					.addClass('has--image');
			});

			frame.on('ready', function () {
				frame.uploader.options.uploader.params = {
					type: 'eaccounting_file'
				};
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

/**
 * Get currency.
 *
 * @param code
 * @returns {boolean|*}
 */
function eaccounting_get_currency(code){
	if (!eaccountingi10n.hasOwnProperty('currencies')) {
		return false;
	}
	return eaccountingi10n.currencies.find(function (currency) {
		return currency.code === code;
	});
}

/**
 * Format currency field.
 * @param el
 * @param currency
 * @returns {boolean}
 */
function eaccounting_mask_amount(el, currency) {
	if( 'object' !== typeof currency){
		if (!eaccountingi10n.hasOwnProperty('currencies')) {
			return false;
		}
		currency = eaccountingi10n.currencies.find(function (currency) {
			return currency.code === currency;
		});
		if (!currency) {
			return false;
		}

	}

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

/**
 * Block UI
 * @param el
 */
function eaccounting_block(el){
	jQuery(el).block({
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.6
		}
	});
}

/**
 * Unblock UI
 * @param el
 */
function eaccounting_unblock(el){
	jQuery(el).unblock();
}
