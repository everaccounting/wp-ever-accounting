<?php
/**
 * EverAccounting File Functions.
 *
 * @since  1.0.2
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit();

/**
 * Set upload directory for Accounting
 *
 * @since 1.0.2
 * @return array
 */
function eac_get_upload_dir() {
	$upload            = wp_upload_dir();
	$upload['basedir'] = $upload['basedir'] . '/eac-files';
	$upload['path']    = $upload['basedir'] . $upload['subdir'];
	$upload['baseurl'] = $upload['baseurl'] . '/eac-files';
	$upload['url']     = $upload['baseurl'] . $upload['subdir'];

	return $upload;
}

/**
 * Scan folders
 *
 * @param string $path Path to scan.
 * @param array  $return Array of files.
 *
 * @since 1.0.2
 *
 * @return array
 */
function eac_scan_folders( $path = '', $return = array() ) {
	$path  = '' === $path ? __DIR__ : $path;
	$lists = scandir( $path );

	if ( ! empty( $lists ) ) {
		foreach ( $lists as $f ) {
			if ( is_dir( $path . DIRECTORY_SEPARATOR . $f ) && '.' !== $f && '..' !== $f ) {
				if ( ! in_array( $path . DIRECTORY_SEPARATOR . $f, $return, true ) ) {
					$return[] = trailingslashit( $path . DIRECTORY_SEPARATOR . $f );
				}

				eac_scan_folders( $path . DIRECTORY_SEPARATOR . $f, $return );
			}
		}
	}

	return $return;
}

/**
 * Protect accounting files
 *
 * @param bool $force Force protect.
 * @since 1.0.2
 */
function eac_protect_files( $force = false ) {
	if ( false === get_transient( 'eac_check_files_protection' ) || $force ) {
		$upload_dir = eac_get_upload_dir();
		if ( ! is_dir( $upload_dir['path'] ) ) {
			wp_mkdir_p( $upload_dir['path'] );
		}

		$base_dir = $upload_dir['basedir'];
		$htaccess = trailingslashit( $base_dir ) . '.htaccess';
		// init file system.
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}
		if ( empty( $wp_filesystem ) ) {
			return;
		}

		if ( ! $wp_filesystem->exists( $htaccess ) ) {
			$rule  = "Options -Indexes\n";
			$rule .= "deny from all\n";
			$rule .= "<FilesMatch '\.(jpg|jpeg|png|pdf|doc|docx|xls)$'>\n";
			$rule .= "Order Allow,Deny\n";
			$rule .= "Allow from all\n";
			$rule .= "</FilesMatch>\n";
			$wp_filesystem->put_contents( $htaccess, $rule, FS_CHMOD_FILE );
		}

		// Top level blank index.php.
		if ( ! file_exists( $base_dir . '/index.php' ) && wp_is_writable( $base_dir ) ) {
			$wp_filesystem->put_contents( $base_dir . '/index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
		}

		$folders = eac_scan_folders( $base_dir );
		foreach ( $folders as $folder ) {
			// Create index.php, if it doesn't exist.
			if ( ! file_exists( $folder . 'index.php' ) && wp_is_writable( $folder ) ) {
				$wp_filesystem->put_contents( $folder . 'index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
			}
		}

		// Check for the files once per day.
		set_transient( 'eac_check_files_protection', true, 3600 * 24 );
	}
}
