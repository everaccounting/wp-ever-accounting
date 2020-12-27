<?php
/**
 * Prepares the query fields.
 *
 * @since 1.1.0
 *
 * @param $table
 * @param $qv
 *
 * @return string
 */

defined( 'ABSPATH' ) || die();

/**
 * Prepare query fields.
 *
 * @since 1.1.0
 *
 * @param string $table
 * @param array  $qv
 *
 * @return string
 */
function eaccounting_prepare_query_fields( &$qv, $table ) {
	if ( true === $qv['count_total'] ) {
		return 'COUNT(1)';
	}
	$re           = '/^\W?(?<table>[\w-]+\.)?(?<column>[\w-]+)\W?(\W+as\W+)?(?<alias>[\w-]+)?\W?$/i';
	$query_fields = '';
	if ( is_array( $qv['fields'] ) ) {
		$qv['fields'] = array_unique( $qv['fields'] );
		$query_fields = array();
		foreach ( $qv['fields'] as $field ) {
			preg_match( $re, $field, $matches );
			if ( ! empty( $matches['table'] ) || ! empty( $matches['column'] ) ) {
				$query_fields[] = preg_replace( '/[^0-9a-zA-Z\*\.\s\_]/', '', $field );
				continue;
			}

			$field          = sanitize_key( $field );
			$query_fields[] = "$table.`$field`";
		}

		return implode( ', ', $query_fields );
	} else {
		$query_fields = "$table.*";
	}

	return $query_fields;
}

/**
 * Prepare query from.
 *
 * @since 1.1.0
 *
 * @param $table
 *
 * @return string
 */
function eaccounting_prepare_query_from( $table ) {
	global $wpdb;
	$query_from = " FROM {$wpdb->prefix}$table as $table";

	return $query_from;
}

/**
 * Prepares the query where.
 *
 * @since 1.1.0
 *
 * @param $table
 * @param $qv
 *
 * @return string
 */
function eaccounting_prepare_query_where( &$qv, $table ) {
	global $wpdb;
	$query_where = '';
	if ( ! empty( $qv['include'] ) ) {
		$include      = implode( ',', wp_parse_id_list( $qv['include'] ) );
		$query_where .= " AND $table.`id` IN ($include)";
	} elseif ( ! empty( $qv['exclude'] ) ) {
		$exclude      = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
		$query_where .= " AND $table.`id` NOT IN ($exclude)";
	}

	// Status
	if ( isset( $qv['status'] ) && in_array( $qv['status'], array( 'active', 'inactive' ), true ) ) {
		$qv['enabled'] = 'active' === $qv['status'] ? '1' : '0';
	}
	if ( isset( $qv['enabled'] ) && '' !== $qv['enabled'] ) {
		$query_where .= $wpdb->prepare( " AND $table.`enabled` = %d", $qv['enabled'] );
	}

	//search
	if ( ! empty( $qv['search'] ) && ! empty( $qv['search_cols'] ) ) {
		$searches    = array();
		$query_where = ' AND (';
		foreach ( $qv['search_cols'] as $col ) {
			foreach ( explode( ' ', $qv['search'] ) as $word ) {
				$searches[] = $wpdb->prepare( $col . ' LIKE %s', '%' . $wpdb->esc_like( $word ) . '%' );
			}
		}
		$query_where .= implode( ' OR ', $searches );
		$query_where .= ')';
	}

	// Date queries are allowed for the subscription creation date.
	if ( ! empty( $qv['date_created'] ) && is_array( $qv['date_created'] ) ) {
		$query_where = eaccounting_sql_parse_date_query( $qv['date_created'], "$table.date_created" );
	}

	return $query_where;
}

/**
 * Prepare query limit.
 *
 * @since 1.1.0
 *
 * @param $qv
 *
 * @return string
 */
