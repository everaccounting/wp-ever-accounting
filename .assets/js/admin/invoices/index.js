(function ($) {

	// invoice item model.
	var InvoiceLine = Backbone.Model.extend({
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
		}
	});

	// Invoice Line Collection.
	var InvoiceLines = Backbone.Collection.extend({
		model: InvoiceLine,
	});

	// Invoice Line View.
	// var InvoiceLineView = Backbone.View.extend({
	// 	tagName: 'tr',
	// 	template: _.template($('#eac-invoice-line-template').html()),
	// 	events: {
	// 		'click .eac-delete-invoice-line': 'deleteInvoiceLine',
	// 	},
	// 	initialize: function () {
	// 		this.listenTo(this.model, 'change', this.render);
	// 	}
	// });

	// Invoice Line Tax Model.
	var InvoiceLineTax = Backbone.Model.extend({
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

	// Invoice Line Tax Collection.
	var InvoiceLineTaxes = Backbone.Collection.extend({
		model: InvoiceLineTax,
	});

	// Invoice Model.
	var Invoice = Backbone.Model.extend({
		defaults: {
			id: null,
			number: '',
			date: '',
			due_date: '',
			status: 'draft',
			customer_id: null,
			customer_name: '',
			customer_email: '',
			customer_address: '',
			customer_phone: '',
			customer_vat: '',
			customer_note: '',
			currency_code: '',
			currency_rate: 1,
			subtotal: 0,
			subtotal_tax: 0,
			discount: 0,
			discount_tax: 0,
			tax_total: 0,
			total: 0,
			lines: new InvoiceLines(),
			taxes: new InvoiceLineTaxes(),
		},
	});

	var InvoiceView = Backbone.View.extend({
		/**
		 * Element
		 */
		el: $('#eac-invoice-form'),

		/**
		 * Events
		 */
		events: {
			'change :input.add-line-item': 'addLineItem',
			'change :input[name="currency_code"]': 'updateCurrency',
			'click #eac-add-invoice-line': 'addInvoiceLine',
		},

		/**
		 * Initialize actions
		 */
		initialize: function () {
			console.log('InvoiceView initialize');
		},

		/**
		 * Add line item
		 */
		addLineItem: function (e) {
			var id = $(e.target).val();
			// fetch item details from rest api.
			$.ajax({
				url: eacInvoices.restUrl + 'eac/v1/items/' + id,
				method: 'GET',
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', eacInvoices.restNonce);
				},
				success: function (response) {
					console.log(response);
				},
				error: function (response) {
					console.log(response);
				}
			});
		},

		/**
		 * Update currency
		 */
		updateCurrency: function (e) {
			var currencyCode = $(e.target).val();
			console.log('Currency code: ' + currencyCode);
		},

		/**
		 * Block UI
		 */
		block: function () {
			console.log(this)
			this.$el.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		/**
		 * Unblock UI
		 */
		unblock: function () {
			this.$el.unblock();
		}
	});

	console.log(eacInvoices)
	new InvoiceView();
})(jQuery);
