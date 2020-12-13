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
 * Get all the admin users.
 *
 * @return array
 * @since 1.1.0
 */
function eaccounting_get_admin_users() {
	$admins = get_users( [ 'role__in' => [ 'administrator' ] ] );
	$users  = array();
	if ( is_array( $admins ) && count( $admins ) ) {
		foreach ( $admins as $single ) {
			$users = array(
				$single->ID => $single->user_nicename . '(' . "#" . $single->ID . '&ndash;' . $single->user_email . ')',
			);
		}
	}

	return apply_filters( 'eaccounting_api_keys_users', $users );
}

/**
 * Generate a random hash.
 *
 * @return string
 * @since  1.1.0
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
 * @param string $data Message to be hashed.
 *
 * @return string
 * @since  1.1.0
 */
function ea_api_hash( $data ) {
	return hash_hmac( 'sha256', $data, 'ea-api' );
}




