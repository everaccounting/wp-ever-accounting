jQuery(function ($) {
	'use strict';
	//Customer Form
	var ea_customer_form = {
		init: function () {
			$(document)
				.on('submit', '#ea-customer-form', this.submit);
		},
		submit: function (e) {
			e.preventDefault();
			eaccounting.block('#ea-customer-form');
			const data = eaccounting.get_values('#ea-customer-form');
			$.post(ajaxurl, data, function (json) {
				eaccounting.notice(json);
				eaccounting.redirect(json);
			}).always(function (json) {
				eaccounting.unblock('#ea-customer-form');
			});
		}
	}


	// Revenue Form
	var ea_income_form = {
		init: function () {
			$(document)
				.on('change', '#ea-income-form #account_id', this.update_amount_input)
				.on('submit', '#ea-income-form', this.submit)
				.find('#ea-income-form #account_id').trigger('change');
		},
		update_amount_input: function (e) {
			var account_id = parseInt(e.target.value, 10);
			if (!account_id) {
				return false;
			}
			eaccounting.block('#ea-income-form');
			var data = {
				action: 'eaccounting_get_account_currency',
				account_id: account_id,
				_wpnonce: eaccounting_form_i10n.nonce.get_currency,
			}
			$.post(ajaxurl, data).always(function (json){
				eaccounting.unblock('#ea-income-form');
				if (json.success) {
					return eaccounting.mask_amount('#ea-income-form #amount', json.data);
				}
				eaccounting.notice(json);
			});
		},
		submit: function (e) {
			e.preventDefault();
			eaccounting.block('#ea-income-form');
			const data = eaccounting.get_values('#ea-income-form');
			$.post(ajaxurl, data, function (json){
				eaccounting.notice(json);
				eaccounting.redirect(json);
			}).always(function (json){
				eaccounting.unblock('#ea-income-form');
			});
		}
	}

	var ea_invoice_form = {
		init: function () {
			$('.ea-add-line-item', this.$form).on('click', this.add_line);
		},
		add_line: function (e) {
			e.preventDefault();
			$('#modal-add-invoice-item').ea_modal({
				onSubmit: function (plugin) {
					plugin.close();
				}
			})
		}
	};

	ea_customer_form.init();
	ea_income_form.init();
	ea_invoice_form.init();
});
