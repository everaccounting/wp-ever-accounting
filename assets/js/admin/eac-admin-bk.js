(function ($, window, document, undefined) {
	'use strict';

	var eac_admin = {
		init: function () {
			$(document)
				.on('init-select2', this.init_select2)
				.on('init-datepicker', this.init_datepicker)
				.on('init-price-input', this.init_price_input)
				.on('init-tooltip', this.init_tooltip)
				.on('init-drawer', this.init_drawer)
				.on('click', '.del', this.handle_delete)
				.on('submit', '.eac-form', this.form_submit)
				.on('click', '.eac-dropdown__button', this.toggle_dropdown)
				.on('eac_drawer_ready', this.init_drawer)
				.on('change', '.eac-form [name="is_taxable"]', this.toggle_tax_fields)
				.trigger('init-select2')
				.trigger('init-datepicker')
				.trigger('init-price-input')
				.trigger('init-tooltip');

			$('.eac-form [name="taxable"]').trigger('change');

			// Document form.
			$('.eac-document__form').on('change', '.eac-select-customer', this.change_customer_user);
		},
		init_select2: function () {
			$('.eac-select2').eac_select2();
			$('.ever-accounting select[data-eac-select2]').eac_select2({
				ajax: {
					delay: 250,
					url: eac_admin_vars.ajax_url,
					method: 'POST',
					dataType: 'json',
					data(params) {
						return {
							action: 'eac_json_search',
							args: $(this).data('query-args'),
							type: $(this).data('eac-select2'),
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
		},
		init_datepicker: function () {
			$('.eac-field-date').datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				yearRange: '-100:+0',
			});
		},
		init_price_input: function () {
			var base_currency = eac_admin_vars.base_currency;
			$('.eac-form').each(function () {
				var $form = $(this);
				var $currency_field = $form.find('[name="currency_code"]');
				var currency_code = $currency_field.val() || base_currency;
				var currency_data = eac_admin_vars.currencies[currency_code];

				$form.find('.eac-field-money').inputmask('decimal', {
					alias: 'numeric',
					groupSeparator: currency_data.thousand_sep,
					autoGroup: true,
					digits: currency_data.precision,
					radixPoint: currency_data.decimal_sep,
					digitsOptional: false,
					allowMinus: true,
					prefix: currency_data.position === 'before' ? currency_data.symbol : '',
					suffix: currency_data.position === 'after' ? currency_data.symbol : '',
				});

				// If there is any currency field, then we will update the price input mask when the currency field value changed
				if ($currency_field.length) {
					$currency_field.on('change', function () {
						$(document).trigger('init-price-input');
					});
				}
			});
		},
		init_tooltip: function () {
			$('.eac-tooltip').tooltip({
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
		},
		toggle_dropdown: function (e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).closest('.eac-dropdown').toggleClass('is--open');

			$(document).on('click', function () {
				$('.eac-dropdown').removeClass('is--open');
			});
		},
		init_drawer: function () {
			$(document)
				.trigger('init-select2')
				.trigger('init-datepicker')
				.trigger('init-price-input')
				.trigger('init-tooltip');
		},
		toggle_tax_fields: function () {
			var $this = $(this);
			var $form = $this.closest('.eac-form');
			var $tax_fields = $form.find('.eac-field--tax_ids');
			if ($this.val() === 'yes') {
				$tax_fields.show();
			} else {
				$tax_fields.hide();
			}
		},
		handle_delete: function () {
			// confirmation alert.
			if (!confirm(eac_admin_vars.i18n.delete_confirmation)) {
				e.preventDefault();
				return false;
			}
		},
		form_submit: function (e) {
			e.preventDefault();
			var $form = $(this);
			var data = $form.serializeAssoc();
			var $submit = $('[form="' + $form.attr('id') + '"]');
			// if submit button is disabled, do nothing.
			if ($submit.is(':disabled')) {
				return;
			}

			$submit.attr('disabled', 'disabled');
			$form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
			$.post(eac_admin_vars.ajax_url, data, function (json) {
				if (json.success) {
					if ($form.closest('.eac-drawer').length) {
						$form.closest('.eac-drawer').data('eac_drawer').close();
					} else {
						$.eac_redirect(json)
					}
				}
			}).always(function (json) {
				$form.unblock();
				$submit.removeAttr('disabled');
				$.eac_notification(json);
			});
		},
	};

	var eac_document = {
		init: function () {
			this.bindEvents();
		},
		bindEvents: function () {
			$('form.eac-document')
				.on('click', '.add-line-item', this.toggle_new_line_item)
				.on('change', '.select-new-item', this.select_new_line_item)
				.on('click', '.remove-line-item', this.remove_line_item)
				.on('block', this.block_form)
				.on('unblock', this.unblock_form)
				.on('updated', this.update_form)
				.on('submit', this.form_submit)
		},
		toggle_new_line_item: function (e) {
			e.preventDefault();
			var $this = $(this);
			var $form = $this.closest('form');
			var $new_line_item = $form.find('.eac-document__items-new-line');
			$new_line_item.toggle();
		},
		select_new_line_item: function (e) {
			e.preventDefault();
			var $this = $(this);
			var $form = $this.closest('form');
			var product_id = $this.val();
			var $btn = $form.find('.calculate-totals');
			if (product_id) {
				$form.append('<input type="hidden" name="items[][product_id]" value="' + product_id + '">');
				$btn.trigger('click')
			}
		},
		remove_line_item: function (e) {
			e.preventDefault();
			var $this = $(this);
			var $form = $this.closest('form');
			var $line_item = $this.closest('.eac-document__items-line');
			$line_item.remove();
			$form.find('.calculate-totals').trigger('click');
		},
		block_form: function () {
			var $form = $(this);
			var $btn = $form.find('.calculate-totals');
			var $submit = $('[form="' + $form.attr('id') + '"]');
			$btn.attr('disabled', 'disabled');
			$submit.attr('disabled', 'disabled');
			$form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
		},
		unblock_form: function () {
			var $form = $(this);
			var $btn = $form.find('.calculate-totals');
			var $submit = $('[form="' + $form.attr('id') + '"]');
			$submit.removeAttr('disabled');
			$btn.removeAttr('disabled');
			$form.unblock();
		},
		update_form: function () {
			$(document)
				.trigger('init-select2')
				.trigger('init-datepicker')
				.trigger('init-price-input')
				.trigger('init-tooltip');
		},
		form_submit: function (e) {
			e.preventDefault();
			var $form = $(this);
			var data = [];
			$form.find('input, select, textarea').each(function () {
				var name = $(this).attr('name');
				var value = $(this).val();
				if (name)
					data.push({name: name, value: value});
			});
			var $submit = $('[form="' + $form.attr('id') + '"]');
			// if submit button is disabled, do nothing.
			if ($submit.is(':disabled')) {
				return;
			}

			$submit.trigger('block')
			$form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
			$.post(eac_admin_vars.ajax_url, data, function (json) {
				if (json.success) {
					$.eac_redirect(json)
				}
			}).always(function (json) {
				$form.trigger('unblock')
				$submit.removeAttr('disabled');
				$.eac_notification(json);
			});
		}
	};

	var eac_invoice = {
		init: function () {
			this.bindEvents();
		},
		bindEvents: function () {
			$('form.eac-invoice-form')
				.on('click', '.calculate-totals', this.calculate_totals)
		},
		calculate_totals: function (e) {
			console.log('calculate_totals');
			e.preventDefault();
			var $this = $(this);
			var $form = $this.closest('form');
			var data = [{name: 'action', value: 'eac_calculate_invoice'}];
			$form.find('input, select, textarea').each(function () {
				var name = $(this).attr('name');
				var value = $(this).val();
				if (name && ('action' !== name))
					data.push({name: name, value: value});
			});
			// return false;
			var $submit = $('[form="' + $form.attr('id') + '"]');
			// if submit button is disabled, do nothing.
			if ($submit.is(':disabled')) {
				return false;
			}
			$form.trigger('block');
			$form.load(eac_admin_vars.ajax_url, data, function (response, status, xhr) {
				if (status === 'error') {
					$.eac_notification({
						success: false,
						message: xhr.statusText,
					});
				}
				$form.trigger('updated');
				$form.trigger('unblock');
			});
		}
	}

	$(document).ready(function () {
		eac_admin.init();
		eac_document.init();
		eac_invoice.init();
	});


}(jQuery, window, document));
