jQuery(function ($) {
	var ea_quick_add = {
		init_plugins: function () {
			$('.ea-ajax-select2').eaccounting_select2();
			$('.ea-input-color').ea_color_picker();
		},
		success: function (res, modal) {
			modal.$el.unblock();
			$.eaccounting_notice(res.message, 'success');
			modal.closeModal();
		},
		error: function (error, modal) {
			modal.$el.unblock();
			if (!error || !error.message) {
				error.message = 'No data or status object returned in request';
			}
			$.eaccounting_notice(error.message, 'error');
			modal.enableSubmit();
		},
		category_modal: function (e, $el, modalData) {
			$(this).ea_backbone_modal({
				template: 'ea-modal-add-category',
				onSubmit: function (formData, modal) {
					modal.disableSubmit();
					modal.$el.blockThis();
					wp.ajax.post($.extend({}, formData, modalData))
						.then(function (res) {
							$el.eaccounting_select2({data: [{id: result.item.id, text: result.item.name}]}).trigger('change');
							ea_quick_add.success(res, modal);
						})
						.fail(function (error) {
							ea_quick_add.error(error, modal);
						});
				}
			});
		},
		account_modal: function (e, $el, modalData) {
			$(this).ea_backbone_modal({
				template: 'ea-modal-add-category',
				onSubmit: function (formData, modal) {
					modal.disableSubmit();
					modal.$el.blockThis();
					wp.ajax.post($.extend({}, formData, modalData))
						.then(function (res) {
							$el.eaccounting_select2({data: [{id: result.item.id, text: result.item.name}]}).trigger('change');
							ea_quick_add.success(res, modal);
						})
						.fail(function (error) {
							ea_quick_add.error(error, modal);
						});
				}
			});
		},
		init: function () {
			$(document)
				.on('ea_request_category_modal', this.category_modal)
				.on('ea_backbone_modal_loaded', this.init_plugins);
		}
	}

	ea_quick_add.init();

});
