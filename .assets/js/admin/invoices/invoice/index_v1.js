export const Invoice = Backbone.View.extend({
	/**
	 * Element
	 */
	el: '#eac-invoice-form',

	/**
	 * Events
	 */
	events: {
		'change :input.add-line-item': 'addLineItem',
		// 'change :input[name="currency_code"]': 'updateCurrency',
		// 'click #eac-add-invoice-line': 'addInvoiceLine',
	},

	/**
	 * Initialize actions
	 */
	initialize: function () {

	},

	/**
	 * Add Line Item
	 */
	addLineItem: function (e) {
		var item_id = parseInt($(e.target).val());
		if (!item_id) {
			return;
		}
		//reset the value of the select box.
		$(e.target).empty().trigger('change');

		// make fetch request to get the item details from rest api.
		$.ajax({
			url: eac_invoice_vars.rest_url + '/items/' + item_id,
			method: 'GET',
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', eac_invoice_vars.nonce);
			},
			success: function (response) {
				if (response.success) {
					// add the line item to the invoice.
					$('#eac-invoice-line-items').append(response.data);
				}
			},
			error: function (response) {
				console.log(response);
			}
		});
	}
});
