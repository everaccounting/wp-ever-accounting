window.eAccounting = window.eAccounting || {};
(function ($) {
	$.eAccountingFormHandler = function (form, options) {

		var defaults = {
			action: undefined,
			redirect: true
		};

		var plugin = this;

		plugin.settings = {};

		var $form = $(form),
			form = form;

		plugin.init = function () {
			plugin.settings = $.extend({}, defaults, options);
			if (this.settings.action === undefined || this.settings.action === '') {
				alert('Please define a action');
			}
			$form.on('submit', this.handleForm);


		};
		plugin.handleForm = function (e) {
			e.preventDefault();
			plugin.disableSubmit();
			var form = $(this).serializeObject();
			wp.ajax.send(plugin.settings.action, {
				data: form,
				success: function (res) {
					if (res.id && res.redirect) {
						window.location.href = res.redirect;
					}
				},
				error: function (response) {
					alert(response);
				}
			});

		};
		plugin.disableSubmit = function () {
			$form.find('[type="submit"]').attr('disabled', 'disabled');
		};

		plugin.enableSubmit = function () {
			$form.find('[type="submit"]').removeAttr('disabled');
		};

		plugin.init();

	};

	$.fn.eAccountingFormHandler = function (options) {

		return this.each(function () {
			if (undefined === $(this).data('eAccountingFormHandler')) {
				var plugin = new $.eAccountingFormHandler(this, options);
				$(this).data('handler', plugin);
			}
		});

	};

})(jQuery);

(function ($, window, wp, document, undefined) {
	'use strict';
	$('#ea-contact-form').eAccountingFormHandler({action: 'eaccounting_edit_contact'});


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

