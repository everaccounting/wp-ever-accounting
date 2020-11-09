<?php
/**
 * Cache Controller.
 *
 * Handles data object caching.
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Cache
 *
 * @since   1.0.
 *
 * @package EverAccounting
 */
class Cache {
	/**
	 * Cache version. Used to invalidate all cached values.
	 */
	const OPTION = 'eaccounting_cache';

	/**
	 * @since 1.1.0
	 *
	 * @param $group
	 * @param $key
	 *
	 * @return string
	 */
	public static function get_name( $group, $key ) {
		return self::OPTION . '_' . $group . '_' . $key;
	}

	/**
	 * Get cached value.
	 *
	 * @param string $key Cache key.
	 *
	 * @return mixed
	 */
	public static function get( $key, $group = '' ) {
		$transient_name = self::get_name( $group, $key );

		return get_transient( $transient_name );
	}

	/**
	 * Update cached value.
	 *
	 * @param string $key   Cache key.
	 * @param mixed  $value New value.
	 *
	 * @return bool
	 */
	public static function set( $key, $value, $group = '' ) {
		$transient_name = self::get_name( $group, $key );
		$result         = set_transient( $transient_name, $value, HOUR_IN_SECONDS );

		return $result;
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $group
	 */
	public static function delete( $group ) {
		global $wpdb;
		if ( ! empty( $group ) ) {
			$transient_name = '_transient_' . self::get_name( $group, '' );
			$wpdb->query( "delete * from wp_options where option_name like  '{$transient_name}_%'" );
		}
	}
}
