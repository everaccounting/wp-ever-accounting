/* global eac_admin_vars */

jQuery(document).ready(($) => {
	'use strict';

	// Select2.
	$('.eac_select2').filter(':not(.enhanced)').each(function () {
		const $this = $(this);
		const options = {
			allowClear: $this.data('allow-clear') && !$this.prop('multiple') || true,
			placeholder: $this.data('placeholder') || '',
			width: '100%',
			minimumInputLength: $this.data('minimum-input-length') || 0,
			readOnly: $this.data('readonly') || false,
			ajax: {
				url: eac_admin_vars.ajax_url,
				dataType: 'json',
				delay: 250,
				method: 'POST',
				data: function (params) {
					return {
						term: params.term,
						action: $this.data('action'),
						type: $this.data('type'),
						subtype: $this.data('subtype'),
						_wpnonce: eac_admin_vars.search_nonce,
						exclude: $this.data('exclude'),
						include: $this.data('include'),
						limit: $this.data('limit'),
					};
				},
				processResults: function (data) {
					data.page = data.page || 1;
					return data;
				},
				cache: true
			}
		}
		// if data-action is not defined then return.
		if (!$this.data('action')) {
			delete options.ajax;
		}
		$this.addClass('enhanced').selectWoo(options);
	});

	// Datepicker.
	$('.eac_datepicker').filter(':not(.enhanced)').each(function () {
		const $this = $(this);
		const options = {
			dateFormat: $this.data('format') || 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true,
			showOtherMonths: true,
			selectOtherMonths: true,
			yearRange: '-100:+10',
			beforeShow: function () {
				$('#ui-datepicker-div')
					.removeClass('ui-datepicker')
					.addClass('eac-datepicker');
			},
		};
		$this.addClass('enhanced').datepicker(options);
	});

	// Tooltip.
	$('.eac_tooltip').filter(':not(.enhanced)').each(function () {
		const $this = $(this);
		const options = {
			position: {
				my: 'center bottom-15',
				at: 'center top',
			},
			tooltipClass: 'eac-tooltip',
		};
		$this.addClass('enhanced').tooltip(options);
	});

	// inputMask.
	$('.eac_inputmask').filter(':not(.enhanced)').each(function () {
		const $this = $(this);
		const options = {
			alias: $this.data('alias') || '',
			placeholder: $this.data('placeholder') || '',
			clearIncomplete: $this.data('clear-incomplete') || false,
		};
		$this.addClass('enhanced').inputmask(options);
	});

	// Number Input.
	$('.eac_number_input').filter(':not(.enhanced)').each(function () {
		const $this = $(this);
		$this.addClass('enhanced').on('input', function () {
			$this.value = $this.value.replace(/[^0-9]/g, '');
		});
	});

	// Decimal Input.
	$('.eac_decimal_input').filter(':not(.enhanced)').each(function () {
		const $this = $(this);
		$this.addClass('enhanced').on('input', function () {
			var val = $(this).val();
			val = val.replace(/[^0-9.]/g, '');
			$this.val(val);
		});
	});

});
