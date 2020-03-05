jQuery(function($) {
	$.blockUI.defaults.overlayCSS.cursor = 'default';

	// var eaccounting_tax_form = {
	// 	$tax_form: $('form.ea-tax-form'),
	// 	init: function () {
	// 		this.validateForm();
	// 	},
	// 	validateForm: function () {
	// 		var self = this;
	// 		this.$tax_form.validate({
	// 			rules: {
	// 				rate: {
	// 					number: true
	// 				}
	// 			},
	// 			errorPlacement: function() {
	// 				return true;
	// 			},
	// 			submitHandler: self.submit()
	// 		});
	// 	},
	// 	submit: function () {
	//
	// 	}
	// };
	//
	// var eaccounting_currency_form = {
	// 	$tax_form: $('form.ea-currency-form'),
	// 	init: function () {
	// 		this.validateForm();
	// 	},
	// 	validateForm: function () {
	// 		var self = this;
	// 		this.$tax_form.validate({
	// 			rules: {
	// 				rate: {
	// 					number: true
	// 				}
	// 			},
	// 			errorPlacement: function() {
	// 				return true;
	// 			},
	// 			submitHandler: self.submit()
	// 		});
	// 	},
	// 	submit: function () {
	//
	// 	}
	// };
	// var eaccounting_account_form = {
	// 	$account_form: $('form.ea-account-form'),
	// 	init: function () {
	// 		$(document.body).on('change', '#currency_code', this.handleCurrency).trigger('change');
	// 	},
	// 	handleCurrency:function(){
	// 		console.log('changed');
	// 	},
	// 	submit: function () {
	//
	// 	}
	// };
	// eaccounting_tax_form.init();
	// eaccounting_currency_form.init();
	// eaccounting_account_form.init();

	$.eaccounting_account_form = {
		$account_form: $('form.ea-account-form'),
		init: function() {
			$(document)
				.change('#currency_code', this.handleCurrency)
				.change();
		},
		handleCurrency: function(e) {
			$.ajax({
				url: eAccountingi18n.ajax_url,
				data: {
					action: 'eaccounting_get_currency',
					code: $(this).val(),
				},
				type: 'POST',
				success: function(response) {
					console.log(response);
				},
			});
		},
		block: function() {
			$('#woocommerce-order-items').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
		},

		unblock: function() {
			$('#woocommerce-order-items').unblock();
		},
		submit: function() {},
	};
	$.eaccounting_account_form.init();
});

jQuery(document).ready(function($) {});
