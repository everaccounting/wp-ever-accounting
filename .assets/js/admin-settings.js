jQuery(document).ready(function ($) {
	// Settings uploader
	let file_frame;
	window.formfield = '';

	$('body').on('click', '.ea_settings_upload_button', function (e) {
		e.preventDefault();

		const button = $(this);

		window.formfield = $(this).parent().prev();

		// If the media frame already exists, reopen it.
		if (file_frame) {
			file_frame.open();
			return;
		}

		// Create the media frame
		file_frame = wp.media.frames.file_frame = wp.media({
			frame: 'post',
			state: 'insert',
			title: button.data('uploader_title'),
			button: {
				text: button.data('uploader_button_text'),
			},
			multiple: false,
		});

		file_frame.on('menu:render:default', function (view) {
			// Store our views in an object,
			const views = {};

			// Unset default menu items
			view.unset('library-separator');
			view.unset('gallery');
			view.unset('featured-image');
			view.unset('embed');

			// Initialize the views in our view object
			view.set(views);
		});

		// When an image is selected, run a callback
		file_frame.on('insert', function () {
			const selection = file_frame.state().get('selection');

			selection.each(function (attachment, index) {
				attachment = attachment.toJSON();
				window.formfield.val(attachment.url);
			});
		});

		// Open the modal
		file_frame.open();
	});

	$('.ea-financial-start').datepicker({dateFormat: 'dd-mm'});

	$('#eac-currency-list').eac_form({
		events: {
			'select2:open #select-currency': 'onOpenCurrency',
			'change #select-currency': 'onAddCurrency',
		},

		onOpenCurrency: function (e) {
			var $this = $(e.target);
			console.log('Open Currency');
		},

		onAddCurrency: function (e) {
			var $this = $(e.target);
			var currency = $this.val();
			// if we found a value then enable #eac-add-currency button otherwise disable it.
			if (currency) {
				$('#eac-add-currency').removeAttr('disabled');
			} else {
				$('#eac-add-currency').attr('disabled', 'disabled');
			}
		}
	})
});
