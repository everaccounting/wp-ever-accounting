/* global eaccounting_importer_i10n */
jQuery(function ($) {
	$.eaccounting_importer = function (form, options) {
		this.defaults = {};
		this.form = form;
		this.$form = $(form);
		this.$upload_wrapper = $('.ea-importer-upload-wrapper', this.$form);
		this.$mapping_wrapper = $('.ea-importer-mapping-wrapper', this.$form);
		this.$submit_btn = $('input[type="submit"]', this.$form);
		this.$notice = $('.ea-io-form-notice', this.$upload_wrapper);
		this.options = $.extend(this.defaults, options);
		this.data = {};
		var plugin = this;

		/**
		 * Disable submit button
		 */
		this.disable_submit = function () {
			plugin.$submit_btn.addClass('disabled');
		}

		/**
		 * Enable submit button
		 */
		this.enable_submit = function () {
			plugin.$submit_btn.removeClass('disabled');
		}

		/**
		 * Add spinner
		 */
		this.add_spinner = function () {
			$('input[type="submit"]', this.$form).closest('p').append('<span class="spinner is-active"></span>');
		}

		/**
		 * Remove spinner
		 */
		this.remove_spinner = function () {
			$('.spinner', this.$form).remove();
		}

		/**
		 * Handle file upload part.
		 * @param e
		 * @returns {boolean}
		 */
		this.upload = function (e) {
			e.preventDefault();
			//if disabled submit button then bail
			if (plugin.$submit_btn.hasClass('disabled')) {
				return false;
			}

			if (plugin.$form.hasClass('mapped')) {
				plugin.init_import();
				return false;
			}

			plugin.response = {};
			plugin.disable_submit();
			plugin.add_spinner();
			$('.ea-io-form-notice', plugin.$form).remove();
			var data = new FormData(plugin.form);
			data.append('action', 'eaccounting_do_ajax_import');
			window.wp.ajax.send({
					type: 'POST',
					data: data,
					dataType: 'json',
					cache: false,
					contentType: false,
					processData: false,
					success: function (res) {
						plugin.remove_spinner();
						plugin.data = res;
						plugin.$form.addClass('mapped');
						plugin.$submit_btn.removeClass('disabled');
						plugin.$form.trigger('upload_complete', [res]);
					},
					error: function (error) {
						plugin.remove_spinner();
						plugin.$submit_btn.removeClass('disabled');
						plugin.$form.append('<div class="ea-io-form-notice"><div class="updated error"><p>' + error.message + '</p></div></div>');
					},
				},
			);

			return false;
		}

		this.init_mapping = function (e, response) {
			if ($.isEmptyObject(response)) {
				plugin.$notice.remove();
				plugin.$form.append('<div class="ea-io-form-notice"><div class="updated error"><p>' + eaccounting_importer_i10n.uploaded_file_not_found + '</p></div></div>');
				return false;
			}
			plugin.$upload_wrapper.remove();
			plugin.$mapping_wrapper.slideDown();
			var select = $('.ea-importer-map-column', plugin.$mapping_wrapper);
			var options = [];
			$.each(select, function () {
				var currentSelect = $(this),
					selectName = $(this).attr('name'),
					$tr = $(this).closest('tr');

				if ($.inArray(selectName.replace('mapping', '').replace(/[\[\]\]]/g, ''), response.required) !== -1) {
					$tr.find('td').eq(0).append(' <strong>' + eaccounting_importer_i10n.required + '</strong>');
					currentSelect.attr('required', 'required');
				}

				$.each(response.headers, function (columnKey, columnValue) {
					var processedColumnValue = columnValue.toLowerCase().replace(/ /g, '_');
					var columnRegex = new RegExp("\\[" + processedColumnValue + "\\]");

					if (selectName.length && selectName.match(columnRegex)) {
						// If the column matches a select, auto-map it. Boom.
						options += '<option value="' + columnValue + '" selected="selected">' + columnValue + '</option>';
						// Update the preview if there's a first-row value.
						if (false !== response.sample[columnValue]) {
							currentSelect.parent().next().html(response.sample[columnValue]);
						} else {
							currentSelect.parent().next().html('');
						}
					} else {
						options += '<option value="' + columnValue + '">' + columnValue + '</option>';
					}
				});

				// Add the options markup to the select.
				$(this).append(options).trigger('change');

				// Reset options.
				options = '';
			});
		}

		this.handle_preview = function () {
			var index = $(this).prop('selectedIndex');
			if (!index) {
				$(this).parent().next().html(eaccounting_importer_i10n.select_field_to_preview);
			} else {
				if (false !== plugin.data.sample[index - 1]) {
					$(this).parent().next().html(plugin.data.sample[index - 1]);
				} else {
					$(this).parent().next().html(eaccounting_importer_i10n.select_field_to_preview);
				}
			}
		}

		this.init_import = function () {
			$('.ea-io-form-notice', plugin.$form).remove();
			plugin.disable_submit();
			plugin.add_spinner();
			plugin.$notice.remove();
			plugin.$form.append('<div class="ea-io-form-notice"><div class="ea-io-form-progress"><div></div></div></div>');
			this.run_import(plugin.data);
		}

		this.run_import = function (res) {
			var data = $.extend({}, plugin.$form.serializeObject(), res, {action: 'eaccounting_do_ajax_import'});
			window.wp.ajax.send({
					type: 'POST',
					data: data,
					success: function (res) {
						if ('done' === res.position) {
							plugin.remove_spinner();
							$('.ea-io-form-notice', plugin.$form).remove();
							plugin.$submit_btn.remove();
							plugin.$form.append('<div class="ea-io-form-notice"><div class="updated success"><p>' + res.message + '</p></div></div>');

						} else {
							plugin.$form.find('.ea-io-form-progress div').animate({
								width: res.percentage + '%',
							}, 50, function () {
								// Animation complete.
							});

							plugin.run_import(res);
						}
					},
					error: function (error) {
						plugin.remove_spinner();
						$('.ea-io-form-notice', plugin.$form).remove();
						plugin.$submit_btn.removeClass('disabled');
						plugin.$form.append('<div class="ea-io-form-notice"><div class="updated error"><p>' + error.message + '</p></div></div>');
					},
				},
			);
		}

		this.init = function () {
			$(document)
				.on('submit', plugin.$form, this.upload)
				.on('upload_complete', plugin.$form, this.init_mapping);
			$('.ea-importer-map-column').on('change', plugin.$form, this.handle_preview);
		}

		this.init();
		return this;
	}

	$.fn.eaccounting_importer = function (options) {
		return this.each(function () {
			(new $.eaccounting_importer(this, options));
		});
	};

	$('.ea-importer').eaccounting_importer();
});
