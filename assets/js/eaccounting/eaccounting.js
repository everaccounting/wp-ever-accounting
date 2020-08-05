/**
 * Ever Accounting Core Plugin
 *
 * version 1.0.0
 */
window.eaccounting = window.eaccounting || {};

(function ($, window, wp, document, undefined) {
	'use strict';

	eaccounting.ajax = function (data, onSuccess, onError) {
		$.ajax({
			url: ajaxurl,
			data: data,
			success: onSuccess,
			error: onError,
		});
	}

	eaccounting.redirect = function(url){
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

	eaccounting.validate_rate = function (value) {
		value.replace(/[^\d.]+/g, '')
	}

	eaccounting.handle_dropdown = function (e) {
		// e.preventDefault();
		// $('.ea-dropdown').removeClass('active');
		// var wrapper = $(e.target).closest('.ea-dropdown');
		//
		// if (wrapper.length) {
		// 	if (wrapper.hasClass('active')) {
		// 		return wrapper.removeClass('active');
		// 	}
		//
		// 	return wrapper.addClass('active');
		// }
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
	$.fn.eaccounting_select2 = function (el, options) {
		return this.each(function () {
			(new $.eaccounting_select2(this, options));
		});
	};

	$.eaccounting_select2 = function (el, options) {
		plugin = this;
		plugin.settings = $.extend(true, {}, $.eaccounting_select2.defaultOptions, options);
		plugin.$el = $(el);
		plugin.data = {};
		plugin.options = {};
		plugin.ajax = $(el).hasClass('ea-ajax-select2');
		plugin.createable = $(el).attr('data-createable');
		plugin.data.nonce = $(el).attr('data-nonce');
		plugin.data.action = $(el).attr('data-action');
		plugin.data.type = $(el).attr('data-type');

		if (plugin.ajax) {
			plugin.options = {
				ajax: {
					cache: true,
					delay: 500,
					url: ajaxurl,
					method: 'POST',
					dataType: 'json',
					data: function (params) {
						return {
							action: plugin.data.action,
							nonce: plugin.data.nonce,
							type: plugin.data.type,
							search: params.term,
							page: params.page
						};
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
				placeholder: plugin.$el.attr('placeholder') || 'Select..',
				allowClear: false
			}
		}
		var instance = $(el).select2(plugin.options);
		instance.on('select2:open', () => {
			if (!$(".select2-results .ea-select2-footer").length) {
				var $footer = $('<a href="#" class="ea-select2-footer"><span class="dashicons dashicons-plus"></span>Add New</a>')
					.on('click', function (e) {
						e.preventDefault();
						instance.select2("close");
						plugin.$el.trigger('select2:click_add', [instance, plugin]);
					});
				$(".select2-results").append($footer);
			}
		});

		return instance;
	};

	$.eaccounting_select2.defaultOptions = {};

});

