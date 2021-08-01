<?php
/**
 * EverAccounting Currency Functions.
 *
 * Currency related functions.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Currency;

defined( 'ABSPATH' ) || exit();
/**
 * Retrieves currency data given a currency id or currency object.
 *
 * @param int|object|Currency $currency currency to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N.Default OBJECT.
 * @param string $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return Currency|array|null
 * @since 1.1.0
 */
function eaccounting_get_currency( $currency, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $currency ) ) {
		return null;
	}

	if ( $currency instanceof Currency ) {
		$_currency = $currency;
	} elseif ( is_object( $currency ) ) {
		$_currency = new Currency( $currency );
	} elseif ( is_numeric( $currency ) ) {
		$_currency = Currency::get_data_by( $currency );
	} else {
		$_currency = Currency::get_data_by( $currency, 'code' );
	}

	if ( ! $_currency ) {
		return null;
	}

	$_currency = $_currency->filter( $filter );

	if ( ARRAY_A === $output ) {
		return $_currency->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_currency->to_array() );
	}

	return $_currency->filter( $filter );
}

/**
 *  Insert or update a currency.
 *
 * @param array|object|Currency $currency_arr An array, object, or currency object of data arguments.
 *
 * @return Currency|WP_Error The currency object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_currency( $currency_arr ) {
	global $wpdb;

	if ( $currency_arr instanceof Currency ) {
		$currency_arr = $currency_arr->to_array();
	} elseif ( $currency_arr instanceof stdClass ) {
		$currency_arr = get_object_vars( $currency_arr );
	}

	$defaults = array(
		'name'               => '',
		'code'               => '',
		'rate'               => 1,
		'number'             => '',
		'precision'          => 2,
		'subunit'            => 100,
		'symbol'             => '',
		'position'           => 'before',
		'decimal_separator'  => '.',
		'thousand_separator' => ',',
		'date_created'       => '',
	);

	// Are we updating or creating?
	$id          = null;
	$update      = false;
	$data_before = array();
	if ( ! empty( $item_data['id'] ) ) {
		$update      = true;
		$id          = absint( $item_data['id'] );
		$data_before = eaccounting_get_currency( $id, ARRAY_A );

		if ( is_null( $data_before ) ) {
			return new WP_Error( 'invalid_currency_id', __( 'Invalid currency id to update.', 'wp-ever-accounting' ) );
		}

		// Merge old and new fields with new fields overwriting old ones.
		$currency_arr = array_merge( $data_before, $currency_arr );
		$data_before  = $data_before->to_array();
	}

	$item_data = wp_parse_args( $currency_arr, $defaults );
	$data_arr  = eaccounting_sanitize_currency( $currency_arr, 'db' );

	// Check required
	if ( empty( $data_arr['code'] ) ) {
		return new WP_Error( 'invalid_currency_code', esc_html__( 'Currency code is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['rate'] ) ) {
		return new WP_Error( 'invalid_currency_rate', esc_html__( 'Currency rate id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['symbol'] ) ) {
		return new WP_Error( 'invalid_currency_symbol', esc_html__( 'Currency symbol is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['position'] ) ) {
		return new WP_Error( 'invalid_currency_position', esc_html__( 'Currency position is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['decimal_separator'] ) ) {
		return new WP_Error( 'invalid_currency_decimal_separator', esc_html__( 'Currency decimal separator is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['thousand_separator'] ) ) {
		return new WP_Error( 'invalid_currency_thousand_separator', esc_html__( 'Currency thousand separator is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
		$data_arr['date_created'] = current_time( 'mysql' );
	}

	$fields = array_keys( $defaults );
	$data   = wp_array_slice_assoc( $data_arr, $fields );

	/**
	 * Filters currency data before it is inserted into the database.
	 *
	 * @param array $data Data to be inserted.
	 * @param array $data_arr Sanitized data.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_currency', $data, $data_arr );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );

	if ( $update ) {

		/**
		 * Fires immediately before an existing currency item is updated in the database.
		 *
		 * @param int $id Currency id.
		 * @param array $data Currency data to be inserted.
		 * @param array $changes Currency data to be updated.
		 * @param array $data_arr Sanitized currency item data.
		 * @param array $data_before Currency previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_currency', $id, $data, $data_arr, $data_before );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_currencies', $data, $where, $data_before ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update currency in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing currency is updated in the database.
		 *
		 * @param int $id Currency id.
		 * @param array $data Currency data to be inserted.
		 * @param array $changes Currency data to be updated.
		 * @param array $data_arr Sanitized Currency data.
		 * @param array $data_before Currency previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_currency', $id, $data, $data_arr, $data_before );
	} else {

		/**
		 * Fires immediately before an existing currency is inserted in the database.
		 *
		 * @param array $data Currency data to be inserted.
		 * @param string $data_arr Sanitized currency data.
		 * @param array $item_data Currency data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_currency', $data, $data_arr, $item_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_currencies', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert currency into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$id = (int) $wpdb->insert_id;

		/**
		 * Fires immediately after an existing currency is inserted in the database.
		 *
		 * @param int $id Currency id.
		 * @param array $data Currency has been inserted.
		 * @param array $data_arr Sanitized currency data.
		 * @param array $item_data Currency data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_currency', $id, $data, $data_arr, $item_data );
	}

	// Clear cache.
	wp_cache_delete( $id, 'ea_currencies' );
	wp_cache_set( 'last_changed', microtime(), 'ea_currencies' );

	// Get new currency object.
	$currency = eaccounting_get_currency( $id );

	/**
	 * Fires once a currency has been saved.
	 *
	 * @param int $id Currency id.
	 * @param Currency $currency Currency object.
	 * @param bool $update Whether this is an existing currency being updated.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_saved_currency', $id, $currency, $update, $data_arr, $data_before );

	return $currency;
}

/**
 * Delete a currency.
 *
 * @param int $currency_id Currency id.
 *
 * @return Currency |false|null Currency data on success, false or null on failure.
 * @since 1.1.0
 */
