(function ($, window, document, undefined) {
	'use strict';
	var $document = $(document),
		$window = $(window),
		$body = $(document.body);
	window.eac_admin = {
		init: function () {
			var self = this;
			self.select2('.eac_select2');
			self.datepicker('.eac_datepicker');
			self.tooltip('.eac_tooltip');
		},
		select2: function (el) {
			// if ('undefined' === typeof $.selectWoo) {
			// 	console.warn('Select2 is not loaded.');
			// 	return;
			// }
			$(el).filter(':not(.enhanced)')
				.each(function () {
					var self = this;
					var options = {
						allowClear: $(self).data('allow-clear') && !$(self).prop('multiple') || true,
						placeholder: $(self).data('placeholder') || '',
						width: '100%',
						minimumInputLength: $(self).data('minimum-input-length') || 0,
						ajax: {
							url: eac_admin_js_vars.ajax_url,
							dataType: 'json',
							delay: 250,
							method: 'POST',
							data: function (params) {
								return {
									term: params.term,
									action: $(self).data('action'),
									type: $(self).data('type'),
									_wpnonce: eac_admin_js_vars.search_nonce,
									exclude: $(self).data('exclude'),
									include: $(self).data('include'),
									limit: $(self).data('limit'),
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
					if (!$(self).data('action')) {
						delete options.ajax;
					}
					$(this).selectWoo(options);
				});
		},
		datepicker: function (el) {
			if ('undefined' === typeof $.datepicker) {
				console.warn('jQuery UI Datepicker is not loaded.');
				return;
			}
			$(el).filter(':not(.enhanced)')
				.each(function () {
					console.log($(this).data('format'));
					$(this).datepicker({
						dateFormat: $(this).data('format') || 'mm-dd',
						changeMonth: $(this).data('change-month') || false,
						changeYear: $(this).data('change-year') || false,
						yearRange: $(this).data('year-range') || 'c-10:c+10',
						showButtonPanel: $(this).data('show-button-panel') || true,
						onClose: function (dateText, inst) {
							if ($(window.event.srcElement).hasClass('ui-datepicker-close')) {
								$element.val('');
							}
						},
					});
					$(this).addClass('enhanced');
				});
		},
		tooltip: function (el) {
			if ('undefined' === typeof $.tooltip) {
				console.warn('jQuery UI is not loaded.');
				return;
			}
			const self = this;
			$(el).filter(':not(.enhanced)')
				.each(function () {
					$(this).tooltip({
						content: function () {
							return $(self).prop('title');
						},
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
				});
		},
	}

	$(document).ready(function () {
		eac_admin.init();
	});

})(jQuery, window, document);
