jQuery(function ($) {
	'use strict';

	var ea_category_controller = {
		init: function () {
			$('#ea-categories-list-table')
				.on('change', '[name="enabled"]', this.list_table.toggle_enabled);
			$('#ea-category-form')
				.on('submit', this.form.submit);
		},

		list_table: {
			toggle_enabled: function (e) {
				e.preventDefault();
				var nonce = $(this).data('nonce'),
					id = $(this).data('id'),
					enabled = $(this).is(':checked'),
					data = {
						action: 'eaccounting_edit_category',
						id: id,
						enabled: enabled,
						nonce: nonce
					};
				$.post(ajaxurl, data, function (json) {
					$.eaccounting_notice(json);
				});
			},
		},

		form: {

			block: function () {
				$('#ea-category-form').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
			},

			unblock: function () {
				$('#ea-category-form').unblock();
			},

			submit: function (e) {
				e.preventDefault();
				ea_category_controller.form.block();
				var data = $(this).serializeObject();
				$.post(ajaxurl, data, function (json) {
					$.eaccounting_notice(json);
					$.eaccounting_redirect(json);
				}).always(function (json) {
					ea_category_controller.form.unblock();
				});
			}
		}
	};

	var ea_currency_controller = {
		init: function () {
			$('#ea-currencies-list-table')
				.on('change', '[name="enabled"]', this.list_table.toggle_enabled);
			$('#ea-currency-form')
				.on('change', '#code', this.form.update_currency_props)
				.on('submit', this.form.submit);
		},

		list_table: {
			toggle_enabled: function (e) {
				e.preventDefault();
				var nonce = $(this).data('nonce'),
					id = $(this).data('id'),
					enabled = $(this).is(':checked'),
					data = {
						action: 'eaccounting_edit_currency',
						id: id,
						enabled: enabled,
						nonce: nonce
					};
				$.post(ajaxurl, data, function (json) {
					$.eaccounting_notice(json);
				});
			},
		},

		form: {

			block: function () {
				$('#ea-currency-form').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
			},

			unblock: function () {
				$('#ea-currency-form').unblock();
			},
			update_currency_props:function(e){
				var code = e.target.value;
				if (!code) {
					return false;
				}
				var currency = eaccounting_form_i10n.global_currencies[code];
				$('#name', '#ea-currency-form').val(currency.name).change()
				$('#precision', '#ea-currency-form').val(currency.precision).change();
				$('#position', '#ea-currency-form').val(currency.position).change();
				$('#symbol', '#ea-currency-form').val(currency.symbol).change();
				$('#decimal_separator', '#ea-currency-form')
					.val(currency.decimal_separator)
					.change();
				$('#thousand_separator', '#ea-currency-form')
					.val(currency.thousand_separator)
					.change();
			},
			submit: function (e) {
				e.preventDefault();
				ea_currency_controller.form.block();
				var data = $(this).serializeObject();
				$.post(ajaxurl, data, function (json) {
					$.eaccounting_notice(json);
					$.eaccounting_redirect(json);
				}).always(function (json) {
					ea_currency_controller.form.unblock();
				});
			}
		}
	};

	var ea_invoice_controller1 = {
		init: function () {
			$('#ea-invoice-form')
				.on('click', '.add-line-item', this.form.add_line_item)
				.on('select2:select', '.select-item', this.form.item_selected)
				.on('click', '.delete-invoice-item', this.form.remove_line_item)
				.on('click', '.edit-invoice-item, .save-invoice-item', this.form.edit_line_item)
				.on('click', '.recalculate', this.form.recalculate)
				.on('submit', this.form.submit);

			$('#ea-invoice')
				.on('click', '.receive-payment', this.view.receive_payment)

			$(document.body).on('ea_invoice_updated', this.form.recalculate)
		},
		form: {
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
			add_line_item: function (e) {
				e.preventDefault();
				var line_item = $($('#ea-invoice-line-template').html());
				var item_selector = $($('#ea-invoice-item-selector').html());
				var item_selector_name = item_selector.attr('name');
				var index = Array(1).fill(null).map(() => Math.random().toString(10).substr(2)).join('');
				line_item.addClass('editing')
				$(line_item).find(":input").each(function () {
					var name = $(this).attr('name');
					name = name.replace(/\[(\d+)\]/, '[' + (index) + ']');
					$(this).attr('name', name).attr('id', name);
				});
				item_selector_name = item_selector_name.replace(/\[(\d+)\]/, '[' + (index) + ']');
				item_selector.attr('name', item_selector_name).attr('id', item_selector_name);
				item_selector.css({width: '90%', maxWidth: 'none'})
				line_item.find('.ea-invoice__line-name').html(item_selector);
				$('#ea-invoice__line-items').append(line_item);
				$(document.body).trigger('ea_select2_init');
			},
			item_selected: function (e) {
				var data = e.params.data;
				$(e.target).closest('tr')
					.find("input.item-name").val(data.item.name)
					.end()
					.find("input.invoice_unit_price").val(data.item.sale_price)
					.end()
					.find("input.invoice_item_quantity").val(1)
					.end()
					.find("input.invoice_item_tax").val(data.item.sales_tax_rate);
				$('body').trigger('ea_invoice_updated');
			},
			remove_line_item: function (e) {
				e.preventDefault();
				$(this).closest('tr').remove();
				ea_invoice_controller.form.recalculate();
			},
			edit_line_item:function(e){
				e.preventDefault();
				var $tr = $(this).closest('.ea-invoice__line');
				if ($tr.hasClass('editing')) {
					$tr.removeClass('editing');
					$('body').trigger('ea_invoice_updated');
					return false;
				}
				$tr.siblings('tr').removeClass('editing');
				$tr.addClass('editing');
			},
			recalculate:function (){
				ea_invoice_controller.form.block();
				var data = $.extend({}, $('#ea-invoice-form').serializeObject(), {action: 'eaccounting_invoice_recalculate'});
				$.post(ajaxurl, data, function (json) {
					if (json.success) {
						$('#ea-invoice-form .ea-invoice__items-wrapper').replaceWith(json.data.html);
					}else{
						$.eaccounting_notice(json);
					}
				}).always(function (json) {
					ea_invoice_controller.form.unblock();
				});
			},
			submit: function (e) {
				e.preventDefault();
				ea_invoice_controller.form.block();
				var data = $('#ea-invoice-form').serializeObject();
				$.post(ajaxurl, data, function (json) {
					$.eaccounting_redirect(json);
				}).always(function (json) {
					$.eaccounting_notice(json);
					ea_invoice_controller.form.unblock();
				});
			},
		},
		view:{
			receive_payment:function (e){
				e.preventDefault();
				$('#ea-modal-add-invoice-payment').ea_modal();
			}
		},
		list_table: {}
	}
	var ea_invoice_controller = {
		init: function () {
			$('#ea-invoice-form')
				.on('click', '.add-line-item', this.form.add_line_item)
				.on('select2:select', '.select-item', this.form.item_selected)
				.on('click', '.delete-invoice-item', this.form.remove_line_item)
				.on('click', '.edit-invoice-item, .save-invoice-item', this.form.edit_line_item)
				.on('click', '.recalculate', this.form.recalculate)
				.on('submit', this.form.submit);

			$('#ea-invoice')
				.on('click', '.receive-payment', this.view.receive_payment)

			$(document.body).on('ea_invoice_updated', this.form.recalculate)
		},
		form: {
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
			add_line_item: function (e) {
				e.preventDefault();
				var line_item = $($('#ea-invoice-line-template').html());
				var item_selector = $($('#ea-invoice-item-selector').html());
				var item_selector_name = item_selector.attr('name');
				var index = Array(1).fill(null).map(() => Math.random().toString(10).substr(2)).join('');
				line_item.addClass('editing')
				$(line_item).find(":input").each(function () {
					var name = $(this).attr('name');
					name = name.replace(/\[(\d+)\]/, '[' + (index) + ']');
					$(this).attr('name', name).attr('id', name);
				});
				item_selector_name = item_selector_name.replace(/\[(\d+)\]/, '[' + (index) + ']');
				item_selector.attr('name', item_selector_name).attr('id', item_selector_name);
				item_selector.css({width: '90%', maxWidth: 'none'})
				line_item.find('.ea-invoice__line-name').html(item_selector);
				$('#ea-invoice__line-items').append(line_item);
				$(document.body).trigger('ea_select2_init');
			},
			item_selected: function (e) {
				var data = e.params.data;
				$(e.target).closest('tr')
					.find("input.item-name").val(data.item.name)
					.end()
					.find("input.invoice_unit_price").val(data.item.sale_price)
					.end()
					.find("input.invoice_item_quantity").val(1)
					.end()
					.find("input.invoice_item_tax").val(data.item.sales_tax_rate);
				$('body').trigger('ea_invoice_updated');
			},
			remove_line_item: function (e) {
				e.preventDefault();
				$(this).closest('tr').remove();
				ea_invoice_controller.form.recalculate();
			},
			edit_line_item:function(e){
				e.preventDefault();
				var $tr = $(this).closest('.ea-invoice__line');
				if ($tr.hasClass('editing')) {
					$tr.removeClass('editing');
					$('body').trigger('ea_invoice_updated');
					return false;
				}
				$tr.siblings('tr').removeClass('editing');
				$tr.addClass('editing');
			},
			recalculate:function (){
				ea_invoice_controller.form.block();
				var data = $.extend({}, $('#ea-invoice-form').serializeObject(), {action: 'eaccounting_invoice_recalculate'});
				$.post(ajaxurl, data, function (json) {
					if (json.success) {
						$('#ea-invoice-form .ea-invoice__items-wrapper').replaceWith(json.data.html);
					}else{
						$.eaccounting_notice(json);
					}
				}).always(function (json) {
					ea_invoice_controller.form.unblock();
				});
			},
			submit: function (e) {
				e.preventDefault();
				ea_invoice_controller.form.block();
				var data = $('#ea-invoice-form').serializeObject();
				$.post(ajaxurl, data, function (json) {
					$.eaccounting_redirect(json);
				}).always(function (json) {
					$.eaccounting_notice(json);
					ea_invoice_controller.form.unblock();
				});
			},
		},
		view:{
			receive_payment:function (e){
				e.preventDefault();
				$('#ea-modal-add-invoice-payment').ea_modal();
			}
		},
		list_table: {}
	}

	var ea_item_controller = {};

	ea_category_controller.init();
	ea_currency_controller.init();
	ea_invoice_controller.init();
});