function eaccounting_delete_currency( $currency_id ) {
	global $wpdb;

	$currency = eaccounting_get_currency( $currency_id );
	if ( ! $currency || ! $currency->exists() ) {
		return false;
	}

	/**
	 * Filters whether a currency delete should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Currency $currency contact object.
	 *
	 * @since 1.2.1
	 */
	$check = apply_filters( 'eaccounting_pre_delete_currency', null, $currency );
	if ( null !== $check ) {
		return $check;
	}

	/**
	 * Fires before a currency is deleted.
	 *
	 * @param int $currency_id Contact id.
	 * @param Currency $currency currency object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_currency()
	 */
	do_action( 'eaccounting_before_delete_currency', $currency_id, $currency );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_currencies', array( 'id' => $currency_id ) );
	if ( ! $result ) {
		return false;
	}

	wp_cache_delete( $currency_id, 'ea_currencies' );
	wp_cache_set( 'last_changed', microtime(), 'ea_currencies' );

	/**
	 * Fires after a currency is deleted.
	 *
	 * @param int $currency_id contact id.
	 * @param Currency $currency contact object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_currency()
	 */
	do_action( 'eaccounting_delete_currency', $currency_id, $currency );

	return $currency;
}

/**
 * Get currency items.
 *
 * @param array $args
 *
 * @return array|int|null
 * @since 1.1.0
 *
 *
 */
function eaccounting_get_currencies( $args = array() ) {
	global $wpdb;
	$args = wp_parse_args(
		$args,
		array(
			'search'      => '',
			'fields'      => '*',
			'orderby'     => 'name',
			'order'       => 'ASC',
			'number'      => - 1,
			'offset'      => 0,
			'paged'       => 1,
			'return'      => 'objects',
			'count_total' => false,
		)
	);

	$qv           = apply_filters( 'eaccounting_get_currencies_args', $args );
	$table        = $wpdb->prefix . 'ea_currencies';
	$columns      = [ 'name', 'code', 'symbol', 'enabled', 'date_created' ];
	$qv['fields'] = wp_parse_list( $qv['fields'] );
	foreach ( $qv['fields'] as $index => $field ) {
		if ( ! in_array( $field, $columns, true ) ) {
			unset( $qv['fields'][ $index ] );
		}
	}
	$fields = is_array( $qv['fields'] ) && ! empty( $qv['fields'] ) ? implode( ',', $qv['fields'] ) : '*';
	$where  = 'WHERE 1=1';

	if ( ! empty( $qv['include'] ) ) {
		$include = implode( ',', wp_parse_id_list( $qv['include'] ) );
		$where   .= " AND $table.`id` IN ($include)";
	} elseif ( ! empty( $qv['exclude'] ) ) {
		$exclude = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
		$where   .= " AND $table.`id` NOT IN ($exclude)";
	}

	if ( ! empty( $qv['status'] ) && ! in_array( $qv['status'], array( 'all', 'any' ), true ) ) {
		$status = eaccounting_string_to_bool( $qv['status'] );
		$status = eaccounting_bool_to_number( $status );
		$where  .= " AND $table.`enabled` = ('$status')";
	}

	if ( ! empty( $qv['date_created'] ) && is_array( $qv['date_created'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['date_created'], "{$table}.date_created" );
		$where              .= $date_created_query->get_sql();
	}

	$search_cols = array( 'name', 'code' );
	if ( ! empty( $qv['search'] ) ) {
		$searches = array();
		$where    .= ' AND (';
		foreach ( $search_cols as $col ) {
			$searches[] = $wpdb->prepare( $col . ' LIKE %s', '%' . $wpdb->esc_like( $qv['search'] ) . '%' );
		}
		$where .= implode( ' OR ', $searches );
		$where .= ')';
	}

	$order   = isset( $qv['order'] ) ? strtoupper( $qv['order'] ) : 'ASC';
	$orderby = isset( $qv['orderby'] ) && in_array( $qv['orderby'], $columns, true ) ? eaccounting_clean( $qv['orderby'] ) : "{$table}.id";

	$limit = '';
	if ( isset( $qv['number'] ) && $qv['number'] > 0 ) {
		if ( $qv['offset'] ) {
			$limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['offset'], $qv['number'] );
		} else {
			$limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['number'] * ( $qv['paged'] - 1 ), $qv['number'] );
		}
	}

	$select      = "SELECT {$fields}";
	$from        = "FROM {$wpdb->prefix}$table $table";
	$orderby     = "ORDER BY {$orderby} {$order}";
	$count_total = true === $qv['count_total'];
	$clauses     = compact( 'select', 'from', 'where', 'orderby', 'limit' );
	$cache_key   = 'query:' . md5( serialize( $qv ) ) . ':' . wp_cache_get_last_changed( 'ea_currencies' );
	$results     = wp_cache_get( $cache_key, 'ea_currencies' );
	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( "SELECT COUNT(id) $from $where" );
			wp_cache_set( $cache_key, $results, 'ea_currencies' );
		} else {
			$results = $wpdb->get_results( implode( ' ', $clauses ) );
			if ( in_array( $fields, array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'ea_currencies' );
					wp_cache_set( $item->name . '-' . $item->type, $item, 'ea_currencies' );
				}
			}
			wp_cache_set( $cache_key, $results, 'ea_currencies' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_currency', $results );
	}

	return $results;
}


