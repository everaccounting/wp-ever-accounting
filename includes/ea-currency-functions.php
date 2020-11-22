<?php
/**
 * EverAccounting Currency Functions.
 *
 * Currency related functions.
 *
 * @since   1.1.0
 * @package EverAccounting
 */


defined( 'ABSPATH' ) || exit();

/**
 * Get currency data by code.
 *
 * @param $code
 *
 * @return bool|array
 * @since 1.1.0
 *
 */
function eaccounting_get_currency_data( $code ) {
	$codes = eaccounting_get_data( 'currencies' );

	return isset( $codes[ $code ] ) ? $codes[ $code ] : false;
}

/**
 * Main function for returning currency.
 *
 * This function is little different from rest
 * Even if the currency in the database doest not
 * exist it will it populate with default data.
 *
 * Whenever need to check existence of the object
 * in database must check $currency->exist()
 *
 * @param object|string|int $currency
 *
 * @return EverAccounting\Models\Currency|null
 * @since 1.1.0
 *
 */
function eaccounting_get_currency( $currency ) {
	if ( empty( $currency ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\Currency( $currency );
		var_dump($result);
		return $result->exists() ? $result : null;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return null;
	}
}

/**
 *  Create new currency programmatically.
 *
 *  Returns a new currency object on success.
 *
 * @param array $args {
 *                                  An array of elements that make up a currency to update or insert.
 *
 * @type int $id The currency ID. If equal to something other than 0,
 *                                         the currency with that id will be updated. Default 0.
 *
 * @type string $name The name of the currency . Default empty.
 *
 * @type string $code The code of currency. Default empty.
 *
 * @type double $rate The rate for the currency.Default is 1.
 *
 * @type double $precision The precision for the currency. Default 0.
 *
 * @type string $symbol The symbol for the currency. Default empty.
 *
 * @type string $position The position where the currency code will be set in amount. Default before.
 *
 * @type string $decimal_separator The decimal_separator for the currency code. Default ..
 *
 * @type string $thousand_separator The thousand_separator for the currency code. Default ,.
 *
 * @type int $enabled The status of the currency. Default 1.
 *
 * @type string $date_created The date when the currency is created. Default is current time.
 *
 *
 * }
 *
 * @return EverAccounting\Models\Currency|\WP_Error|bool
 * @since 1.1.0
 *
 */
function eaccounting_insert_currency( $args, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $args ) ) {
		return false;
	}
	try {
		// The  id will be provided when updating an item.
		$args = wp_parse_args( $args, array( 'id' => null ) );

		// Retrieve the currency.
		$item = new \EverAccounting\Models\Currency( $args['id'] );

		// Load new data.
		$item->set_props( $args );

		// Save the item
		$item->save();

		return $item;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return $wp_error ? new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}

/**
 * Delete a currency.
 *
 * @param $currency_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_currency( $currency_id ) {
	try {
		$currency = new EverAccounting\Models\Currency( $currency_id );

		return $currency->exists() ? $currency->delete() : false;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return false;
	}
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
	$search_cols  = array( 'id', 'name', 'code', 'rate', 'date_created' );
	$orderby_cols = array( 'id', 'name', 'code', 'rate', 'date_created' );
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'status'       => 'all',
			'type'         => '',
			'include'      => '',
			'search'       => '',
			'search_cols'  => $search_cols,
			'orderby_cols' => $orderby_cols,
			'fields'       => '*',
			'orderby'      => 'id',
			'order'        => 'ASC',
			'number'       => 20,
			'offset'       => 0,
			'paged'        => 1,
			'return'       => 'objects',
			'count_total'  => false,
		)
	);

	$qv    = apply_filters( 'eaccounting_get_categories_args', $args );
	$table = $wpdb->prefix . 'ea_currencies';

	$query_from    = "FROM {$table}";
	$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
	$query_where   = 'WHERE 1=1';
	$query_where   .= eaccounting_prepare_query_where( $qv, $table );
	$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
	$query_limit   = eaccounting_prepare_query_limit( $qv );
	$count_total   = true === $qv['count_total'];
	$cache_key     = md5( serialize( $qv ) );
	$results       = wp_cache_get( $cache_key, 'eaccounting_currency' );

	if ( false === $results ) {
		if ( $count_total ) {
			$request = "SELECT COUNT($table.`id`) $query_from  $query_where";
			$results = (int) $wpdb->get_var( $request );
			wp_cache_set( $cache_key, $results, 'eaccounting_currency' );
		} else {
			$request = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";
			$results = $wpdb->get_results( $request );
			if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'eaccounting_currency' );
					wp_cache_set( $item->code, $item->id, 'eaccounting_currency' );
				}
			}
			wp_cache_set( $cache_key, $results, 'eaccounting_currency' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_currency', $results );
	}

	return $results;
}

