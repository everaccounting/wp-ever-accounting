<?php
/**
 * EverAccounting api key Functions.
 *
 * All api_key related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit();

/**
 * Get all the permissions of api_key the plugin support.
 *
 * @return array
 * @since 1.1.0
 */
function eaccounting_get_api_key_permissions() {
	$types = array(
		'read'       => __( 'Read', 'wp-ever-accounting' ),
		'write'      => __( 'Write', 'wp-ever-accounting' ),
		'read_write' => __( 'Read/Write', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_get_api_key_permissions', $types );
}

/**
 * Get the api_key permission label of a specific permission.
 *
 * @param $permission
 *
 * @return string
 * @since 1.1.0
 *
 */
function eaccounting_get_api_key_permission( $permission ) {
	$permissions = eaccounting_get_api_key_permissions();

	return array_key_exists( $permission, $permissions ) ? $permissions[ $permission ] : null;
}

/**
 * Get api_key.
 *
 * @param $api_key
 *
 * @return null|EverAccounting\Models\ApiKey
 * @since 1.1.0
 *
 */
function eaccounting_get_api_key( $api_key ) {
	if ( empty( $api_key ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\ApiKey( $api_key );

		return $result->exists() ? $result : null;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return null;
	}
}

/**
 * Insert a api_key.
 *
 * @param bool $wp_error Whether to return false or WP_Error on failure.
 *
 * @param array $data {
 *                            An array of elements that make up an api_key to update or insert.
 *
 * @type int $id The api_key ID. If equal to something other than 0, the api_key with that ID will be updated. Default 0.
 *
 * @type int $user_id User_id for the api_key.
 *
 * @type string $description api_key description.
 *
 * @type string $permission api_key permission.
 *
 * }
 *
 * @return int|\WP_Error|\EverAccounting\Models\ApiKey|bool The value 0 or WP_Error on failure. The Api Key object on success.
 * @since 1.1.0
 *
 */
function eaccounting_insert_api_key( $data = array(), $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $data ) ) {
		return false;
	}
	try {
		// The  id will be provided when updating an item.
		$data = wp_parse_args( $data, array( 'id' => null ) );

		// Retrieve the category.
		$item = new \EverAccounting\Models\ApiKey( $data['id'] );

		// Load new data.
		$item->set_props( $data );

		$item->save();

		return $item;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return $wp_error ? new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}

/**
 * Delete a api_key.
 *
 * @param $api_key_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_api_key( $api_key_id ) {
	try {
		$api_key = new EverAccounting\Models\ApiKey( $api_key_id );

		return $api_key->exists() ? $api_key->delete() : false;
	} catch ( \EverAccounting\Core\Exception $e ) {
		return false;
	}
}

	/**
	 * Get api_key collection.
	 *
	 * @param array $args
	 *
	 * @return int|array|null
	 * @since 1.1.0
	 *
	 */
	function eaccounting_get_api_keys( $args = array() ) {
		global $wpdb;
		// Prepare args.
		$args = wp_parse_args(
			$args,
			array(
				'status'       => 'all',
				'type'         => '',
				'include'      => '',
				'search'       => '',
				'search_cols'  => array( 'user_id', 'permission' ),
				'orderby_cols' => array( 'user_id', 'permission' ),
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

		$qv    = apply_filters( 'eaccounting_get_api_keys_args', $args );
		$table = 'ea_api_keys';

		$query_fields  = eaccounting_prepare_query_fields( $qv, $table );
		$query_from    = eaccounting_prepare_query_from( $table );
		$query_where   = 'WHERE 1=1';
		$query_where   .= eaccounting_prepare_query_where( $qv, $table );
		$query_orderby = eaccounting_prepare_query_orderby( $qv, $table );
		$query_limit   = eaccounting_prepare_query_limit( $qv );
		$count_total   = true === $qv['count_total'];
		$cache_key     = md5( serialize( $qv ) );
		$results       = wp_cache_get( $cache_key, 'ea_api_keys' );
		$request       = "SELECT $query_fields $query_from $query_where $query_orderby $query_limit";

		if ( false === $results ) {
			if ( $count_total ) {
				$results = (int) $wpdb->get_var( $request );
				wp_cache_set( $cache_key, $results, 'ea_api_keys' );
			} else {
				$results = $wpdb->get_results( $request );
				if ( in_array( $qv['fields'], array( 'all', '*' ), true ) ) {
					foreach ( $results as $key => $item ) {
						wp_cache_set( $item->id, $item, 'ea_api_keys' );
						wp_cache_set( "key-{$item->user_id}", $item->id, 'ea_api_keys' );
						wp_cache_set( "key-{$item->permission}", $item->id, 'ea_api_keys' );
					}
				}
				wp_cache_set( $cache_key, $results, 'ea_api_keys' );
			}
		}

		if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
			$results = array_map( 'eaccounting_get_api_key', $results );
		}

		return $results;

}
/**
 * Get all the admin users.
 *
 * @since 1.1.0
 * @return array
 */
function eaccoutning_get_admin_users() {
	$admins = get_users( [ 'role__in' => [ 'administrator' ] ] );
	$users = array();
	if(is_array($admins) && count($admins)){
		foreach($admins as $single){
			$users = array(
				$single->ID => $single->user_nicename.'('. "#".$single->ID.'&ndash;'.$single->user_email.')',
			);
		}
	}
	return apply_filters( 'eaccounting_api_keys_users', $users );
}

/**
 * Generate a random hash.
 *
 * @since  1.1.0
 * @return string
 */
function ea_rand_hash() {
	if ( ! function_exists( 'openssl_random_pseudo_bytes' ) ) {
		return sha1( wp_rand() );
	}

	return bin2hex( openssl_random_pseudo_bytes( 20 ) ); // @codingStandardsIgnoreLine
}

/**
 * EA API - Hash.
 *
 * @since  1.1.0
 * @param  string $data Message to be hashed.
 * @return string
 */
function ea_api_hash( $data ) {
	return hash_hmac( 'sha256', $data, 'ea-api' );
}
