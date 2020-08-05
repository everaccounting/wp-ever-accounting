/*global ea_account_i10n */
jQuery(function ($) {
	var currency_controller = {
		init: function () {
			$('.ea-currency-toggle').on('change', currency_controller.update_status);
			// $('#ea-currency-form #code, #ea-currency-form #code1').select2()
			// 	.on('select2:open', () => {
			// 		// if (!$(".select2-results .ea-select2-footer").length) {
			// 		// 	var $footer = $('<a href="#" class="ea-select2-footer"><span class="dashicons dashicons-plus"></span> Add New</a>');
			// 		// 	$(".select2-results").append($footer);
			// 		// }
			// 	});
			// $('#ea-currency-form')
			// 	.validate({
			// 		errorPlacement: function (error, element) {
			// 			return true;
			// 		},
			// 		ignore: [],
			// 		rate: {
			// 			required: true,
			// 			number: true
			// 		},
			// 		submitHandler: function () {
			// 			currency_form.onSubmit();
			// 		},
			// 	});
		},
		onSubmit: function (e) {
			e.preventDefault();

			console.log('submited');
			// form.validate({
			// 	submitHandler: function (form) {
			// 		console.log(form);
			// 		//form.submit();
			// 	}
			// })

		},
		update_status: function (e) {
			var id = $(this).data('currency_id'),
				nonce = $(this).data('nonce'),
				status = $(this).is(':checked') ? 1 : 0
			$.ajax({
				url: ajaxurl,
				data: {
					action: 'eaccounting_update_currency_status',
					id: id,
					nonce: nonce,
					enabled: status
				},
				success: function (res) {
					eaccounting.output_response(res);
				},
				error: function (res) {
					eaccounting.output_response(res);
				},
			});
		}
	}
	currency_controller.init();
});
