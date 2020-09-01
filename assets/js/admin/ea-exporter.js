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
