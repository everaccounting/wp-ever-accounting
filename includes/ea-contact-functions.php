<?php
/**
 * EverAccounting Contact Functions.
 *
 * Contact related functions.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit();

/**
 * Get contact types.
 *
 * @since 1.1.0
 * @return array
 */
function eaccounting_get_contact_types() {
	return apply_filters(
		'eaccounting_contact_types',
		array(
			'customer' => __( 'Customer', 'wp-ever-accounting' ),
			'vendor'   => __( 'Vendor', 'wp-ever-accounting' ),
		)
	);
}


/**
 * Get the contact type label of a specific type.
 *
 * @since 1.1.0
 *
 * @param $type
 *
 * @return string
 */
function eaccounting_get_contact_type( $type ) {
	$types = eaccounting_get_contact_types();

	return array_key_exists( $type, $types ) ? $types[ $type ] : null;
}


/**
 * Get customer.
 *
 * @since 1.1.0
 *
 * @param $customer
 *
 * @return \EverAccounting\Models\Customer|null
 */
function eaccounting_get_customer( $customer ) {
	if ( empty( $customer ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Customer( $customer );

	return $result->exists() ? $result : null;
}


/**
 *  Create new customer programmatically.
 *
 *  Returns a new customer object on success.
 *
 * @since 1.1.0
 *
 * @param array $args          {
 *                             An array of elements that make up an customer to update or insert.
 *
 * @type int    $id            ID of the contact. If equal to something other than 0,
 *                               the post with that ID will be updated. Default 0.
 * @type int    $user_id       user_id of the contact. Default null.
 * @type string $name          name of the contact. Default not null.
 * @type string $email         email of the contact. Default null.
 * @type string $phone         phone of the contact. Default null.
 * @type string $fax           fax of the contact. Default null.
 * @type string $fax           fax of the contact. Default null.
 * @type string $birth_date    date of birth of the contact. Default null.
 * @type string $address       address of the contact. Default null.
 * @type string $country       country of the contact. Default null.
 * @type string $website       website of the contact. Default null.
 * @type string $tax_number    tax_number of the contact. Default null.
 * @type string $currency_code currency_code of the contact. Default null.
 * @type string $note          Additional note of the contact. Default null.
 * @type string $attachment    Attachment attached with contact. Default null.
 *
 * }
 *
 * @return EverAccounting\Models\Customer|\WP_Error|bool
 */
function eaccounting_insert_customer( $args, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $args ) ) {
		return false;
	}

	// The  id will be provided when updating an item.
	$args = wp_parse_args( $args, array( 'id' => null ) );

	// Retrieve the category.
	$item = new \EverAccounting\Models\Customer( $args['id'] );

	// Load new data.
	$item->set_props( $args );

	// Save the item
	$error = $item->save();

	// Do we have an error while saving?
	if ( is_wp_error( $error ) ) {
		return $wp_error ? $error : 0;
	}

	if ( ! $item->get_id() ) {
		return $wp_error ? new WP_Error( 'insert_error', __( 'An error occurred when saving customer.', 'wp-ever-accounting' ) ) : 0;
	}

	return $item;
}

/**
 * Delete a customer.
 *
 * @since 1.1.0
 *
 * @param $customer_id
 *
 * @return bool
 */
function eaccounting_delete_customer( $customer_id ) {
	$customer = new EverAccounting\Models\Customer( $customer_id );
	if ( ! $customer->exists() ) {
		return false;
	}

	return $customer->delete();
}

/**
 * Get customers items.
 *
 * @since 1.1.0
 *
 * @param bool  $callback
 *
 * @param array $args          {
 *
 * @type int    $id            ID of the contact.
 * @type int    $user_id       user_id of the contact.
 * @type string $name          name of the contact.
 * @type string $email         email of the contact.
 * @type string $phone         phone of the contact.
 * @type string $fax           fax of the contact.
 * @type string $fax           fax of the contact.
 * @type string $birth_date    date of birth of the contact.
 * @type string $address       address of the contact.
 * @type string $country       country of the contact.
 * @type string $website       website of the contact.
 * @type string $tax_number    tax_number of the contact.
 * @type string $currency_code currency_code of the contact.
 *
 * }
 *
 * @return array|int
 */
function eaccounting_get_customers( $args = array(), $callback = true ) {
	global $wpdb;
	$search_cols  = array( 'id', 'name', 'email', 'phone', 'fax', 'address', 'country' );
	$orderby_cols = array( 'id', 'name', 'email', 'phone', 'fax', 'address', 'country', 'enabled', 'date_created' );
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'status'       => 'all',
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

	$qv    = apply_filters( 'eaccounting_get_vendors_args', $args );
	$table = 'ea_contacts';

	$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
	$query_from    = eaccounting_prepare_query_from( $table );
	$query_where   = "WHERE 1=1 AND $table.`type`='customer' ";
	$query_where  .= eaccounting_prepare_query_where( $qv, $table );
	$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
	$query_limit   = eaccounting_prepare_query_limit( $qv );
	$count_total   = true === $qv['count_total'];
	$cache_key     = md5( serialize( $qv ) );
	$results       = wp_cache_get( $cache_key, 'eaccounting_customer' );
	$request       = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( $request );
			wp_cache_set( $cache_key, $results, 'eaccounting_contact' );
		} else {
			$results = $wpdb->get_results( $request );
			if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'eaccounting_contact' );
				}
			}
			wp_cache_set( $cache_key, $results, 'eaccounting_contact' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_customer', $results );
	}

	return $results;
}


