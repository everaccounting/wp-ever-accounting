
window.eac = window.eac || {};

(function ($) {
	var invoices = {model: {}, view: {}, controller: {}};

	// For debugging.
	invoices.debug = true;

	/**
	 * invoice.log
	 *
	 * A debugging utility for invoices. Works only when a
	 * debug flag is on and the browser supports it.
	 */
	invoices.log = function () {
		if (window.console && invoices.debug) {
			window.console.log.apply(window.console, arguments);
		}
	};

	/**
	 * ========================================================================
	 * MODELS
	 * ========================================================================
	 */

	invoices.model.LineItem = Backbone.Model.extend({
		defaults: {
			id: null,
			type: 'standard',
			name: '',
			price: 0,
			quantity: 1,
			subtotal: 0,
			subtotal_tax: 0,
			discount: 0,
			discount_tax: 0,
			tax_total: 0,
			total: 0,
			taxable: false,
			description: '',
			unit: '',
			item_id: null,
		},
	});

	invoices.model.LineTax = Backbone.Model.extend({
		defaults: {
			'id': null,
			'name': '',
			'rate': 0,
			'is_compound': false,
			'amount': 0,
			'line_id': null,
			'tax_id': null,
			'document_id': null,
		}
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	invoices.view.LineItem = Backbone.View.extend({});


})(jQuery);
