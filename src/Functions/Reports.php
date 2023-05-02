<?php

defined( 'ABSPATH' ) || exit;

/**
 * Get receivable summary.
 *
 * @param string $period Period.
 *
 * @since 1.0.0
 */
function eac_get_receivable_amount_summary( $period = 'this_month' ) {
	$summery = get_transient( 'eac_receivable_amount_summary_' . $period );
	if ( false === $summery ) {
		$summery = array(
			'total'   => 0,
			'open'    => 0,
			'overdue' => 0,
		);

		// Convert period to date range.
		switch ( $period ) {
			case 'last_month':
				$start_date = date( 'Y-m-01', strtotime( 'last month' ) );
				$end_date   = date( 'Y-m-t', strtotime( 'last month' ) );
				break;
			case 'this_year':
				$start_date = date( 'Y-01-01' );
				$end_date   = date( 'Y-12-31' );
				break;
			case 'last_year':
				$start_date = date( 'Y-01-01', strtotime( 'last year' ) );
				$end_date   = date( 'Y-12-31', strtotime( 'last year' ) );
				break;
			default:
				$start_date = date( 'Y-m-01' );
				$end_date   = date( 'Y-m-t' );
				break;
		}
	}
}


/**
 * Retrieves key/label pairs of date filter options for use in a drop-down.
 *
 * @since 1.1.6
 *
 * @return array Key/label pairs of date filter options.
 */
function eac_get_date_filter_options() {
	$options = array(
		'today'        => __( 'Today', 'wp-ever-accounting' ),
		'yesterday'    => __( 'Yesterday', 'wp-ever-accounting' ),
		'this_week'    => __( 'This Week', 'wp-ever-accounting' ),
		'last_week'    => __( 'Last Week', 'wp-ever-accounting' ),
		'last_30_days' => __( 'Last 30 Days', 'wp-ever-accounting' ),
		'this_month'   => __( 'This Month', 'wp-ever-accounting' ),
		'last_month'   => __( 'Last Month', 'wp-ever-accounting' ),
		'this_quarter' => __( 'This Quarter', 'wp-ever-accounting' ),
		'last_quarter' => __( 'Last Quarter', 'wp-ever-accounting' ),
		'this_year'    => __( 'This Year', 'wp-ever-accounting' ),
		'last_year'    => __( 'Last Year', 'wp-ever-accounting' ),
		'custom'       => __( 'Custom', 'wp-ever-accounting' ),
	);

	return apply_filters( 'wp_ever_accounting_date_filter_options', $options );
}

/**
 * Parse date range from date filter.
 *
 * @param string $date_filter Date filter.
 *
 * @since 1.1.6
 *
 * @return array
 */
function eac_parse_date_range_filter( $date_filter ) {
	$range = array(
		'start_date' => '',
		'end_date'   => '',
	);
	switch ( $date_filter ) {
		case 'today':
			$range['start_date'] = date( 'Y-m-d' );
			$range['end_date']   = date( 'Y-m-d' );
			break;
		case 'yesterday':
			$range['start_date'] = date( 'Y-m-d', strtotime( 'yesterday' ) );
			$range['end_date']   = date( 'Y-m-d', strtotime( 'yesterday' ) );
			break;
		case 'this_week':
			$range['start_date'] = date( 'Y-m-d', strtotime( 'this week' ) );
			$range['end_date']   = date( 'Y-m-d' );
			break;
		case 'last_week':
			$range['start_date'] = date( 'Y-m-d', strtotime( 'last week' ) );
			$range['end_date']   = date( 'Y-m-d', strtotime( 'last week' ) );
			break;
		case 'last_30_days':
			$range['start_date'] = date( 'Y-m-d', strtotime( '-30 days' ) );
			$range['end_date']   = date( 'Y-m-d' );
			break;
		case 'this_month':
			$range['start_date'] = date( 'Y-m-01' );
			$range['end_date']   = date( 'Y-m-d' );
			break;
		case 'last_month':
			$range['start_date'] = date( 'Y-m-01', strtotime( 'last month' ) );
			$range['end_date']   = date( 'Y-m-t', strtotime( 'last month' ) );
			break;
		case 'this_quarter':
			$range['start_date'] = date( 'Y-m-01', strtotime( 'first day of this quarter' ) );
			$range['end_date']   = date( 'Y-m-d' );
			break;
		case 'last_quarter':
			$range['start_date'] = date( 'Y-m-01', strtotime( 'first day of last quarter' ) );
			$range['end_date']   = date( 'Y-m-t', strtotime( 'last day of last quarter' ) );
			break;

		case 'this_year':
			$range['start_date'] = date( 'Y-01-01' );
			$range['end_date']   = date( 'Y-m-d' );
			break;
		case 'last_year':
			$range['start_date'] = date( 'Y-01-01', strtotime( 'last year' ) );
			$range['end_date']   = date( 'Y-12-31', strtotime( 'last year' ) );
			break;

		default:
			$range['start_date'] = date( 'Y-m-d' );
			$range['end_date']   = date( 'Y-m-d' );
			break;

	}

	return $range;
}
