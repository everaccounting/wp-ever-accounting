jQuery(function ($) {
	$('.ea-file-upload').each(function () {
		console.log(this);
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
				var $file_field = $(this);
				var $form = $file_field.closest('form');
				var $uploaded_files = $file_field.parent().find('.ea-uploaded-files');
				var uploadErrors = [];
				var fileLimitLeft = false;
				var fileLimit = parseInt($file_field.data('file_limit'), 10);

				if (typeof $file_field.data('file_limit_left') !== 'undefined') {
					fileLimitLeft = parseInt($file_field.data('file_limit_left'), 10);
				} else if (typeof fileLimit !== 'undefined') {
					var currentFiles = parseInt($uploaded_files.children('.ea-uploaded-file').length, 10);
					fileLimitLeft = fileLimit - currentFiles;
					$file_field.data('file_limit_left', fileLimitLeft);
				}

				if (false !== fileLimitLeft && fileLimitLeft <= 0) {
					var message = 'Exceeded upload limit';
					if ($file_field.data('file_limit_message')) {
						message = $file_field.data('file_limit_message');
					} else if (typeof job_manager_job_submission !== 'undefined') {
						message = 'You are only allowed to upload a maximum of %d files.';
					}
					message = message.replace('%d', fileLimit);

					uploadErrors.push(message);
				}

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
					if (false !== fileLimitLeft) {
						$file_field.data('file_limit_left', fileLimitLeft - 1);
					}
					$form.find(':input[type="submit"]').attr('disabled', 'disabled');
					data.context = $('<progress value="" max="100"></progress>').appendTo($uploaded_files);
					data.submit();
				}
			},
			progress: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				data.context.val(progress);
			},
			fail: function (e, data) {
				var $file_field = $(this);
				var $form = $file_field.closest('form');

				if (data.errorThrown) {
					window.alert(data.errorThrown);
				}

				data.context.remove();

				$form.find(':input[type="submit"]').removeAttr('disabled');
				$file_field.trigger('update_status');
			},
			done: function (e, data) {
				var $file_field = $(this);
				var $form = $file_field.closest('form');
				var $uploaded_files = $file_field.parent().find('.ea-uploaded-files');
				var multiple = $file_field.attr('multiple') ? 1 : 0;
				var image_types = ['jpg', 'gif', 'png', 'jpeg', 'jpe'];

				data.context.remove();

				// Handle JSON errors when success is false
				if (typeof data.result.success !== 'undefined' && !data.result.success) {
					this.validation_errors.push(data.result.data);
				}

				$.each(data.result.files, function (index, file) {
					console.log(file);
					if (file.error) {
						this.validation_errors.push(file.error);
					} else {
						var html;
						if ($.inArray(file.extension, image_types) >= 0) {
							html = $.parseHTML('<span class="ea-uploaded-file-preview"><a href="#" class="ea-file-review"><img src="http://example.com"/></a><a class="ea-remove-uploaded-file" href="#"></a></span>');
							$(html).find('img').attr('src', file.url);
							$(html).find('a').attr('href', file.url);
						} else {
							html = $.parseHTML('<span class="ea-uploaded-file-name"><code></code><a class="ea-remove-uploaded-file" href="#"></a></span>');
							$(html).find('code').text(file.name);
							$(html).find('a').attr('href', file.url);
						}

						// $(html).find('.input-text').val(file.url);
						// $(html).find('.input-text').attr('name', 'current_' + $file_field.attr('name'));

						if (multiple) {
							$uploaded_files.append(html);
						} else {
							$uploaded_files.html(html);
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
				$file_field.trigger('update_status');
			}


		});
	});
});