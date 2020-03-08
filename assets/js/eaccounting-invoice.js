(function($, window, document, undefined) {
	var eAccountingInvoiceTable = function(table, options) {
		this.table = table;
		this.options = options;
		this.lineItem = table
			.find('tbody tr')
			.eq(0)
			.clone(true);
		this.addButton = table.find('#ea-invoice-add-line-item');
		this.init();
	};

	// the plugin prototype
	eAccountingInvoiceTable.prototype = {
		defaults: {},
		init: function() {
			this.options = $.extend({}, this.defaults, this.options);
			this.bindEvents();
			return this;
		},

		bindEvents: function() {
			var self = this;
			this.addButton.bind('click', function() {
				self.addLineItem();
			});
		},
		addLineItem: function() {
			var lineItem = this.lineItem.find('input').each(function() {
				$(this).val('');
			});

			this.table.find('tbody').append(lineItem);
			return false;
		},
	};

	$.eAccountingInvoiceTable = function(options) {
		new eAccountingInvoiceTable(options);
	};

	$.fn.eAccountingInvoiceTable = function(options) {
		return this.each(function() {
			var table = $(this);
			new eAccountingInvoiceTable(table, options);
		});
	};
})(jQuery, window, document);
