(function ($) {
	var eaccounting_batch = Backbone.View.extend({
		el: '.ea-batch-form',
		events: {
			'submit': 'onSubmit',
		},
		/**
		 * Initialize actions
		 */
		initialize: function () {
			this.submitBtn = $('input[type="submit"]', this.$el);
			this.confirmation = this.submitBtn.data('ays');
			_.bindAll(this, 'onSubmit', 'process_step');
		},
		/**
		 * Process form data.
		 *
		 * @param {Object} e
		 */
		onSubmit: function (e) {
			e.preventDefault();
			if (this.submitBtn.hasClass('disabled')) {
				return;
			}

			if (this.confirmation !== undefined) {
				if (!confirm(this.confirmation)) {
					return;
				}
			}

			var data = {
				process_name: this.$el.data('process_name'),
				nonce: this.$el.data('nonce'),
				form: this.$el.serialize(),
			};

			this.submitBtn.addClass('disabled');

			// Add the spinner.
			this.submitBtn.parent().append('<span class="spinner is-active"></span>');

			//remove old progressbar
			$('.ea-batch-progress', this.$el).remove();

			//add new progressbar
			this.$el.append('<div class="ea-batch-progress"><div></div></div>');

			// Start the process.
			this.process_step(1, data, this);
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
				spinner = $form.find('.spinner'),
				$progress_wrap = $form.find('.ea-batch-progress'),
				dismiss_on_complete = true === $form.data('dismiss-when-complete');

			wp.ajax.send('eaccounting_process_batch_request', {
				batch_id: data.process_name,
				nonce: data.nonce,
				form: data.form,
				step: step,
				data: data,
				success: function (res) {
					if (res.done) {
						// We need to get the actual in progress form, not all forms on the page
						if (false === dismiss_on_complete) {
							$form.find('.button-disabled').removeClass('button-disabled');
						}
					}
					this.$el.find('.ea-batch-progress div').animate({
						width: 10 + '%',
					}, 50, function () {
						// Animation complete.
					});
					self.process_step(parseInt(res.step), data);
				},
				error: function (error) {
					spinner.remove();
					$progress_wrap.remove()
					$form.append($('<div class="updated error"><p>' + error.message + '</p></div>'));
					self.submitBtn.removeClass('disabled');
				}
			});


		}

	});
	jQuery(document).ready(function () {
		new eaccounting_batch();
	})


})(jQuery);
