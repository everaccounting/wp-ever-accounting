jQuery( function ( $ ) {
	'use strict';

	var document_editor = {
		$form : $(document).find('.eac-document__form'),
		init: function () {
			$(document)
				.on('click', '#eac-document .eac-document__edit-address',  this.edit_address)
				.on('change', '#eac-document .eac-select-customer', this.change_customer_user)
		},

		edit_address: function (e) {
			e.preventDefault();
			var $this = $(this),
				$address = $this.closest('div'),
				$address_editor = $address.find('.eac-document__address-editor'),
				$address_display = $address.find('eac-document__address');

			$address_editor.toggle();
			$address_display.toggle();
		},
		change_customer_user: function (e) {
			e.preventDefault();
			var contact_id = $(this).val(),
				$address = $(this).closest('.eac-document__address'),
				$address_editor = $address.find('.eac-document__address-editor'),
				$address_display = $address.find('.eac-document__address');

			if ( ! contact_id ) {
				return;
			}
			$.ajax({
				url: eac_admin_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'eac_get_contact_details',
					contact_id: contact_id,
					// '_wpnonce': eac_admin_vars.nonce,
				},
				success: function (response) {
					if ( response.success ) {
						// loop though the response and update the address fields.
						$.each(response.data, function (key, value) {
							$(document).find('.eac-document__address-editor [name="billing_' + key + '"]').val(value).trigger('change')
							$(document).find('.eac-document__address-editor [name="shipping_' + key + '"]').val(value).trigger('change')
						});
					}
				}
			});
		}
	}

	// document_editor.init();
} );