/**
 * Get vendor.
 *
 * @since 1.1.0
 *
 * @param $vendor
 *
 * @return \EverAccounting\Models\Vendor|null
 */
function eaccounting_get_vendor( $vendor ) {
	if ( empty( $vendor ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Vendor( $vendor );

	return $result->exists() ? $result : null;
}


/**
 *  Create new vendor programmatically.
 *
 *  Returns a new vendor object on success.
 *
 * @since 1.1.0
 *
 * @param array $args          {
 *                             An array of elements that make up a vendor to update or insert.
 *
 * @type int    $id            ID of the contact. If equal to something other than 0,
 *                               the post with that ID will be updated. Default 0.
 * @type int    $user_id       user_id of the contact. Default null.
 * @type string $name          name of the contact. Default not null.
 * @type string $email         email of the contact. Default null.
 * @type string $phone         phone of the contact. Default null.
 * @type string $fax           fax of the contact. Default null.
 * @type string $fax           fax of the contact. Default null.
 * @type string $birth_date    date of birth of the contact. Default null.
 * @type string $address       address of the contact. Default null.
 * @type string $country       country of the contact. Default null.
 * @type string $website       website of the contact. Default null.
 * @type string $tax_number    tax_number of the contact. Default null.
 * @type string $currency_code currency_code of the contact. Default null.
 * @type string $note          Additional note of the contact. Default null.
 * @type string $attachment    Attachment attached with contact. Default null.
 *
 * }
 *
 * @return EverAccounting\Models\Vendor|\WP_Error|bool
 */
function eaccounting_insert_vendor( $args, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $args ) ) {
		return false;
	}

	// The  id will be provided when updating an item.
	$args = wp_parse_args( $args, array( 'id' => null ) );

	// Retrieve the category.
	$item = new \EverAccounting\Models\Vendor( $args['id'] );

	// Load new data.
	$item->set_props( $args );

	// Save the item
	$error = $item->save();

	// Do we have an error while saving?
	if ( is_wp_error( $error ) ) {
		return $wp_error ? $error : 0;
	}

	if ( ! $item->get_id() ) {
		return $wp_error ? new WP_Error( 'insert_error', __( 'An error occurred when saving customer.', 'wp-ever-accounting' ) ) : 0;
	}

	return $item;
}

/**
 * Delete a vendor.
 *
 * @since 1.1.0
 *
 * @param $vendor_id
 *
 * @return bool
 */
function eaccounting_delete_vendor( $vendor_id ) {
	$vendor = new EverAccounting\Models\Vendor( $vendor_id );
	if ( ! $vendor->exists() ) {
		return false;
	}

	return $vendor->save();
}

/**
 * Get vendors items.
 *
 * @since 1.1.0
 *
 * @param array $args          {
 *
 * @type int    $id            ID of the contact.
 * @type int    $user_id       user_id of the contact.
 * @type string $name          name of the contact.
 * @type string $email         email of the contact.
 * @type string $phone         phone of the contact.
 * @type string $fax           fax of the contact.
 * @type string $fax           fax of the contact.
 * @type string $birth_date    date of birth of the contact.
 * @type string $address       address of the contact.
 * @type string $country       country of the contact.
 * @type string $website       website of the contact.
 * @type string $tax_number    tax_number of the contact.
 * @type string $currency_code currency_code of the contact.
 *
 * }
 *
 *
 * @return array|int
 */
function eaccounting_get_vendors( $args = array() ) {
	global $wpdb;
	$search_cols  = array( 'id', 'name', 'email', 'phone', 'fax', 'address', 'country' );
	$orderby_cols = array( 'id', 'name', 'email', 'phone', 'fax', 'address', 'country', 'enabled', 'date_created' );
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'status'       => 'all',
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

	$qv    = apply_filters( 'eaccounting_get_vendors_args', $args );
	$table = 'ea_contacts';

	$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
	$query_from    = eaccounting_prepare_query_from( $table );
	$query_where   = "WHERE 1=1 AND $table.`type`='vendor' ";
	$query_where  .= eaccounting_prepare_query_where( $qv, $table );
	$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
	$query_limit   = eaccounting_prepare_query_limit( $qv );
	$count_total   = true === $qv['count_total'];
	$cache_key     = md5( serialize( $qv ) );
	$results       = wp_cache_get( $cache_key, 'eaccounting_vendor' );
	$request       = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( $request );
			wp_cache_set( $cache_key, $results, 'eaccounting_contact' );
		} else {
			$results = $wpdb->get_results( $request );
			if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'eaccounting_contact' );
				}
			}
			wp_cache_set( $cache_key, $results, 'eaccounting_contact' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_vendor', $results );
	}

	return $results;
}
