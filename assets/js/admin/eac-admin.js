(function ($, window, document, undefined) {
	'use strict';

	$(document)
		.on('init-select2', function () {
			$('.eac-select__select2, .eac-select__country').eac_select2();
			$('.eac-input__select[data-search]').eac_select2({
				ajax: {
					delay: 250,
					url: eac_admin_vars.ajax_url,
					method: 'POST',
					dataType: 'json',
					data(params) {
						return {
							action: 'eac_json_search',
							subtype: $(this).data('subtype'),
							type: $(this).data('search'),
							_wpnonce: eac_admin_vars.json_search,
							term: params.term,
							page: params.page,
						}
					},
					processResults(data, params) {
						params.page = params.page || 1;
						return data;
					}
				}
			});
		})
		.on('init-datepicker', function () {
			$('.eac-input__date ').datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				yearRange: '-100:+0',
			});
		})
		.on('init-price-input', function () {
			// console.log('init-price-input');
			$('#eac-item-form .eac-input__number').inputmask('decimal', {
				alias: 'numeric',
				groupSeparator: eac_admin_vars.thousand_sep,
				autoGroup: true,
				digits: eac_admin_vars.currency_precision,
				radixPoint: eac_admin_vars.decimal_sep,
				digitsOptional: false,
				allowMinus: false,
				prefix: eac_admin_vars.currency_symbol,
				placeholder: '0.000',
				rightAlign: 0,
				autoUnmask: true,
			});
		})
		.on('init-tooltip', function () {
			// Tooltips
			$('.eac-help-tip').tooltip({
				tooltipClass: 'eac-ui-tooltip',
				position: {
					my: 'center top',
					at: 'center bottom+10',
					collision: 'flipfit',
				},
				hide: {
					duration: 200,
				},
				show: {
					duration: 200,
				},
			});
		})
		.on('click', '.del', function (e) {
			// confirmation alert.
			if (!confirm(eac_admin_vars.i18n.delete_confirmation)) {
				e.preventDefault();
				return false;
			}
		})
		.on('change', '#eac-income-form #account_id', function () {
			var $this = $(this);
			var $form = $this.closest('form');
			var $currency_rate = $form.find('#currency_rate');
			var $currency_rate_wrapper = $currency_rate.closest('.eac-input-wrapper');
		})
		.on('submit', '.eac-form', function (e) {
			e.preventDefault();
			var $form = $(this);
			var isWithinModal = $form.closest('.ea-modal').length;
			var data = $form.serializeAssoc();
			var $submit = $('[form="' + $form.attr('id') + '"]');
			$submit.attr('disabled', 'disabled');
			$form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
			$.post(eac_admin_vars.ajax_url, data, function (json) {
				if (isWithinModal) {
					// find the ea-modal__close button and trigger a click.
					$form.closest('.ea-modal').find('.ea-modal__close').trigger('click');
					return;
				}
				$.eac_redirect(json);
			}).always(function (json) {
				$form.unblock();
				$submit.removeAttr('disabled');
				$.eac_notification(json);
			});
		})
		.on('click', '.eac-add-category', function (e) {
			e.preventDefault();
			var type = $(this).data('type') || 'item';
			$('#eac-category-modal').ea_modal({
				onSubmit: function ($modal) {
					console.log($modal)
				},
				onReady:function($plugin) {
					$(document).trigger('init-select2');
					$plugin.$modal.find('#type').closest('.eac-input-wrapper').remove();
					$plugin.$modal.find('#eac-category-form').append('<input type="hidden" name="type" id="type" value="' + type + '">');
				},
			})
		})
		.on('click', '.eac-add-customer', function (e) {
			e.preventDefault();
			$('#eac-customer-modal').ea_modal({
				onSubmit: function ($modal) {
					console.log($modal)
				},
				onReady:function() {
					$(document).trigger('init-select2');
					$(document).trigger('init-datepicker');
					$(document).trigger('init-price-input');
				},
			})
		})
		.on('click', '.eac-add-account', function (e) {
			e.preventDefault();
			$('#eac-account-modal').ea_modal({
				onSubmit: function ($modal) {
					console.log($modal)
				},
				onReady:function() {
					$(document).trigger('init-select2');
					$(document).trigger('init-datepicker');
					$(document).trigger('init-price-input');
				},
			})
		})

	$(document).ready(function () {
		$(document).trigger('init-select2');
		$(document).trigger('init-datepicker');
		$(document).trigger('init-price-input');
		$(document).trigger('init-tooltip');
	});


}(jQuery, window, document));
