jQuery(function ($) {
	$('.ea-file-upload').each(function () {
		$(this).fileupload({
			dataType: 'json',
			dropZone: $(this),
			url: ajaxurl,
			formData: {
				script: true,
				action: 'eaccounting_file_upload',
				nonce: $(this).data('nonce')
			},
			change: function () {
				this.validation_errors = [];
			},
			add: function (e, data) {
				var $file_field = $(this).closest('.ea-file-field');
				var $file_upload = $(this);
				var $form = $file_upload.closest('form');
				var uploadErrors = [];

				// Validate type
				var allowed_types = $(this).data('file_types');

				if (allowed_types) {
					var acceptFileTypes = new RegExp('(\.|\/)(' + allowed_types + ')$', 'i');

					if (data.originalFiles[0].name.length && !acceptFileTypes.test(data.originalFiles[0].name)) {
						uploadErrors.push('Invalid file type. Accepted types:' + ' ' + allowed_types);
					}
				}

				if (uploadErrors.length > 0) {
					this.validation_errors = this.validation_errors.concat(uploadErrors);
				} else {
					$form.find(':input[type="submit"]').attr('disabled', 'disabled');
					data.context = $('<progress value="" max="100"></progress>').appendTo($file_field);
					data.submit();
				}
			},
			progress: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				data.context.val(progress);
			},
			fail: function (e, data) {
				var $file_upload = $(this);
				var $form = $file_upload.closest('form');

				if (data.errorThrown) {
					window.alert(data.errorThrown);
				}

				data.context.remove();

				$form.find(':input[type="submit"]').removeAttr('disabled');
				$file_upload.trigger('update_status');
			},
			done: function (e, data) {
				var $file_upload = $(this);
				var $file_field = $(this).closest('.ea-file-field');
				var $form = $file_upload.closest('form');
				var image_types = ['jpg', 'gif', 'png', 'jpeg', 'jpe'];

				data.context.remove();

				// Handle JSON errors when success is false
				if (typeof data.result.success !== 'undefined' && !data.result.success) {
					this.validation_errors.push(data.result.data);
				}

				$.each(data.result.files, function (index, file) {
					$file_upload.val('');
					if (file.error) {
						this.validation_errors.push(file.error);
					} else {
						$file_field.addClass('has-value');
						$file_field.find('input[type="hidden"]').val(file.url);
						$file_field.find('.ea-file-link').attr('href', file.url).text(file.name.substring(0, 20));

						if ($.inArray(file.extension, image_types) >= 0) {
							$file_field.css('background-image', 'url(' + file.url + ')');
							$file_field.removeClass('file-type-file');

						} else {
							$file_field.addClass('file-type-file');
							$file_field.css('background-image', '');
						}

					}
				});

				if (this.validation_errors.length > 0) {
					this.validation_errors = this.validation_errors.filter(function (value, index, self) {
						return self.indexOf(value) === index;
					});
					window.alert(this.validation_errors.join('\n'));
				}

				$form.find(':input[type="submit"]').removeAttr('disabled');
				$file_upload.trigger('update_status');
			}
		});
	});

	//handle file remove
	$(document).on('click', '.ea-file-remove', function (e) {
		e.preventDefault();
		e.stopPropagation();
		var $file_field = $(this).closest('.ea-file-field');
		$file_field.find('.ea-file-value').val();
		$file_field.find('.ea-file-link').attr('href', '#').text('');
		$file_field.css('background-image', '');
		$file_field.removeClass('has-value');
		$file_field.removeClass('file-type-file');
	});


});
