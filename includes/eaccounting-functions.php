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


function eaccounting_get_transactions( $args = array(), $count = false ) {
	global $wpdb;
	$query_fields  = '';
	$query_from    = '';
	$query_where   = '';
	$query_orderby = '';
	$query_limit   = '';

	$default = array(
		'search'         => '',
		'orderby'        => 'created_at',
		'order'          => 'DESC',
		'date_before'    => '',
		'date_after'     => '',
		'fields'         => 'all',
		'search_columns' => array( 'description', 'reference' ),
		'per_page'       => 20,
		'page'           => 1,
		'offset'         => 0,
	);

	$args              = wp_parse_args( $args, $default );
	$transaction_table = 'transactions';
	$query_from        = "FROM ( SELECT *, 'expense' as type FROM $wpdb->ea_payments as payments UNION ALL SELECT *, 'income' as type FROM $wpdb->ea_revenues as revenues ) as transactions ";
	$query_where       = 'WHERE 1=1';

	//account_id
	if ( ! empty( $args['account_id'] ) ) {
		$query_where .= $wpdb->prepare( " AND $transaction_table.account_id= %s", absint( $args['account_id'] ) );
	}

	//exclude from others category
	$query_where .= " AND $transaction_table.category_id NOT IN ( SELECT id from $wpdb->ea_categories WHERE type='other') ";


	//amount
	if ( ! empty( $args['amount'] ) ) {
		$amount = trim( $args['amount'] );
		$number = preg_replace( '#[^0-9\.]#', '', $amount );

		if ( strpos( $amount, '>' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $transaction_table.amount > %f ", $number );
		} elseif ( strpos( $amount, '<' ) !== false ) {
			$query_where .= $wpdb->prepare( " AND $transaction_table.amount < %f ", $number );
		} else {
			$query_where .= $wpdb->prepare( " AND $transaction_table.amount = %f ", $number );
		}
	}

	//after date
	if ( ! empty( $args['date_after'] ) ) {
		$query_where .= $wpdb->prepare( " AND $transaction_table.paid_at >= date(%s) ", sanitize_text_field( $args['date_after'] ) );
	}

	//before date
	if ( ! empty( $args['date_before'] ) ) {
		$query_where .= $wpdb->prepare( " AND $transaction_table.paid_at <= date(%s) ", sanitize_text_field( $args['date_before'] ) );
	}

	//fields
	if ( is_array( $args['fields'] ) ) {
		$args['fields'] = array_unique( $args['fields'] );

		$query_fields = array();
		foreach ( $args['fields'] as $field ) {
			$field          = 'id' === $field ? 'id' : sanitize_key( $field );
			$query_fields[] = "$transaction_table.$field";
		}
		$query_fields = implode( ',', $query_fields );
	} elseif ( 'all' == $args['fields'] ) {
		$query_fields = "$transaction_table.*";
	} else {
		$query_fields = "$transaction_table.id";
	}

	//search
	$search = '';
	if ( isset( $args['search'] ) ) {
		$search = trim( $args['search'] );
	}
	if ( $search ) {
		$searches = array();
		$cols     = array_map( 'sanitize_key', $args['search_columns'] );
		$like     = '%' . $wpdb->esc_like( $search ) . '%';
		foreach ( $cols as $col ) {
			$searches[] = $wpdb->prepare( "$col LIKE %s", $like );
		}

		$query_where .= ' AND (' . implode( ' OR ', $searches ) . ')';
	}


	//ordering
	$order         = isset( $args['order'] ) ? esc_sql( strtoupper( $args['order'] ) ) : 'ASC';
	$order_by      = esc_sql( $args['orderby'] );
	$query_orderby = sprintf( " ORDER BY %s %s ", $order_by, $order );

	// limit
	if ( isset( $args['per_page'] ) && $args['per_page'] > 0 ) {
		if ( $args['offset'] ) {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $args['offset'], $args['per_page'] );
		} else {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $args['per_page'] * ( $args['page'] - 1 ), $args['per_page'] );
		}
	}

	if ( $count ) {
		return $wpdb->get_var( "SELECT count($transaction_table.id) $query_from $query_where" );
	}


	$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( is_array( $args['fields'] ) || 'all' == $args['fields'] ) {
		return $wpdb->get_results( $request );
	}

	return $wpdb->get_col( $request );
}

/**
 * Get expense by category
 *
 * @param null $start
 * @param null $end
 *
 * @return array
 * @since 1.0.0
 */
function eaccounting_get_expense_by_categories( $start = null, $end = null, $limit = 6 ) {

	global $wpdb;
	$query_fields = " category_id, SUM(amount) total ";
	$query_from   = " from $wpdb->ea_payments ";
	$query_where  = "WHERE category_id NOT IN (select id from $wpdb->ea_categories WHERE type='other') ";
	$limit        = " LIMIT $limit";
	if ( ! empty( $start ) ) {
		$query_where .= $wpdb->prepare( " AND paid_at >= DATE(%s)", date( 'Y-m-d', strtotime( $start ) ) );
	}

	if ( ! empty( $end ) ) {
		$query_where .= $wpdb->prepare( " AND paid_at <= DATE(%s)", date( 'Y-m-d', strtotime( $end ) ) );
	}

	$query_results = $wpdb->get_results( "select $query_fields $query_from $query_where group by category_id order by total desc $limit" );
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
