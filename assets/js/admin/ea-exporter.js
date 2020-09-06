jQuery(function ($) {
	$.fn.eaccounting_exporter = function (options) {
		return this.each(function () {
			new $.eaccounting_exporter(this, options);
		});
	};

	$.eaccounting_exporter = function (form, options) {
		this.defaults = {};
		this.form = form;
		this.$form = $(form);
		this.$submit_btn = $('input[type="submit"]', this.$form);
		this.options = $.extend(this.defaults, options);
		this.action = 'eaccounting_do_ajax_export';
		this.nonce = this.$form.data('nonce');
		this.type = this.$form.data('type');
		var plugin = this;

		/**
		 * Submit form.
		 * @param e
		 * @returns {boolean}
		 */
		this.submit = function (e) {
			e.preventDefault();
			if (plugin.$submit_btn.hasClass('disabled')) {
				return false;
			}
			//disable submit
			plugin.$submit_btn.addClass('disabled');

			//reset
			plugin.reset();

			//Add the spinner.
			plugin.$submit_btn.parent('p').append('<span class="spinner is-active"></span>');

			//add progressbar
			plugin.$form.append('<div class="ea-batch-notice"><div class="ea-batch-progress"><div></div></div></div>');

			//process step
			plugin.process_step(1);
		};

		this.process_step = function (step) {
			window.wp.ajax.send(plugin.action, {
				data: {
					nonce: plugin.nonce,
					type: plugin.type,
					step: step,
				},
				success: function (res) {
					if (res.step === 'done') {
						plugin.reset();
						plugin.$form.append(
							'<div class="ea-batch-notice"><div class="updated success"><p>' + res.message + '</p></div></div>'
						);
						window.location = res.url;
						return false;
					} else {
						plugin.$form.find('.ea-batch-progress div').animate(
							{
								width: res.percentage + '%',
							},
							50,
							function () {}
						);
						plugin.process_step(parseInt(res.step, 10));
					}
				},
				error: function (error) {
					plugin.reset();
					if (error.message) {
						plugin.$form.append(
							'<div class="ea-batch-notice"><div class="updated error"><p>' + error.message + '</p></div></div>'
						);
					}
					console.warn(error);
				},
			});
		};

		/**
		 * Reset everything as fresh.
		 */
		this.reset = function () {
			//remove old notice
			$('.ea-batch-notice', plugin.$form).remove();
			//disable submit
			plugin.$submit_btn.removeClass('disabled');
			//remove spinner
			$('.spinner', plugin.$form).remove();
		};

		/**
		 * Initialize the plugin.
		 */
		this.init = function () {
			this.$form.on('submit', this.submit);
		};

		this.init();
		return this;
	};

	$('.ea-exporter').eaccounting_exporter();
});
