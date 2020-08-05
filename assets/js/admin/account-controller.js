/*global ea_account_i10n */
jQuery(function ($) {

	var account_form = {
		$form: $('#ea-account-form'),
		block: function () {
			account_form.$form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		unblock: function () {
			account_form.$form.unblock();
		},
	}

	$(document)
		.on('select2:select', '#ea-account-form #currency_code', function (e) {
			var data = e.params.data;
			console.log(data);
		})
		.on('select2:click_add', '#ea-account-form #currency_code', function () {
			$(this).ea_backbone_modal({
				template: 'ea-modal-add-currency',
				onSubmit: function (form, modal) {
					modal.closeModal();
					account_form.block();
				}
			});
			return false;
		})

});
