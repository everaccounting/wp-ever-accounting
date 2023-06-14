/* global eac_admin_vars */
(function ($, window, document, undefined) {
	'use strict';
	var eac_admin = {
		init: function () {
			$(document)
				.on('click', '.eac-dropdown__button', this.toggle_dropdown)
				.on('click', '.del', this.handle_delete)
				.on('keyup change', '.eac_input_decimal', this.input_decimal)
				.on('block', 'form.eac-ajax-form', this.block_form)
				.on('unblock', 'form.eac-ajax-form', this.unblock_form)
				.on('submit', 'form.eac-ajax-form', this.submit_form)
				.on('init-select2', this.init_select2)
				.on('init-tooltip', this.init_tooltip)
				.on('init-datepicker', this.init_datepicker)
				.on('eac_drawer_ready', this.triggerEvents)
				.on('ready', this.triggerEvents);
		},
		toggle_dropdown: function (e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).closest('.eac-dropdown').toggleClass('is--open');

			$(document).on('click', function () {
				$('.eac-dropdown').removeClass('is--open');
			});
		},
		handle_delete: function (e) {
			// confirmation alert.
			if (!confirm(eac_admin_vars.i18n.delete_confirmation)) {
				e.preventDefault();
				return false;
			}
		},
		input_decimal: function () {
			// allow only numbers and decimal point.
			var val = $(this).val();
			val = val.replace(/[^0-9.]/g, '');
			$(this).val(val);
		},
		block_form: function () {
			var $form = $(this);
			var $submit = $('[form="' + $form.attr('id') + '"]');
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
			var $submit = $('[form="' + $form.attr('id') + '"]');
			$submit.removeAttr('disabled');
			$form.unblock();
		},
		submit_form: function (e) {
			e.preventDefault();
			var $form = $(this);
			var data = $form.serializeAssoc();
			var $submit = $('[form="' + $form.attr('id') + '"]');
			// if submit button is disabled, do nothing.
			if ($submit.is(':disabled')) {
				return;
			}

			// block the form.
			$form.trigger('block');

			$.post(eac_admin_vars.ajax_url, data, function (json) {
				if (json.success) {
					if ($form.closest('.eac-drawer').length) {
						$form.closest('.eac-drawer').data('eac_drawer').close();
					} else {
						$.eac_redirect(json);
					}
				}
			}).always(function (json) {
				$form.trigger('unblock');
				$.eac_notification(json);
			});
		},
		init_select2: function () {
			$(':input.eac_select2').filter(':not(.enhanced)').each(function () {
				var select2_args = {
					allowClear: $(this).data('allow_clear') && !$(this).prop('multiple') || true,
					placeholder: $(this).data('placeholder') || $(this).attr('placeholder') || '',
					minimumInputLength: $(this).data('minimum_input_length') ? $(this).data('minimum_input_length') : 0,
					ajax: {
						url: eac_admin_vars.ajax_url,
						dataType: 'json',
						delay: 250,
						method: 'POST',
						data: function (params) {
							return {
								term: params.term,
								action: $(this).data('action'),
								type: $(this).data('type'),
								_wpnonce: eac_admin_vars.search_nonce,
								exclude: $(this).data('exclude'),
								include: $(this).data('include'),
								limit: $(this).data('limit'),
							};
						},
						processResults: function (data) {
							data.page = data.page || 1;
							return data;
						},
						cache: true
					}
				};
				// if data action is set then use ajax.
				if (!$(this).data('action')) {
					delete select2_args.ajax;
				}

				// if the select2 is within a drawer, then set the parent to the drawer.
				if ($(this).closest('.eac-form').length) {
					select2_args.dropdownParent = $(this).closest('.eac-form');
				}
				$(this).select2(select2_args).addClass('enhanced');
				$('.ea-select2').select2(select2_args);
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
		init_datepicker: function () {
			$('.eac-field-date').datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				yearRange: '-100:+0',
			});
			$('#eac_financial_year_start').datepicker({
				dateFormat: 'mm-dd',
				changeMonth: true,
				changeYear: false,
				yearRange: '-100:+0',
			});
		},
		triggerEvents: function () {
			$(document).trigger('init-select2');
			$(document).trigger('init-tooltip');
			$(document).trigger('init-datepicker');
		},
	};

	eac_admin.init();

}(jQuery, window, document));
