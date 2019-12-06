<?php
defined( 'ABSPATH' ) || exit();

function eaccounting_reports_tab_income_summery_render() {
	global $wpdb;
	$dates       = $totals = $incomes = $incomes_graph = $categories = [];
	$year        = ! empty( $_REQUEST['year'] ) ? absint( $_REQUEST['year'] ) : date( 'Y' );
	$category_id = ! empty( $_REQUEST['category_id'] ) ? absint( $_REQUEST['category_id'] ) : '';

	// check and assign year start
	$financial_start = eaccounting_get_financial_start();
	if ( ! is_null( $year ) ) {
		$financial_start['year'] = $year;
	}


	$categories   = $wpdb->get_results( "SELECT id, name FROM $wpdb->ea_categories WHERE type='income' ORDER BY name ASC", ARRAY_A );
	$category_ids = wp_list_pluck( $categories, 'id' );
	if ( ! empty( $category_id ) && in_array( $category_id, $category_ids ) ) {
		$categories = wp_list_filter( $categories, [ 'id' => $category_id ] );
	}
	$categories = wp_list_pluck( $categories, 'name', 'id' );
	$date_start = sprintf( "%d-%d-%d", $financial_start['year'], $financial_start['month'], $financial_start['day'] );
	$date       = new DateTime( $date_start );

	// Dates
	for ( $j = 1; $j <= 12; $j ++ ) {
		$dates[ $j ]                             = $date->format( 'F' );
		$incomes_graph[ $date->format( 'F-Y' ) ] = 0;
		// Totals
		$totals[ $dates[ $j ] ] = array(
			'amount' => 0,
		);

		foreach ( $categories as $category_id => $category_name ) {
			$incomes[ $category_id ][ $dates[ $j ] ] = [
				'category_id' => $category_id,
				'name'        => $category_name,
				'amount'      => 0,
			];
		}
		$date->modify( '+1 month' )->format( 'Y-m' );
	}

	$revenues = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->ea_revenues WHERE YEAR(paid_at)=%d AND category_id NOT IN (SELECT id from $wpdb->ea_categories WHERE type='other')", $financial_start['year'] ) );
	foreach ( $revenues as $revenue ) {
		$month      = date( 'F', strtotime( $revenue->paid_at ) );
		$month_year = date( 'F-Y', strtotime( $revenue->paid_at ) );

		if ( ! isset( $incomes[ $revenue->category_id ] ) || ! isset( $incomes[ $revenue->category_id ][ $month ] ) || ! isset( $incomes_graph[ $month_year ] ) ) {
			continue;
		}

		$incomes[ $revenue->category_id ][ $month ]['amount'] += $revenue->amount;
		$incomes_graph[ $month_year ]                         += $revenue->amount;
		$totals[ $month ]['amount']                           += $revenue->amount;
	}

	$data = compact( 'dates', 'categories', 'statuses', 'accounts', 'customers', 'incomes', 'totals', 'incomes_graph' );
	eaccounting_get_views( 'reports/income-summery.php', $data );
}

add_action( 'eaccounting_reports_tab_income_summery', 'eaccounting_reports_tab_income_summery_render' );
