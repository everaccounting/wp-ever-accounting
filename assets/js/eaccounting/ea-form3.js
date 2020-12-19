jQuery(function ($) {
	'use strict';
	//Invoice Form
	var ea_invoice_form = {
		init: function () {
			$('#ea-invoice-form')
				.on('change', '#currency_code', this.currency_changed)
				.on('click', '.add-line-item', function () {
					var index = Array(1).fill(null).map(() => Math.random().toString(10).substr(2)).join('');
					var row = $($('#invoice-line-item').html())
						.addClass('editing');

					$(row).find(":input").each(function () {
						var name = $(this).attr('name');
						name = name.replace(/\[(\d+)\]/, '[' + (index) + ']');
						$(this).attr('name', name).attr('id', name);
					});
					var items = $('.ea-invoice-line-items').append(row);
					$('[name$="line_items['+index+'][item_id]"]', items).eaccounting_select2();
				})
				// .on('select2:select', '.item_id', function (e){
				// 	var data = e.params.data;
				// 	console.log(data);
				// 	console.log($(e.target).closest('tr')[0])
				// 	console.log(data.item.sale_price);
				// 	$(e.target).closest('tr')
				// 		.find("input.item-name").val(data.item.name)
				// 		.end()
				// 		.find("input.item-price").val(data.item.sale_price)
				// 		.end()
				// 		.find("input.item-vat").val(data.item.vat)
				// 		.end()
				// 		.find("input.item-tax").val(data.item.sales_tax_rate)
				// 	$('body').trigger('ea_invoice_updated');
				//
				// })
				.on('click', '.delete-line', this.remove_line_item)
				.on('click', '.edit-line, .save-line', this.edit_line_item)
				.on('click', '.recalculate-totals', this.update_totals)
				.on('submit', this.submit);

			$('#ea-invoice')
				.on('click', '.receive-payment', this.receive_payment);


			$(document.body)
				.on('ea_invoice_updated', this.update_totals)
				.on('ea_modal_loaded', this.modal.init)
				.on('ea_modal_form_submitted', this.modal.submitted)
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
			ea_invoice_form.update_totals();
		},
		add_line_item: function (e) {
			e.preventDefault();
			$('#modal-add-line-item').ea_modal();
		},
		remove_line_item: function (e) {
			e.preventDefault();
			$(this).closest('tr').remove();
			ea_invoice_form.update_totals();
		},
		edit_line_item: function (e) {
			e.preventDefault();
			var $tr = $(this).closest('.ea-invoice-line');
			if ($tr.hasClass('editing')) {
				$tr.removeClass('editing');
				$('body').trigger('ea_invoice_updated');
				return false;
			}
			$tr.siblings('tr').removeClass('editing');
			$tr.addClass('editing');
		},
		update_totals: function () {
			ea_invoice_form.block();
			var data = $.extend({}, $('#ea-invoice-form').serializeObject(), {action: 'eaccounting_invoice_calculate_totals'});
			$.post(ajaxurl, data, function (json) {
				if (json.success) {
					$('#ea-invoice-form .ea-invoice-line-items').html(json.data.lines_html);
					$('#ea-invoice-form .ea-invoice-totals').html(json.data.totals_html);
				}
			}).always(function (json) {
				ea_invoice_form.unblock();
			});
		},
		receive_payment: function (e) {
			e.preventDefault();
			$('#ea-modal-add-invoice-payment').ea_modal();
		},
		submit: function (e) {
			e.preventDefault();
			ea_invoice_form.block();
			var data = $('#ea-invoice-form').serializeObject();
			$.post(ajaxurl, data, function (json) {
				$.eaccounting_redirect(json);
			}).always(function (json) {
				ea_invoice_form.unblock();
			});
		},
		modal: {
			init: function (e, plugin) {
				if ('modal-add-line-item' === plugin.modal_id) {
					plugin.$modal.on('change', '.ea-select2', function () {
						if (!$(this).closest('tr').is(':last-child')) {
							return;
						}
						//destroy select2
						$('.ea-select2', plugin.$modal).select2('destroy');
						var item_table = $(this).closest('table.widefat'),
							item_table_body = item_table.find('tbody'),
							row = $(this).closest('tr')
								.clone().find('.ea-select2').val('').end()
								.find('[type="text"]').val(1).end();
						item_table_body.append(row);
						item_table_body.find('tr').each(function () {
							var index = Array(1).fill(null).map(() => Math.random().toString(10).substr(2)).join('');
							$(this).find(":input").each(function () {
								var name = $(this).attr('name');
								name = name.replace(/\[(\d+)\]/, '[' + (index) + ']');
								$(this).attr('name', name).attr('id', name);
							});
						});
						$(document.body).trigger('ea_select2_init');
					})
				} else if ('ea-modal-add-invoice-payment' === plugin.modal_id) {

				}
			},
			submitted: function (e, plugin, data) {
				if ('modal-add-line-item' === plugin.modal_id) {
					for (const key in data) {
						$('#ea-invoice-form .ea-invoice-line-items')
							.append($('<input type="hidden" name="' + key + '" value="' + data[key] + '">'))
					}

					$('body').trigger('ea_invoice_updated');
					plugin.close();
				}
			}
		}
	}

	ea_invoice_form.init();

});