function eaccounting_prepare_query_limit( &$qv ) {
	global $wpdb;
	$query_limit = '';
	// If count do no proceed.
	if ( true === $qv['count_total'] ) {
		return '';
	}

	if ( isset( $qv['number'] ) && $qv['number'] > 0 ) {
		if ( $qv['offset'] ) {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['offset'], $qv['number'] );
		} else {
			$query_limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['number'] * ( $qv['paged'] - 1 ), $qv['number'] );
		}
	}

	return $query_limit;
}

/**
 * Parse orderby.
 *
 * @since 1.1.0
 *
 * @param $table
 * @param $qv
 *
 * @return string
 */
function eaccounting_prepare_query_orderby( &$qv, $table ) {
	// If count do no proceed.
	if ( true === $qv['count_total'] ) {
		return '';
	}

	$qv['order'] = isset( $qv['order'] ) ? strtoupper( $qv['order'] ) : '';
	$order       = eaccounting_sql_parse_order( $qv['order'] );

	// Default order is by 'id' (latest transactions).
	if ( empty( $qv['orderby'] ) ) {
		$qv['orderby'] = array( 'id' );
	}

	// 'orderby' values may be an array, comma- or space-separated list.
	$ordersby = array_filter( wp_parse_list( $qv['orderby'] ) );

	$orderby_array = array();
	foreach ( $ordersby as $_key => $_value ) {

		if ( is_int( $_key ) ) {
			// Integer key means this is a flat array of 'orderby' fields.
			$_orderby = $_value;
			$_order   = $order;
		} else {
			// Non-integer key means that the key is the field and the value is ASC/DESC.
			$_orderby = $_key;
			$_order   = $_value;
		}

		$parsed = eaccounting_sql_parse_orderby( $_orderby, $table, $qv );

		if ( $parsed ) {
			$orderby_array[] = $parsed . ' ' . eaccounting_sql_parse_order( $_order );
		}
	}

	// If no valid clauses were found, order by id.
	if ( empty( $orderby_array ) ) {
		$orderby_array[] = "id $order";
	}

	return 'ORDER BY ' . implode( ', ', $orderby_array );
}

/**
 * Parse and sanitize 'orderby' keys passed to the transactions query.
 *
 * @since 1.1.0
 *
 * @param string $orderby Alias for the field to order by.
 * @param string $table   The current table.
 *
 * @param array  $qv
 *
 * @return string Value to use in the ORDER clause, if `$orderby` is valid.
 */
function eaccounting_sql_parse_orderby( $orderby, $table, &$qv = array() ) {

	$_orderby = '';
	if ( is_array($qv['orderby_cols']) && in_array(
		$orderby,
		$qv['orderby_cols'],
		true
	) ) {
		$_orderby = "$table.`$orderby`";
	} elseif ( 'id' === strtolower( $orderby ) ) {
		$_orderby = "$table.id";
	} elseif ( 'include' === $orderby && ! empty( $qv['include'] ) ) {
		$include     = wp_parse_id_list( $qv['include'] );
		$include_sql = implode( ',', $include );
		$_orderby    = "FIELD( $table.id, $include_sql )";
	}

	return $_orderby;
}

/**
 * Parse an 'order' query variable and cast it to ASC or DESC as necessary.
 *
 * @since 1.1.0
 *
 * @param string $order The 'order' query variable.
 *
 * @return string The sanitized 'order' query variable.
 */
function eaccounting_sql_parse_order( $order ) {
	if ( ! is_string( $order ) || empty( $order ) ) {
		return 'DESC';
	}

	if ( 'ASC' === strtoupper( $order ) ) {
		return 'ASC';
	} else {
		return 'DESC';
	}
}

/**
 * Parse date query.
 *
 * @since 1.1.0
 *
 * @param string $table
 * @param array  $date_query
 *
 * @return string
 */
function eaccounting_sql_parse_date_query( $date_query, $column ) {
	$query_date = '';
	// Date queries are allowed for the subscription creation date.
	if ( ! empty( $date_query ) && is_array( $date_query ) ) {
		$date_created_query = new \WP_Date_Query( $date_query, "$column" );
		$query_date        .= $date_created_query->get_sql();
	}

	return $query_date;
}
