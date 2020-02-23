<?php
defined( 'ABSPATH' ) || exit();

/**
 * Get financial Start
 *
 * since 1.0.0
 * @return array
 */
function eaccounting_get_financial_start( $year = null ) {
	$financial_start = apply_filters( 'eaccounting_financial_start', '01-01' );
	$setting         = explode( '-', $financial_start );
	$day             = ! empty( $setting[0] ) ? $setting[0] : '01';
	$month           = ! empty( $setting[1] ) ? $setting[1] : '01';
	$year            = empty( $year ) ? date( 'Y' ) : $year;

	return array(
		'year'  => $year,
		'month' => $month,
		'day'   => $day,
	);
}

/**
 * Display a WooCommerce help tip.
 *
 * @param string $tip Help tip text.
 * @param bool $allow_html Allow sanitized HTML if true or escape.
 *
 * @return string
 * @since  1.0.0
 *
 */
function eaccounting_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = htmlspecialchars(
			wp_kses(
				html_entity_decode( $tip ),
				array(
					'br'     => array(),
					'em'     => array(),
					'strong' => array(),
					'small'  => array(),
					'span'   => array(),
					'ul'     => array(),
					'li'     => array(),
					'ol'     => array(),
					'p'      => array(),
				)
			)
		);
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="eaccounting-help-tip" data-tip="' . $tip . '"></span>';
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
	$query_fields = " category_id, SUM(amount) total ";
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


function eaccounting_cashflow_income( $start = null, $end = null ) {
	if ( empty( $start ) ) {
		$start = date( "1-1-Y" );
	}

	if ( empty( $end ) ) {
		$end = date( "31-12-Y" );
	}
	global $wpdb;

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
 * @since 1.0.2
 * @return array|object|void|null
 */
function eaccounting_get_default_currency() {
	$default_currency_code = get_option( 'ea_default_currency', 'USD' );

	return eaccounting_get_currency( $default_currency_code, 'code' );
}
