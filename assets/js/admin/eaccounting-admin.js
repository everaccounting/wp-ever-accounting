/* global eaccounting_admin_i10n */
(function ($, eaccounting_admin_i10n) {
	$(function () {
		if ('undefined' === typeof eaccounting_admin_i10n) {
			return;
		}

		//initialize pace animation
		$(document).ajaxStart(function () {
			Pace.restart();
		})

		$.extend($.validator.messages, {
			required: "This field is required.",
			remote: "Please fix this field.",
			email: "Please enter a valid email address.",
			url: "Please enter a valid URL.",
			date: "Please enter a valid date.",
			dateISO: "Please enter a valid date (ISO).",
			number: "Please enter a valid number.",
			digits: "Please enter only digits.",
			creditcard: "Please enter a valid credit card number.",
			equalTo: "Please enter the same value again.",
			accept: "Please enter a value with a valid extension.",
			maxlength: $.validator.format("Please enter no more than {0} characters."),
			minlength: $.validator.format("Please enter at least {0} characters."),
			rangelength: $.validator.format("Please enter a value between {0} and {1} characters long."),
			range: $.validator.format("Please enter a value between {0} and {1}."),
			max: $.validator.format("Please enter a value less than or equal to {0}."),
			min: $.validator.format("Please enter a value greater than or equal to {0}.")
		});

		$.validator.setDefaults({
			errorElement: 'span',
			errorClass: 'description error',
		});

		$.validator.addMethod("currency_rate", function (value, element) {
			return this.optional(element) || !/[^\d.]+/g.test(value);
		}, "numbers, and dot only please");


		window.eaccounting.currency_form = {
			validate: function () {
				$('#ea-currency-form').validate({
					rules: {
						rate: {
							required: true,
							currency_rate: true,
							normalizer: function (value) {
								return $.trim(value);
							}
						}
					}
				});
			},
			handleSubmit: function (e) {
				e.preventDefault();
				var $form = $(this);
				$form.blockThis();
				wp.ajax.post($form.serializeArray())
					.then(function (result) {
						result = $.extend(true, {}, {message: '', redirect: ''}, result)
						console.log(result);
						$form.unblock();
						$.eaccounting_notice(result.message, 'success');
						eaccounting.redirect(result.redirect);
					})
					.fail(function (error) {
						console.log(error);
						$form.unblock();
						$.eaccounting_notice(error.message, 'error');
					});
			},
			init: function () {
				this.validate();
				$(document)
					.on('ready', function () {
						$('#ea-currency-form #code').select2();
						$('#ea-currency-form #position').select2();
					})
					.on('select2:select', '#ea-currency-form #code', function (e) {
						var data = e.params.data;
						if (data.id === '') {
							return false;
						}
						try {
							currency = eaccounting_admin_i10n.global_currencies[data.id];
							$('#ea-currency-form #precision').val(currency.precision).change();
							$('#ea-currency-form #position').val(currency.position).change();
							$('#ea-currency-form #symbol').val(currency.symbol).change();
							$('#ea-currency-form #decimal_separator').val(currency.decimal_separator).change();
							$('#ea-currency-form #thousand_separator').val(currency.thousand_separator).change();
						} catch (e) {
							console.log(e.message)
						}
					})
					.on('submit', '#ea-currency-form', this.handleSubmit)
			}
		}

		eaccounting.currency_form.init();


		$(document)
			.on('click', '.wp-list-table .ea_item_status_update', function () {
				var objectid = $(this).data('objectid'),
					nonce = $(this).data('nonce'),
					enabled = $(this).is(':checked') ? 1 : 0,
					objecttype = $(this).data('objecttype');

				if (!objectid || !nonce || !objecttype) {
					$.eaccounting_notice('Item Missing some important property', 'error');
					return false;
				}

				wp.ajax.post('eaccounting_item_status_update', {
					objectid: objectid,
					nonce: nonce,
					enabled: enabled,
					objecttype: objecttype
				}).then(function (result) {
					result = $.extend(true, {}, {message: '', redirect: ''}, result)
					$.eaccounting_notice(result.message, 'success');
				}).fail(function (error) {
					$.eaccounting_notice(error.message, 'error');
				});
			})
			.on('click', function () {
				$('.ea-dropdown').removeClass('open');
				console.log('body')
			})
			.on('click', '.ea-dropdown-button', function (e) {
				e.preventDefault();
				e.stopPropagation();
				$('.ea-dropdown').removeClass('open');
				$(this).closest('.ea-dropdown').toggleClass('open');
			})

	});
})(jQuery, eaccounting_admin_i10n);
