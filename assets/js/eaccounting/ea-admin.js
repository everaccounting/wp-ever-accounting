/* global eaccounting_admin_i10n */
jQuery(function ($) {

	//initialize plugins
	$('.ea-input-date').datepicker({dateFormat: 'yy-mm-dd'});
	$('.ea-help-tip').tipTip();
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
