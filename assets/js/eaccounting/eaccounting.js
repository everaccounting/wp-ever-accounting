/**
 * Ever Accounting Core Plugin
 *
 * version 1.0.0
 */
window.eaccounting = window.eaccounting || {};

(function ($, window, wp, document, undefined) {
	'use strict';

	//
	// $.extend($.validator.messages, {
	// 	required: "This field is required.",
	// 	remote: "Please fix this field.",
	// 	email: "Please enter a valid email address.",
	// 	url: "Please enter a valid URL.",
	// 	date: "Please enter a valid date.",
	// 	dateISO: "Please enter a valid date (ISO).",
	// 	number: "Please enter a valid number.",
	// 	digits: "Please enter only digits.",
	// 	creditcard: "Please enter a valid credit card number.",
	// 	equalTo: "Please enter the same value again.",
	// 	accept: "Please enter a value with a valid extension.",
	// 	maxlength: $.validator.format("Please enter no more than {0} characters."),
	// 	minlength: $.validator.format("Please enter at least {0} characters."),
	// 	rangelength: $.validator.format("Please enter a value between {0} and {1} characters long."),
	// 	range: $.validator.format("Please enter a value between {0} and {1}."),
	// 	max: $.validator.format("Please enter a value less than or equal to {0}."),
	// 	min: $.validator.format("Please enter a value greater than or equal to {0}.")
	// });
	//
	// $.validator.setDefaults({
	// 	errorElement: 'span',
	// 	errorClass: 'description error',
	// });
	//
	// $.validator.addMethod("currency_rate", function (value, element) {
	// 	return this.optional(element) || !/[^\d.]+/g.test(value);
	// }, "numbers, and dot only please");

	eaccounting.redirect = function (url) {
		url = url.trim();
		if (!url) {
			return false;
		}
		var ua = navigator.userAgent.toLowerCase(),
			isIE = ua.indexOf('msie') !== -1,
			version = parseInt(ua.substr(4, 2), 10);

		// Internet Explorer 8 and lower
		if (isIE && version < 9) {
			var link = document.createElement('a');
			link.href = url;
			document.body.appendChild(link);
			return link.click();
		}
		// All other browsers can use the standard window.location.href (they don't lose HTTP_REFERER like Internet Explorer 8 & lower does)
		window.location.href = url;
	}

	eaccounting.output_response = function (res) {
		var success = false, message = false;
		if (res && res.success && res.success) {
			success = true;
		}

		if (res && res.data && res.data.message && res.data.message) {
			message = res.data.message;
		}
		if (message)
			$.eaccounting_notice(message, success ? 'success' : 'error');
	}


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

/**
 * Jquery block plugin warpper
 */
jQuery.fn.blockThis = function () {
	return this.each(function () {
		jQuery(this).block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
	});
};

/**
 * Custom plugin for handling select 2 field
 */

jQuery(function ($) {
	$.fn.eaccounting_select2 = function (options) {
		return this.each(function () {
			(new $.eaccounting_select2(this, options));
		});
	};

	$.eaccounting_select2 = function (el, options) {
		this.$el = $(el);
		this.has_search = this.$el.is("[data-search]");
		this.search_data = this.has_search ? this.$el.data('search') : {};
		this.has_modal = this.$el.is("[data-modal]");
		this.moda_data = this.has_modal ? this.$el.data('modal') : {};
		this.id = this.$el.attr('id');
		// var plugin = this;
		// 	plugin.ajax =
		// plugin.$el = $(el);
		// plugin.data = {};
		// plugin.ajax = $(el).hasClass('ea-ajax-select2');
		// plugin.createable = $(el).hasClass('enable-create');
		// plugin.data.nonce = $(el).attr('data-nonce');
		// plugin.data.action = $(el).attr('data-action');
		// plugin.data.type = $(el).attr('data-type');
		// plugin.options = $.extend(true, {}, $.eaccounting_select2.defaultOptions, options);
		// console.log($(el).data('search'));
		//
		var self = this;
		if (this.has_search) {
			options = $.extend(true, {}, options, {
				ajax: {
					cache: true,
					delay: 500,
					url: ajaxurl,
					method: 'POST',
					dataType: 'json',
					data: function (params) {
						return $.extend(true, {}, self.search_data, {
							search: params.term,
							page: params.page
						});
					},
					processResults: function (data, params) {
						params.page = params.page || 1;
						return {
							results: data.results,
							pagination: {
								more: data.pagination.more
							}
						};
					}
				},
				placeholder: self.$el.attr('placeholder') || 'Select..',
				allowClear: false
			});
		}
		if (this.$el.data('select2')) {
			this.$el.select2('destroy');
		}

		var instance = this.$el.select2(options);
		if (this.has_modal) {
			instance.on('select2:open', (e) => {
				var $results = $('#select2-' + self.id + '-results').closest('.select2-results');
				if (!$results.children('.ea-select2-footer').length) {
					console.log('nai');
					var $footer = $('<a href="#" class="ea-select2-footer"><span class="dashicons dashicons-plus"></span>Add New</a>')
						.on('click', function (e) {
							e.preventDefault();
							instance.select2("close");
							self.$el.trigger(self.moda_data.event, [instance, self.moda_data]);
						});
					$results.append($footer);
				}
			});
		}

		return instance;
	};

	$.eaccounting_select2.defaultOptions = {};
	$('[data-search]').eaccounting_select2({test: true});
	$('.ea-ajax-select2').eaccounting_select2({test: true});
});

jQuery.fn.ea_color_picker = function () {
	return this.each(function () {
		var el = this;
		jQuery(el)
			.iris({
				change: function (event, ui) {
					jQuery(el).parent().find('.colorpickpreview').css({backgroundColor: ui.color.toString()});
				},
				hide: true,
				border: true
			})
			.on('click focus', function (event) {
				event.stopPropagation();
				jQuery('.iris-picker').hide();
				jQuery(el).closest('div').find('.iris-picker').show();
				jQuery(el).data('original-value', jQuery(el).val());
			})
			.on('change', function () {
				if (jQuery(el).is('.iris-error')) {
					var original_value = jQuery(this).data('original-value');

					if (original_value.match(/^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})jQuery/)) {
						jQuery(el).val(jQuery(el).data('original-value')).change();
					} else {
						jQuery(el).val('').change();
					}
				}
			});

		jQuery('body').on('click', function () {
			jQuery('.iris-picker').hide();
		});

	});
};
