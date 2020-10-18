/**
 * Handle the overview page widgets.
 * global eaccounting_overview_i10n
 * @since 1.0.2
 */
jQuery(document).ready(function ($) {
	var $overview_filter = $('#ea-overview-date-range');
	var startDate = $overview_filter.data('start');
	var endDate = $overview_filter.data('end');
	$overview_filter.daterangepicker({
		startDate: startDate,
		endDate: endDate,
		locale: eaccounting_overview_i10n.datepicker.locale,
		ranges: eaccounting_overview_i10n.datepicker.ranges,
	});
	$overview_filter.on('apply.daterangepicker', function (ev, picker) {
		var $form = $overview_filter.closest('form');
		$form
			.find('[name="start_date"]')
			.val(picker.startDate.format('YYYY-MM-DD'));
		$form
			.find('[name="end_date"]')
			.val(picker.endDate.format('YYYY-MM-DD'));
		$form.submit();
	});
});
