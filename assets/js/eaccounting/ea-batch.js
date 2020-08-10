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
				batch_id: this.$el.data('batch_id'),
				nonce: this.$el.data('nonce'),
				//form: this.$el.serializeAssoc(),
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
			var self = this;
			wp.send({
				batch_id: data.batch_id,
				action: 'process_batch_request',
				nonce: data.nonce,
				form: data.form,
				step: step,
				data: data
			});


			this.$el.find('.ea-batch-progress div').animate({
				width: 10 + '%',
			}, 50, function () {
				// Animation complete.
			});
		}

	});
	jQuery(document).ready(function () {
		new eaccounting_batch();
	})


})(jQuery);
