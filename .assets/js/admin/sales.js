/**
 * ========================================================================
 * PAYMENT FORM
 * ========================================================================
 */
jQuery(document).ready(($) => {
	'use strict';

	$('#eac-payment-form')
		.on('change', '[name="currency"]', function () {
			const currency = $(this).val();
			const rate = eac_admin_vars.currencies[currency]['rate'] || 1;
			const $form = $(this).closest('form');
			const $amount = $form.find('[name="amount"]');
			const $exchange_rate = $form.find('[name="exchange_rate"]');
			$amount.data('currency', currency).removeClass('enhanced');
			$(document.body).trigger('eac_update_ui');
			$exchange_rate.next('.eac-form-field__addon').text(currency);
			$exchange_rate.val(rate);
		})
});
