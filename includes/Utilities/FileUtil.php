<?php

namespace EverAccounting\Utilities;

defined( 'ABSPATH' ) || exit;

/**
 * Class FileUtil
 *
 * @since 1.0.0
 * @package EverAccounting\Utilities
 */
class FileUtil {

	/**
	 * Get the instance of a file system.
	 *
	 * @since 1.0.0
	 * @return \WP_Filesystem_Base File system instance.
	 */
	public static function get_filesystem() {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}
}