/**
 * Sanitizes every currency field.
 *
 * If the context is 'raw', then the currency object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $currency The currency object or array
 * @param string $context Optional. How to sanitize currency fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Currency|array The now sanitized currency object or array
 * @see eaccounting_sanitize_currency_field()
 *
 * @since 1.2.1
 *
 */
function eaccounting_sanitize_currency( $currency, $context = 'raw' ) {
	if ( is_object( $currency ) ) {
		// Check if post already filtered for this context.
		if ( isset( $currency->filter ) && $context == $currency->filter ) {
			return $currency;
		}
		if ( ! isset( $currency->id ) ) {
			$currency->id = 0;
		}
		foreach ( array_keys( get_object_vars( $currency ) ) as $field ) {
			$currency->$field = eaccounting_sanitize_currency_field( $field, $currency->$field, $currency->id, $context );
		}
		$currency->filter = $context;
	} elseif ( is_array( $currency ) ) {
		// Check if post already filtered for this context.
		if ( isset( $currency['filter'] ) && $context == $currency['filter'] ) {
			return $currency;
		}
		if ( ! isset( $currency['id'] ) ) {
			$currency['id'] = 0;
		}
		foreach ( array_keys( $currency ) as $field ) {
			$currency[ $field ] = eaccounting_sanitize_currency_field( $field, $currency[ $field ], $currency['id'], $context );
		}
		$currency['filter'] = $context;
	}

	return $currency;
}

/**
 * Sanitizes a currency field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The currency Object field name.
 * @param mixed $value The currency Object value.
 * @param int $currency_id Currency id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 *
 */
function eaccounting_sanitize_currency_field( $field, $value, $currency_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) {
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters an currency field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the currency field.
		 * @param int $currency_id Currency id.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_edit_currency_{$field}", $value, $currency_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters a currency field value before it is sanitized.
		 *
		 * @param mixed $value Value of the currency field.
		 * @param int $currency_id Currency id.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_pre_currency_{$field}", $value, $currency_id );

	} else {
		// Use display filters by default.

		/**
		 * Filters the currency field sanitized for display.
		 *
		 * The dynamic portion of the filter name, `$field`, refers to the currency field name.
		 *
		 * @param mixed $value Value of the currency field.
		 * @param int $currency_id currency id.
		 * @param string $context Context to retrieve the currency field value.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_currency_{$field}", $value, $currency_id, $context );
	}

	return $value;
}

/**
 * Check if currency code is a valid one.
 *
 * @param $code
 *
 * @return string
 * @since 1.1.0
 *
 */
function eaccounting_sanitize_currency_code( $code ) {
	$codes = eaccounting_get_data( 'currencies' );
	$code  = strtoupper( $code );
	if ( empty( $code ) || ! array_key_exists( $code, $codes ) ) {
		return '';
	}

	return $code;
}
