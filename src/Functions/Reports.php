<?php

defined( 'ABSPATH' ) || exit;

/**
 * Get sales summary.
 *
 * @param string $period Period.
 *
 * @since 1.0.0
 */
function eac_get_income_summary( $period = 'this_month' ) {
	$summery = get_transient( 'eac_income_summary_' . $period );
	if ( false === $summery ) {

		$summery = array();
		// Convert period to date range.
		$range = eac_parse_date_range_filter( $period );

		// Get total sales.
		$summery['total'] = eac_get_total_income( $range['start_date'], $range['end_date'] );

		$summery = apply_filters( 'ever_accounting_sales_summary', $summery, $period );

		// Cache for 1 hour.
		set_transient( 'eac_income_summary_' . $period, $summery, HOUR_IN_SECONDS );
	}

	return $summery;
}

/**
 * Get total sales.
 *
 * @param string $start_date Start date.
 * @param string $end_date End date.
 *
 * @since 1.0.0
 */
function eac_get_total_income( $start_date, $end_date ) {
	global $wpdb;
	$total   = 0;
	$incomes = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT currency_code, currency_rate, SUM(amount / currency_rate) AS amount
     	FROM {$wpdb->prefix}ea_transactions
     	WHERE `type` ='income'
        AND id NOT IN (SELECT income_id FROM {$wpdb->prefix}ea_transfers)
        AND id NOT IN (SELECT expense_id FROM {$wpdb->prefix}ea_transfers)
        AND payment_date BETWEEN %s AND %s
		GROUP BY currency_code, currency_rate",
			esc_sql( $start_date ),
			esc_sql( $end_date )
		)
	);

	foreach ( $incomes as $income ) {
		$total += eac_money_to_base( $income->amount, $income->currency_code, $income->currency_rate );
	}

	return $total;
}

/**
 * Get expenses summary.
 *
 * @param string $period Period.
 *
 * @since 1.0.0
 */
function eac_get_expense_summary( $period = 'this_month' ) {
	$summery = get_transient( 'eac_expense_summary_' . $period );
	if ( false === $summery ) {

		$summery = array();
		// Convert period to date range.
		$range = eac_parse_date_range_filter( $period );

		// Get total expenses.
		$summery['total'] = eac_get_total_expenses( $range['start_date'], $range['end_date'] );

		$summery = apply_filters( 'ever_accounting_expenses_summary', $summery, $period );

		// Cache for 1 hour.
		set_transient( 'eac_expense_summary_' . $period, $summery, HOUR_IN_SECONDS );
	}

	return $summery;
}

/**
 * Get total expenses.
 *
 * @param string $start_date Start date.
 * @param string $end_date End date.
 *
 * @since 1.0.0
 */
function eac_get_total_expenses( $start_date, $end_date ) {
	global $wpdb;

	return $wpdb->get_var(
		$wpdb->prepare(
			"SELECT SUM(amount/currency_rate) amount
	 	FROM {$wpdb->prefix}ea_transactions
	 	WHERE `type` ='expense'
		AND id NOT IN (SELECT income_id FROM {$wpdb->prefix}ea_transfers)
		AND id NOT IN (SELECT expense_id FROM {$wpdb->prefix}ea_transfers)
		AND payment_date BETWEEN %s AND %s",
			esc_sql( $start_date ),
			esc_sql( $end_date )
		)
	);
}

/**
 * Get profit summary.
 *
 * @param string $period Period.
 *
 * @since 1.0.0
 */
function eac_get_profit_summary( $period = 'this_month' ) {
	$summery = get_transient( 'eac_profit_summary_' . $period );
	if ( false === $summery ) {

		$summery = array();
		// Convert period to date range.
		$range = eac_parse_date_range_filter( $period );

		// Get total profit.
		$summery['total'] = eac_get_total_profit( $range['start_date'], $range['end_date'] );

		$summery = apply_filters( 'ever_accounting_profit_summary', $summery, $period );

		// Cache for 1 hour.
		set_transient( 'eac_profit_summary_' . $period, $summery, HOUR_IN_SECONDS );
	}

	return $summery;
}

/**
 * Get total profit.
 *
 * @param string $start_date Start date.
 * @param string $end_date End date.
 *
 * @since 1.0.0
 */
function eac_get_total_profit( $start_date, $end_date ) {
	$total_income  = eac_get_total_income( $start_date, $end_date );
	$total_expense = eac_get_total_expenses( $start_date, $end_date );

	return $total_income - $total_expense;
}

/**
 * Retrieves key/label pairs of date filter options for use in a drop-down.
 *
 * @since 1.1.6
 *
 * @return array Key/label pairs of date filter options.
 */
function eac_get_dates_filter_options() {
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

	return apply_filters( 'wp_ever_accounting_dates_filter_options', $options );
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
function eac_parse_date_range_filter( $date_filter = '' ) {
	switch ( $date_filter ) {
		case 'yesterday':
			$range['start_date'] = wp_date( 'Y-m-d', strtotime( 'yesterday' ) );
			$range['end_date']   = wp_date( 'Y-m-d', strtotime( 'yesterday' ) );
			break;
		case 'this_week':
			$range['start_date'] = wp_date( 'Y-m-d', strtotime( 'this week' ) );
			$range['end_date']   = wp_date( 'Y-m-d' );
			break;
		case 'last_week':
			$range['start_date'] = wp_date( 'Y-m-d', strtotime( 'last week' ) );
			$range['end_date']   = wp_date( 'Y-m-d', strtotime( 'last week' ) );
			break;
		case 'last_30_days':
			$range['start_date'] = wp_date( 'Y-m-d', strtotime( '-30 days' ) );
			$range['end_date']   = wp_date( 'Y-m-d' );
			break;
		case 'last_month':
			$range['start_date'] = wp_date( 'Y-m-01', strtotime( 'last month' ) );
			$range['end_date']   = wp_date( 'Y-m-t', strtotime( 'last month' ) );
			break;
		case 'this_quarter':
			$range['start_date'] = wp_date( 'Y-m-01', strtotime( 'first day of this quarter' ) );
			$range['end_date']   = wp_date( 'Y-m-d' );
			break;
		case 'last_quarter':
			$range['start_date'] = wp_date( 'Y-m-01', strtotime( 'first day of last quarter' ) );
			$range['end_date']   = wp_date( 'Y-m-t', strtotime( 'last day of last quarter' ) );
			break;

		case 'this_year':
			$range['start_date'] = wp_date( 'Y-01-01' );
			$range['end_date']   = wp_date( 'Y-m-d' );
			break;
		case 'last_year':
			$range['start_date'] = wp_date( 'Y-01-01', strtotime( 'last year' ) );
			$range['end_date']   = wp_date( 'Y-12-31', strtotime( 'last year' ) );
			break;

		case 'this_month':
		default:
			$range['start_date'] = wp_date( 'Y-m-01' );
			$range['end_date']   = wp_date( 'Y-m-d' );
			break;
	}

	return $range;
}
