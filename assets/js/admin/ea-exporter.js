jQuery(function ($) {
	var ea_csv_bulk_exporter = Backbone.View.extend({
		el: '.ea-bulk-csv-exporter',
		events: {
			'submit': 'onSubmit',
		},
		/**
		 * Initialize actions
		 */
		initialize: function () {
			this.$submit_btn = $('input[type="submit"]', this.$el);
			_.bindAll(this, 'onSubmit', 'process_step');
		},
		/**
		 * Process form data.
		 *
		 * @param {Object} e
		 */
		onSubmit: function (e) {
			e.preventDefault();
			if (this.$submit_btn.hasClass('disabled')) {
				return;
			}

			this.$submit_btn.addClass('disabled');

			// Add the spinner.
			this.$submit_btn.parent().append('<span class="spinner is-active"></span>');

			//remove old progressbar

			$('.ea-batch-notice', this.$el).remove();
			//add new progressbar
			this.$el.append('<div class="ea-batch-notice"><div class="ea-batch-progress"><div></div></div></div>');

			// Start the process.
			this.process_step(1, this.$el.serializeObject());
		},
		/**
		 * Processes a single batch of data.
		 *
		 * @since 1.0.2
		 *
		 * @param {integer}  step Step in the process.
		 * @param {string[]} data Form data.
		 */
		process_step: function (step, data) {
			var self = this,
				$form = self.$el,
				$spinner = $form.find('.spinner'),
				$notice = $form.find('.ea-batch-notice'),
				$progress = $form.find('.ea-batch-progress');
			data.step = step;
			wp.ajax.send('eaccounting_do_ajax_export', {
				data: data,
				success: function (res) {
					$progress.find('div').animate({
						width: res.percentage + '%',
					}, 50, function () {
						// Animation complete.
					});

					if (res.step === 'done') {
						self.$submit_btn.removeClass('disabled');
						$spinner.remove();
						$progress.remove()
						$notice.append($('<div class="updated success"><p>' + res.message + '</p></div>'));
						window.location = res.url;
						return false;
					} else {
						self.process_step(parseInt(res.step, 10), data);
					}
				},
				error: function (error) {
					$spinner.remove();
					$progress.remove()
					$notice.append($('<div class="updated error"><p>' + error.message + '</p></div>'));
					self.$submit_btn.removeClass('disabled');
				}
			});
		}
	});

	new ea_csv_bulk_exporter();

});


/**
 * Importer for Ever Accounting
 * @since 1.0.2
 */
jQuery(function ($) {
	$.fn.eaccounting_importer = function (options) {
		return this.each(function () {
			(new $.eaccounting_importer(this, options));
		});
	};

	$.eaccounting_importer = function (form, options) {
		this.defaults = {};
		this.form = form;
		this.$form = $(form);
		this.$submit_btn = $('input[type="submit"]', this.$form);
		this.options = $.extend(this.defaults, options);
		var self = this;
		this.block = function () {
			self.$form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		};

		this.unblock = function () {
			self.$form.unblock();
		}

		this.add_spinner = function () {
			// Add the spinner.
			this.$submit_btn.parent().append('<span class="spinner is-active"></span>');
		}

		this.remove_spinner = function () {
			this.$form.find('.spinner').remove();
		}

		this.add_message = function (message, type = 'success') {
			$('.ea-batch-notice', self.$form).remove();
			self.$form.append('<div class="ea-batch-notice"><div class="updated ' + type + '"><p>' + message + '</p></div></div>');
		}

		this.on_submit = function (e) {
			e.preventDefault();
			if (self.$submit_btn.hasClass('disabled')) {
				return;
			}

			$('.ea-batch-notice', self.$form).remove();
			//add new progressbar
			self.$form.append('<div class="ea-batch-notice"><div class="ea-batch-progress"><div></div></div></div>');
			self.$submit_btn.addClass('disabled');

			self.add_spinner();
			var data = new FormData(self.form);
			data.append('action', 'eaccounting_do_ajax_import');

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: data,
				dataType: 'json',
				cache: false,
				contentType: false,
				processData: false,
				success: function (response) {
					self.remove_spinner();
					self.$submit_btn.removeClass('disabled');
					if (!response.success && response.data.message) {
						self.add_message(response.data.message, 'error');
					}
				}
			}).fail(function (response) {
				self.remove_spinner();
				self.$submit_btn.removeClass('disabled');
				window.console.log(response);
			});

		}

		this.init = function () {
			$(document)
				.on('submit', self.$form, this.on_submit)
		}

		this.init();

		return this;
	}

	$('.ea-csv-importer').eaccounting_importer();

});

