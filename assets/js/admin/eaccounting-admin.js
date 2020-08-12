/* global eaccounting_admin_i10n */
jQuery(document).ready(function ($) {
	// $(document).ajaxStart(function () {
	// 	Pace.restart();
	// });
	//

	//initialize plugins
	$('.ea-input-date').datepicker({dateFormat: 'mm-dd-yy'});
	$('.ea-input-color').ea_color_picker();
	$('.ea-help-tip').tipTip();
	$('.ea-input-price').inputmask('decimal', {
		alias: 'numeric',
		groupSeparator: ',',
		autoGroup: true,
		digits: 2,
		radixPoint: '.',
		digitsOptional: false,
		allowMinus: false,
		prefix: '',
		placeholder: '0.000',
		rightAlign: 0,
		clearMaskOnLostFocus: false,
	});

	//status update
	$(document)
		.on('click', '.wp-list-table .ea_item_status_update', function () {
			var objectid = $(this).data('object_id'),
				nonce = $(this).data('nonce'),
				enabled = $(this).is(':checked') ? 1 : 0,
				objecttype = $(this).data('object_type');

			if (!objectid || !nonce || !objecttype) {
				$.eaccounting_notice('Item Missing some important property', 'error');
				return false;
			}
			wp.ajax.send({
				data: {
					objectid: objectid,
					nonce: nonce,
					enabled: enabled,
					objecttype: objecttype,
					action: 'eaccounting_item_status_update'
				},
				success: function (res) {
					$.eaccounting_notice(res, 'success');
				},
				error: function (error) {
					$.eaccounting_notice(error, 'error');
				}
			});
		});

	//dropdwown
	$(document)
		.on('click', function () {
			$('.ea-dropdown').removeClass('open');
		})
		.on('click', '.ea-dropdown-button', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$('.ea-dropdown').removeClass('open');
			$(this).closest('.ea-dropdown').toggleClass('open');
		});


	//forms

	$('#ea-category-form,#ea-currency-form').eaccounting_form();

});


// (function ($, eaccounting_admin_i10n) {
// 	$(function () {
// 		if ('undefined' === typeof eaccounting_admin_i10n) {
// 			return;
// 		}
//
// 		//initialize plugins
// 		$(document).ready(function () {
// 			$(document).ajaxStart(function () {
// 				Pace.restart();
// 			});

// $('.ea-input-date').datepicker({dateFormat: 'mm-dd-yy'});
// $('.ea-input-color').ea_color_picker();
// $('.ea-help-tip').tipTip();
// $('.ea-input-price').inputmask('decimal', {
// 	alias: 'numeric',
// 	groupSeparator: ',',
// 	autoGroup: true,
// 	digits: 2,
// 	radixPoint: '.',
// 	digitsOptional: false,
// 	allowMinus: false,
// 	prefix: '',
// 	placeholder: '0.000',
// 	rightAlign: 0,
// 	clearMaskOnLostFocus: false,
// });

//status update
// 	$(document)
// 		.on('click', '.wp-list-table .ea_item_status_update', function () {
// 			var objectid = $(this).data('object_id'),
// 				nonce = $(this).data('nonce'),
// 				enabled = $(this).is(':checked') ? 1 : 0,
// 				objecttype = $(this).data('object_type');
//
// 			if (!objectid || !nonce || !objecttype) {
// 				$.eaccounting_notice('Item Missing some important property', 'error');
// 				return false;
// 			}
//
// 			wp.ajax.post('eaccounting_item_status_update', {
// 				objectid: objectid,
// 				nonce: nonce,
// 				enabled: enabled,
// 				objecttype: objecttype
// 			}).then(function (result) {
// 				result = $.extend(true, {}, {message: '', redirect: ''}, result)
// 				$.eaccounting_notice(result.message, 'success');
// 			}).fail(function (error) {
// 				$.eaccounting_notice(error.message, 'error');
// 			});
// 		})
//
// 	//Handle forms
// 	$('#ea-account-form, #ea-revenue-form, #ea-payment-form').eaccounting_form();
// 	$('#account_id').eaccounting_creatable();
// })

