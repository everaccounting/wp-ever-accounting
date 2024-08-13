/**
 * Handle the document form in the admin.
 *
 * @since 1.0.0
 */

/* global jQuery */

( function ( $ ) {
	console.log('document.js');
	var $form = $( '.eac-document-form' ),
		$formMain = $( '.eac-document-form__main' ),
		$formSidebar = $( '.eac-document-form__sidebar' ),
		$addLineItem= $( '.add-line-item' ),
		$lineItems = $( '.line-items', $form ),
		$contactInput = $( ':input[name="contact_id"]', $form ),
		$currencyInput = $( ':input[name="currency_code"]', $form ),
		$vatExemptInput = $( ':input[name="vat_exempt"]', $form ),
		$discountInput = $( ':input[name="discount"]', $form ),
		$discountTypeInput = $( ':input[name="discount_type"]', $form );

		// Bind events.
		function recalculateTotals(){
			console.log('recalculateTotals');
			var  data = {};
			eac_admin.blockForm( $formMain );
			$(':input', $form).each(function () {
				var name = $(this).attr('name');
				var value = $(this).val();
				if (name) {
					data[name] = value;
				}
			});
			data.action = 'eac_calculate_invoice_totals';
			$formMain.load(eac_admin_js_vars.ajax_url, data, function () {
				eac_admin.unblockForm($form);
				eac_admin.bindEvents();
			});
		}

		console.log($addLineItem.length);

		$addLineItem.on( 'change', function (){
			console.log('addLineItem change');
			recalculateTotals();
		} );


} )( jQuery );
