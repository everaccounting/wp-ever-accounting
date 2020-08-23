jQuery(document).ready(function ($) {
	var $overview_filter = $('#ea-overview-date-range');
	var startDate = $overview_filter.data('start');
	var endDate = $overview_filter.data('end');
	$overview_filter.daterangepicker({
		startDate: startDate,
		endDate: endDate,
		locale: {
			format: 'YYYY-MM-DD',
			separator: '  >>  ',
			applyLabel: 'Apply',
			cancelLabel: 'Cancel',
			fromLabel: 'From',
			toLabel: 'To',
			customRangeLabel: 'Custom',
			daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
			monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
			firstDay: 1,
		},
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'This Year': [moment().startOf('year'), moment().endOf('year')],
			'Last Year': [moment().startOf('year'), moment().endOf('year')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		}
	});
	$overview_filter.on('apply.daterangepicker', function(ev, picker) {
		var $form = $overview_filter.closest('form');
		$form.find('[name="start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
		$form.find('[name="end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
		$form.submit();
	});
});