//
// //Handle Forms
// var ea_modal_handlers = {
// 	init: function () {
// 		$(document)
// 			.on('select2:trigger_add', '.ea-select-account', this.handle_account_modal)
// 			.on('select2:trigger_add', '.ea-select-category', this.handle_category_modal)
// 	},
//
// 	/**
// 	 * Handle everything related to account modal form
// 	 * @param e
// 	 */
// 	handle_account_modal: function (e) {
// 		var $field = $(this);
// 		e.preventDefault();
// 		$(this).ea_backbone_modal({
// 			template: 'ea-modal-add-account',
// 			onReady: function () {
// 				$('.ea-ajax-select2').eaccounting_select2();
// 			},
// 			onSubmit: function (formData, modal) {
// 				modal.disableSubmit();
// 				modal.$el.blockThis();
// 				wp.ajax.post('eaccounting_edit_account', formData)
// 					.then(function (result) {
// 						$field.eaccounting_select2({data: [{id: result.item.id, text: result.item.name}]}).trigger('change');
// 						ea_modal_handlers.handle_success_request(error, modal);
// 					})
// 					.fail(function (error) {
// 						ea_modal_handlers.handle_failed_request(error, modal);
// 					});
// 			}
// 		});
// 	},
//
// 	/**
// 	 * Handle everything related to account modal form
// 	 * @param e
// 	 */
// 	handle_category_modal: function (e) {
// 		var $field = $(this),
// 			type = $field.data('type') || 'category_income';
// 			console.log(type);
// 		e.preventDefault();
// 		$(this).ea_backbone_modal({
// 			template: 'ea-modal-add-category',
// 			onReady: function () {
// 				$('.ea-ajax-select2').eaccounting_select2();
// 			},
// 			// onSubmit: function (formData, modal) {
// 			// 	modal.disableSubmit();
// 			// 	modal.$el.blockThis();
// 			// 	wp.ajax.post('eaccounting_edit_account', formData)
// 			// 		.then(function (result) {
// 			// 			$field.eaccounting_select2({data: [{id: result.item.id, text: result.item.name}]}).trigger('change');
// 			// 			ea_modal_handlers.handle_success_request(error, modal);
// 			// 		})
// 			// 		.fail(function (error) {
// 			// 			ea_modal_handlers.handle_failed_request(error, modal);
// 			// 		});
// 			// }
// 		});
// 	},
//
// 	handle_success_request: function (res, modal) {
// 		modal.$el.unblock();
// 		$.eaccounting_notice(res.message, 'success');
// 		modal.closeModal();
// 	},
//
// 	handle_failed_request: function (res, modal) {
// 		modal.$el.unblock();
// 		$.eaccounting_notice(res.message, 'error');
// 		modal.enableSubmit();
// 	}
// }
// ea_modal_handlers.init();
//
// $(document)
// 	.on('init-contact-modal', function (e, instance, data) {
// 		console.log(e, instance, data);
// 	})
//

