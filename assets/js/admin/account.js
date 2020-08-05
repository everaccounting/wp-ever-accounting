/*global ea_account_i10n */
jQuery(function ($) {
	var account_form = {
		init: function () {
			$('.account-open').on('click', this.add_account);
		},
		add_account:function(){
			$( this ).ea_backbone_modal({
				template: 'ea-modal-add-account'
			});
			return false;
		},
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
	}

	account_form.init();
});
