(function ($, window, document, undefined) {
	'use strict';
	var eac_items = {
		init: function () {
			$(document)
				.on('block', 'form.eac-form', this.block_form)
				.on('unblock', 'form.eac-form', this.unblock_form)
				.on('submit', 'form.eac-form', this.submit_form)
		},
		block_form: function () {
			var $form = $(this);
			var $submit = $('[form="' + $form.attr('id') + '"]');
			$submit.attr('disabled', 'disabled');
			$form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		unblock_form: function () {
			var $form = $(this);
			var $submit = $('[form="' + $form.attr('id') + '"]');
			$submit.removeAttr('disabled');
			$form.unblock();
		},
		submit_form: function (e) {
			e.preventDefault();
			var $form = $(this);
			var data = $form.serializeAssoc();
			var $submit = $('[form="' + $form.attr('id') + '"]');
			// if submit button is disabled, do nothing.
			if ($submit.is(':disabled')) {
				return;
			}
			// block the form.
			$form.trigger('block');
			// submit the form.
			$.ajax({
				url: eac_admin_vars.ajax_url,
				type: 'POST',
				data: data,
				dataType: 'json',
				success: function (response) {
					// unblock the form.
					$form.trigger('unblock');
					// if there is an error, show the error message.
					if (response.success === false) {
						$.each(response.data, function (i, message) {
							eac_admin.show_message(message, 'error');
						});
					} else {
						// show the success message.
						eac_admin.show_message(response.data, 'success');
						// if the form is an add form, reset the form.
						if ($form.hasClass('eac-add-form')) {
							$form.trigger('reset');
						}
					}
				},
				error: function () {
					// unblock the form.
					$form.trigger('unblock');
					// show the error message.
					eac_admin.show_message(eac_admin_vars.i18n.ajax_error, 'error');
				}
			});
		}
	}
})(jQuery, window, document);
