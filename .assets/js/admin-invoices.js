(function (document, wp, $) {
	'use strict';

	const invoices = { collection:{}, model:{}, view:{} }

	invoices.model.InvoiceItem = wp.Backbone.Model.extend({
		defaults: {
			id: 0,
			name: '',
			description: '',
			quantity: 0,
			price: 0,
			total: 0,
		},
	});

	invoices.collection.InvoiceItems = wp.Backbone.Collection.extend({
		model: invoices.model.InvoiceItem,
	});



}(document, wp, jQuery));
