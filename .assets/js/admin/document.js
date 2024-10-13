(function ($) {
	'use strict';

	/**
	 * jQuery plugin to initialize a document with various functionalities.
	 *
	 * @param {Object} [options] - Configuration options for the document.
	 * @param {string} [options.events] - An object where keys are event types and values are handlers.
	 * @returns {jQuery|*} - Returns the jQuery object for chaining, or the result of a method call.
	 */
	$.fn.eac_document = function (options) {};

	/**
	 * Default configuration options for the document.
	 */
	$.fn.eac_document.defaults = {
		tmpl_billing_address: '',
		tmpl_shipping_address: '',
		tmpl_item: '',
		tmpl_empty: '',
		tmpl_totals: '',
	};
})(jQuery);
