jQuery(function ($) {
	'use strict';
	var eaccounting_invoice_form = {
		$form: $('#ea-invoice-form'),
		init: function () {
			$('.ea-add-line-item', this.$form).on('click', this.add_line);
		},
		add_line:function (e){
			e.preventDefault();
			var title = $(this).data('modal-title');
			$.ea_modal({
				title:title,
				target:'#modal-add-invoice-item',
				onReady:function ($modal){
					$( '.ea-select2', $modal ).eaccounting_select2();
					$('#quantity', $modal).on('change keyup', eaccounting_invoice_form.number_input);
				},
				onSubmit:function ($modal, plugin){
					plugin.close();
					console.log(plugin.getFormData());
				}
			})
		},
		number_input:function (e){
			console.log(e);
			e.target.value = e.target.value.replace(/[^0-9.]/g, '');
		}
	};

	eaccounting_invoice_form.init();
});
