(function ($, window, wp, document, undefined) {
	'use strict';
	var eAccounting = {
		$select2Control: $('.ea-select2-control'),
		$priceControl: $('.ea-price-control'),
		$colorControl: $('.ea-color-control'),
		$recurringControl: $('#recurring_frequency'),
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
		handleRecurring: function () {
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

			$('.ea-file-upload').each(function () {
				$(this).fileupload({
					dataType: 'json',
					dropZone: $(this),
					url: ajaxurl,
					formData: {
						script: true,
						action:'eaccounting_file_upload',
						//nonce:
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
								message = job_manager_job_submission.i18n_over_upload_limit;
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
							if (file.error) {
								this.validation_errors.push(file.error);
							} else {
								var html;
								if ($.inArray(file.extension, image_types) >= 0) {
									html = $.parseHTML(job_manager_ajax_file_upload.js_field_html_img);
									$(html).find('.job-manager-uploaded-file-preview img').attr('src', file.url);
								} else {
									html = $.parseHTML(job_manager_ajax_file_upload.js_field_html);
									$(html).find('.job-manager-uploaded-file-name code').text(file.name);
								}

								$(html).find('.input-text').val(file.url);
								$(html).find('.input-text').attr('name', 'current_' + $file_field.attr('name'));

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
			// $('#ea-contact-form').on('submit', function (e) {
			// 	e.preventDefault();
			// 	console.log($(this).serializeObject())
			//
			//
			// });


		}
	};

	document.addEventListener('DOMContentLoaded', function () {
		eAccounting.init();
	})

})(jQuery, window, window.wp, document, undefined);

/**
 * A nifty plugin to converty form to serialize object
 *
 * @link http://stackoverflow.com/questions/1184624/convert-form-data-to-js-object-with-jquery
 */
jQuery.fn.serializeObject = function () {
	var o = {};
	var a = this.serializeArray();
	jQuery.each(a, function () {
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
