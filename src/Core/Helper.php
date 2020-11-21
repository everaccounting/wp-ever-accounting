<?php

/**
 * Helper methods
 *
 * @package        EverAccounting
 * @version        1.0.2
 */

namespace EverAccounting\Core;

defined( 'ABSPATH' ) || exit();

/**
 * Class Helper
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Core
 */
class Helper {

	/**
	 * Fetches data stored on disk.
	 *
	 * @since 1.1.0
	 *
	 * @param string $key Type of data to fetch.
	 *
	 * @return mixed Fetched data.
	 */
	public static function get_data( $key ) {
		return eaccounting_get_data( $key );
	}
}
