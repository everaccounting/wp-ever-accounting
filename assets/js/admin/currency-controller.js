/*global ea_account_i10n */
jQuery(function ($) {
	var currency_controller = {
		update_status: function (e) {
			var id = $(this).data('currency_id'),
				nonce = $(this).data('nonce'),
				status = $(this).is(':checked') ? 1 : 0;
			eaccounting.ajax({
					action: 'eaccounting_update_currency_status',
					id: id,
					nonce: nonce,
					enabled: status
				},
				eaccounting.output_response,
				eaccounting.output_response
			);
		},
		format_amount_field: function () {
			console.log('format_amount_field');
		}

	}

	$(document)
		.on('edit_account_form_loaded', currency_controller.currency_select_init)
		.on('change', '.ea-account-status-toggle', currency_controller.update_status);
});
