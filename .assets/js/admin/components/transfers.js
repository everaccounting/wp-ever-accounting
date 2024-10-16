jQuery(document).ready(($) => {
	'use strict';

	$('#eac-edit-transfer').on('change', ':input[name="from_account_id"]', function (e) {
		var $form = $(this).closest('form'),
			$amount = $(':input[name="amount"]', $form),
			account = $(e.target).select2('data')?.[0],
			currency = account.currency || eac_base_currency;
		$amount.removeClass('enhanced').data('currency', currency)
		$(document.body).trigger('eac_update_ui');
	});
});
