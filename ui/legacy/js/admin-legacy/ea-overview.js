/**
 * Handle the overview page widgets.
 * global eaccounting_overview_i10n
 *
 * @since 1.0.2
 */
jQuery( document ).ready( function ( $ ) {
	const $overview_filter = $( '#ea-overview-date-range' );
	const startDate = $overview_filter.data( 'start' );
	const endDate = $overview_filter.data( 'end' );
	$overview_filter.daterangepicker( {
		startDate,
		endDate,
		locale: eaccounting_overview_i10n.datepicker.locale,
		ranges: eaccounting_overview_i10n.datepicker.ranges,
	} );
	$overview_filter.on( 'apply.daterangepicker', function ( ev, picker ) {
		const $form = $overview_filter.closest( 'form' );
		$form
			.find( '[name="start_date"]' )
			.val( picker.startDate.format( 'YYYY-MM-DD' ) );
		$form
			.find( '[name="end_date"]' )
			.val( picker.endDate.format( 'YYYY-MM-DD' ) );
		$form.submit();
	} );
} );
