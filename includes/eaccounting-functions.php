<?php
defined( 'ABSPATH' ) || exit();

/**
 * Get financial Start
 *
 * since 1.0.0
 * @return string
 */
function eaccounting_get_financial_start( $year = null, $format = 'Y-m-d' ) {
	$financial_start = apply_filters( 'eaccounting_financial_start', '01-01' );
	$setting         = explode( '-', $financial_start );
	$day             = ! empty( $setting[0] ) ? $setting[0] : '01';
	$month           = ! empty( $setting[1] ) ? $setting[1] : '01';
	$year            = empty( $year ) ? date( 'Y' ) : $year;

	$financial_year = new DateTime();
	$financial_year->setDate( $year, $month, $day );

//	$now = new DateTime();
//	if ( $now->diff( $financial_year )->format( '%a' ) > 0 ) {
//		$financial_year->sub( new \DateInterval( 'P1Y' ) );
//	}

	return $financial_year->format( $format );
}


/**
 * Get income by category
 *
 * @param null $start
 * @param null $end
 *
 * @return array
 * @since 1.0.0
 */
function eaccounting_get_income_by_categories( $start = null, $end = null ) {

	global $wpdb;
	$query_fields = " category_id, SUM(amount/currency_rate) total ";
	$query_from   = " from $wpdb->ea_revenues ";
	$query_where  = "WHERE category_id NOT IN (select id from $wpdb->ea_categories WHERE type='other') ";

	if ( ! empty( $start ) ) {
		$query_where .= $wpdb->prepare( " AND paid_at >= DATE(%s)", date( 'Y-m-d', strtotime( $start ) ) );
	}

	if ( ! empty( $end ) ) {
		$query_where .= $wpdb->prepare( " AND paid_at <= DATE(%s)", date( 'Y-m-d', strtotime( $end ) ) );
	}
	$query_results = $wpdb->get_results( "select $query_fields $query_from $query_where group by category_id order by total desc" );
	$results       = [];
	foreach ( $query_results as $query_result ) {
		$category  = eaccounting_get_category( $query_result->category_id );
		$results[] = array(
			'category_id' => $category->id,
			'color'       => $category->color,
			'name'        => $category->name,
			'total'       => $query_result->total,
		);
	}

	return $results;
}

/**
 * @param $contact_id
 *
 * @return float|string|null
 * @since 1.0.1
 */
function eaccounting_get_contact_payment_total( $contact_id ) {
	global $wpdb;
	if ( empty( $contact_id ) ) {
		return 0.00;
	}

	return $wpdb->get_var( $wpdb->prepare( "SELECT SUM(amount) FROM $wpdb->ea_payments WHERE contact_id=%d", absint( $contact_id ) ) );
}

/**
 * @param $contact_id
 *
 * @return float|string|null
 * @since 1.0.1
 */
function eaccounting_get_contact_revenue_total( $contact_id ) {
	global $wpdb;
	if ( empty( $contact_id ) ) {
		return 0.00;
	}

	return $wpdb->get_var( $wpdb->prepare( "SELECT SUM(amount) FROM $wpdb->ea_revenues WHERE contact_id=%d", absint( $contact_id ) ) );
}

/**
 * Makes internal API request for usages within PHP
 *
 * since 1.0.0
 *
 * @param $endpoint
 * @param array $args
 * @param string $method
 *
 * @return array
 */
function eaccounting_rest_request( $endpoint, $args = array(), $method = 'GET' ) {
	$request = new WP_REST_Request( $method, $endpoint );
	$request->set_query_params( $args );
	$response = rest_do_request( $request );
	$server   = rest_get_server();
	$result   = $server->response_to_data( $response, false );

	return $result;
}

/**
 * @return int|mixed
 * @since 1.0.2
 */
function eaccounting_get_creator_id() {

	$user_id = get_current_user_id();
	if ( empty( $user_id ) ) {
		$user = get_user_by( 'email', get_option( 'admin_email' ) );
		if ( $user && in_array( 'administrator', $user->roles ) ) {
			$user_id = $user->ID;
		}
	}

	if ( empty( $user_id ) ) {
		$users   = get_users( [
			'role'   => 'administrator',
			'fields' => 'ID'
		] );
		$user_id = reset( $users );
	}

	return $user_id;
}
