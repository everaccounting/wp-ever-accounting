<?php


function eaccounting_get_transactions( $args = array(), $count = false ) {
	global $wpdb;
	$query_fields  = '';
	$query_from    = '';
	$query_where   = '';
	$query_orderby = '';
	$query_limit   = '';

	$default = array(
		'type'           => '',
		'paid_at'        => '',
		'account_id'     => '',
		'category_id'    => '',
		'search'         => '',
		'order'          => 'DESC',
		'orderby'        => 'paid_at',
		'fields'         => 'all',
		'search_columns' => array( 'description', 'reference' ),
		'per_page'       => 20,
		'page'           => 1,
		'offset'         => 0,
	);

	$args = wp_parse_args( $args, $default );

	$transaction_table = 'transactions';
	$query_from        = "FROM ( SELECT *, 'expense' as type FROM $wpdb->ea_payments as payments UNION ALL SELECT *, 'income' as type FROM $wpdb->ea_revenues as revenues ) as transactions ";
	$query_where       = 'WHERE 1=1';

	//account_id
	if ( ! empty( $args['account_id'] ) ) {
		$account_ids = implode( ',', wp_parse_id_list( $args['account_id'] ) );
		$query_where .= " AND $transaction_table.account_id IN( {$account_ids} ) ";
	}

	//contact_id
	if ( ! empty( $args['contact_id'] ) ) {
		$contact_ids = implode( ',', wp_parse_id_list( $args['contact_id'] ) );
		$query_where .= " AND $transaction_table.contact_id IN( {$contact_ids} ) ";
	}

	//category_id
	if ( ! empty( $args['category_id'] ) ) {
		$category_ids = implode( ',', wp_parse_id_list( $args['category_id'] ) );
		$query_where  .= " AND $transaction_table.category_id IN( {$category_ids} ) ";
	}

	//type
	if ( ! empty( $args['type'] ) ) {
		$types = implode( "','", wp_parse_list($args['type']));
		$query_where  .= " AND $transaction_table.type IN( '$types' ) ";
	}

	//paid_at
	if ( ! empty( $args['paid_at'] ) ) {
		$query_where .= eaccounting_parse_date_query( $args['paid_at'], 'paid_at', $transaction_table );
	}


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
	$query_orderby = sprintf( " ORDER BY $transaction_table.%s %s ", $order_by, $order );

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