// //currency form
// window.eaccounting.currency_form = {
// 	validate: function () {
// 		$('#ea-currency-form').validate({
// 			rules: {
// 				rate: {
// 					required: true,
// 					currency_rate: true,
// 					normalizer: function (value) {
// 						return $.trim(value);
// 					}
// 				}
// 			}
// 		});
// 	},
// 	handleSubmit: function (e) {
// 		e.preventDefault();
// 		var $form = $(this);
// 		$form.blockThis();
// 		wp.ajax.post($form.serializeArray())
// 			.then(function (result) {
// 				result = $.extend(true, {}, {message: '', redirect: ''}, result)
// 				console.log(result);
// 				$form.unblock();
// 				$.eaccounting_notice(result.message, 'success');
// 				eaccounting.redirect(result.redirect);
// 			})
// 			.fail(function (error) {
// 				console.log(error);
// 				$form.unblock();
// 				$.eaccounting_notice(error.message, 'error');
// 			});
// 	},
// 	handle_modal: function (e) {
// 		var $field = $(this);
// 		$(this).ea_backbone_modal({
// 			template: 'ea-modal-add-currency',
// 			onSubmit: function (formData, modal) {
// 				modal.disableSubmit();
// 				modal.$el.blockThis();
// 				wp.ajax.post('eaccounting_edit_currency', formData)
// 					.then(function (result) {
// 						console.log(result);
// 						modal.$el.unblock();
// 						$.eaccounting_notice(result.message, 'success');
// 						modal.closeModal();
// 						var title = result.item.name + '(' + result.item.symbol + ')';
// 						$field.eaccounting_select2({data: [{id: result.item.code, text: title, selected: true}]}).trigger('change');
// 						//$('.select2-selection__rendered[title="'+title+'"]').addClass('new-item');
// 					})
// 					.fail(function (error) {
// 						modal.$el.unblock();
// 						$.eaccounting_notice(error.message, 'error');
// 						modal.enableSubmit();
// 					});
// 			}
// 		});
// 		return false;
// 	},
// 	init: function () {
// 		this.validate();
// 		$(document)
// 			.on('ready', function () {
// 				$('#ea-currency-form #code').select2();
// 				$('#ea-currency-form #position').select2();
// 			})
// 			.on('select2:select', '#ea-currency-form #code', function (e) {
// 				var data = e.params.data;
// 				if (data.id === '') {
// 					return false;
// 				}
// 				try {
// 					currency = eaccounting_admin_i10n.global_currencies[data.id];
// 					$('#ea-currency-form #precision').val(currency.precision).change();
// 					$('#ea-currency-form #position').val(currency.position).change();
// 					$('#ea-currency-form #symbol').val(currency.symbol).change();
// 					$('#ea-currency-form #decimal_separator').val(currency.decimal_separator).change();
// 					$('#ea-currency-form #thousand_separator').val(currency.thousand_separator).change();
// 				} catch (e) {
// 					console.log(e.message)
// 				}
// 			})
// 			.on('submit', '#ea-currency-form', this.handleSubmit)
// 			.on('select2:trigger_add', '.eaccounting .currency_code_picker', this.handle_modal)
//
// 	}
// }
//
// eaccounting.account_form = {
// 	init:function () {
// 		$(document)
// 			.on('ready', function () {
// 				//$('#ea-account-form #currency_code').trigger('change')
// 			})
// 	}
// }
//
//
// eaccounting.currency_form.init();
//
// $(document)
// 	.on('change', '#ea-account-form #currency_code', function (e) {
// 		console.log('changed')
// 		var $field = $(this);
// 		var code = $field.val().trim();
// 		if (!code) {
// 			return false;
// 		}
// 		wp.ajax.post('eaccounting_get_currency', {code: code})
// 			.then(function (currency) {
// 				console.log(currency);
// 				$('#ea-account-form #opening_balance').inputmask('decimal', {
// 					alias: 'numeric',
// 					groupSeparator: currency.thousand_separator,
// 					autoGroup: true,
// 					digits: currency.precision,
// 					radixPoint: currency.decimal_separator,
// 					digitsOptional: false,
// 					allowMinus: false,
// 					prefix: currency.symbol,
// 					placeholder: '0.000',
// 					rightAlign: 0
// 				});
// 			})
// 			.fail(function (error) {
// 				$.eaccounting_notice(error.message, 'error');
// 			});
// 	}).change()
//
//
// //global element handler.
// $(document)
// 	.on('click', '.wp-list-table .ea_item_status_update', function () {
// 		var objectid = $(this).data('objectid'),
// 			nonce = $(this).data('nonce'),
// 			enabled = $(this).is(':checked') ? 1 : 0,
// 			objecttype = $(this).data('objecttype');
//
// 		if (!objectid || !nonce || !objecttype) {
// 			$.eaccounting_notice('Item Missing some important property', 'error');
// 			return false;
// 		}
//
// 		wp.ajax.post('eaccounting_item_status_update', {
// 			objectid: objectid,
// 			nonce: nonce,
// 			enabled: enabled,
// 			objecttype: objecttype
// 		}).then(function (result) {
// 			result = $.extend(true, {}, {message: '', redirect: ''}, result)
// 			$.eaccounting_notice(result.message, 'success');
// 		}).fail(function (error) {
// 			$.eaccounting_notice(error.message, 'error');
// 		});
// 	})
// 	.on('click', function () {
// 		$('.ea-dropdown').removeClass('open');
// 	})
// 	.on('click', '.ea-dropdown-button', function (e) {
// 		e.preventDefault();
// 		e.stopPropagation();
// 		$('.ea-dropdown').removeClass('open');
// 		$(this).closest('.ea-dropdown').toggleClass('open');
// 	})
// 	.on('ea_backbone_modal_loaded', function () {
// 		$('.ea-select2').select2();
// 	})
//
// $(document)
// 	.on('select2:trigger_add', '.ea-select-account', function () {
// 		$(this).ea_backbone_modal({
// 			template: 'ea-modal-add-account',
// 		});
// 	});


