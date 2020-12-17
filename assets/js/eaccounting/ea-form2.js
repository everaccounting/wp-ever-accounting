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
			$.post(ajaxurl, data).always(function (json) {
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
			$.post(ajaxurl, data, function (json) {
				eaccounting.notice(json);
				eaccounting.redirect(json);
			}).always(function (json) {
				eaccounting.unblock('#ea-income-form');
			});
		}
	}

	// var ea_invoice_form = {
	// 	init: function () {
	// 		$(document)
	// 			.on('click', '.ea-add-line-item', this.add_item)
	// 			.on('ea_modal_loaded', this.control_line_item_modal)
	//
	// 	},
	// 	control_line_item_modal:function (e, plugin){
	// 		if( 'modal-add-invoice-item' !== plugin.moda_id){
	// 			return ;
	// 		}
	//
	// 	},
	// 	add_item: function (e) {
	// 		e.preventDefault();
	// 		$('#modal-add-invoice-item').ea_modal();
	//
	// 		// $('#modal-add-invoice-item').ea_modal({
	// 		// 	onReady: function (plugin) {
	// 		// 		$('.ea-select2', plugin.$modal)
	// 		// 			.on('change', function (e) {
	// 		// 				$('.ea-select2', plugin.$modal).select2('destroy');
	// 		// 				console.log($('.ea-row', plugin.$modal).clone(true).find('.ea-select2').val('').end()[0])
	// 		// 				window.ea_item_line = $('.ea-row', plugin.$modal).clone(true).find('.ea-select2').val('').end()[0];
	// 		// 				$(ea_item_line).appendTo($('form', plugin.$modal));
	// 		// 				// var $row = plugin.$modal.find('.ea-row');
	// 		// 				// console.log($row[0]);
	// 		// 				// console.log(plugin.$modal.find('.ea-row').eq(0));
	// 		// 				// $('.ea-select2', plugin.$modal).select2('destroy');
	// 		// 				// plugin.$modal.find('form').append(plugin.$modal.find('.ea-row').eq(0));
	// 		// 				$('.ea-row', plugin.$modal).each(function (rowIndex) {
	// 		// 					$(this).find(":input, select").each(function () {
	// 		// 						var name = $(this).attr('name');
	// 		// 						name = name.replace(/\[(\d+)\]/, '[' + (rowIndex) + ']');
	// 		// 						$(this).attr('name', name).attr('id', name);
	// 		// 					});
	// 		// 				});
	// 		//
	// 		// 				// $('.ea-select2', plugin.$modal).select2('destroy');
	// 		// 				// $('form', plugin.$modal).append(plugin.$modal.data('row'));
	// 		// 				$('.ea-select2', plugin.$modal).eaccounting_select2();
	// 		// 			});
	// 		// 	}
	// 		// })
	// 	},
	//
	// };

	var ea_invoice_form = {
		init: function () {
			$('#ea-invoice-form')
				.on('change', '#currency_code', this.currency_changed)
				.on('click', '.add-line-item', this.add_line_item);

			$(document.body)
				.on('ea_invoice_updated', this.update_totals)
				.on('ea_modal_loaded', this.modal.init)
		},

		block: function () {
			$('#ea-invoice-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function () {
			$('#ea-invoice-form').unblock();
		},

		currency_changed: function () {
			$(document.body).trigger('ea_invoice_updated')
		},

		add_line_item: function(e) {
			e.preventDefault();
			$('#modal-add-line-item').ea_modal();
		},

		update_totals: function ( extra ) {
			extra = extra || {};
			ea_invoice_form.block();
			var data = $.extend({}, $('#ea-invoice-form').serializeObject(), {action: 'eaccounting_invoice_calculate_totals'}, extra);
			$.post(ajaxurl, data, function (json) {
				if( json.success ){
					$('#ea-invoice-form .ea-invoice-line-items').html(json.data.lines_html);
					$('#ea-invoice-form .ea-invoice-totals').html(json.data.totals_html);
				}
			}).always(function (json) {
				ea_invoice_form.unblock();
			});
		},

		modal: {
			init: function (e, plugin) {
				if( 'modal-add-line-item' === plugin.moda_id){
					plugin.$modal.on('change', '.ea-select2', function (){
						console.log(this);
					})
				}
			}
		}
	}

	ea_customer_form.init();
	ea_income_form.init();
	ea_invoice_form.init();
});
