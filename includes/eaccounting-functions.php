<?php
defined( 'ABSPATH' ) || exit();

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


function eaccounting_get_transactions( $args, $count = false ) {
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
 * @since 1.0.0
 * @param null $start
 * @param null $end
 *
 * @return array
 */
function eaccounting_get_expense_by_categories( $start = null, $end = null ) {
	if ( empty( $start ) ) {
		$start = date( "1-1-Y" );
	}

	if ( empty( $end ) ) {
		$end = date( "31-12-Y" );
	}
	global $wpdb;
	$query_results = $wpdb->get_results( $wpdb->prepare( "select category_id, SUM(amount) total 
														  from $wpdb->ea_payments WHERE paid_at >= DATE(%s) AND paid_at <= DATE(%s) 
														  AND category_id NOT IN (select id from $wpdb->ea_categories WHERE type='other') 
														  group by category_id order by total desc", date( 'Y-m-d', strtotime( $start ) ), date( 'Y-m-d', strtotime( $end ) ) ) );
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
 * @since 1.0.0
 * @param null $start
 * @param null $end
 *
 * @return array
 */
function eaccounting_get_income_by_categories( $start = null, $end = null ) {
	if ( empty( $start ) ) {
		$start = date( "1-1-Y" );
	}

	if ( empty( $end ) ) {
		$end = date( "31-12-Y" );
	}
	global $wpdb;
	$query_results = $wpdb->get_results( $wpdb->prepare( "select category_id, SUM(amount) total 
														  from $wpdb->ea_revenues WHERE paid_at >= DATE(%s) AND paid_at <= DATE(%s) 
														  AND category_id NOT IN (select id from $wpdb->ea_categories WHERE type='other') 
														  group by category_id order by total desc", date( 'Y-m-d', strtotime( $start ) ), date( 'Y-m-d', strtotime( $end ) ) ) );
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
