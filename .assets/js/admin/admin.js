/* global eac_admin_vars */

jQuery(document).ready(($) => {
	'use strict';

	var initializeUI = function () {
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

		// Polyfill for card padding for firefox.
		$('.eac-card').each(function () {
			if (!$(this).children('[class*="eac-card__"]').length && !parseInt($(this).css('padding'))) {
				$(this).css('padding', '8px 12px');
			}
		});
	}

	// Initialize UI.
	initializeUI();

	// Reinitialize UI when document body triggers 'eac-update-ui'.
	$(document.body).on('eac_update_ui', initializeUI);

	// Media Uploader.
	$('.eac_media_uploader').filter(':not(.enhanced)').each(function () {
		const $this = $(this);
		const options = {
			title: $this.data('title') || '',
			button: {
				text: $this.data('button') || '',
			},
			multiple: $this.data('multiple') || false,
		};
		$this.addClass('enhanced').on('click', function (e) {
			e.preventDefault();
			const frame = wp.media(options);
			frame.on('select', function () {
				const attachment = frame.state().get('selection').first().toJSON();
				$this.val(attachment.url);
			});
			frame.open();
		});
	});
});

jQuery(document).ready(($) => {

	var form = eac.modal.View.extend({
		el: 'form#eac-expense-form',

		events: {
			'change': 'onSubmit',
		},

		initialize() {
			console.log(this.$el.eacform().data());
			console.log('=== Preinitialize ===');
		},

		render() {
			console.log('=== Render ===');
			return this;
		},

		onSubmit(e) {
			e.preventDefault();
			console.log(this.$el.eacform().data());
		}
	});

	new form().render();

});
