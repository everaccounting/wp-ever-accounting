<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Autoloader.
 *
 * @since   1.1.6
 * @package EverAccounting
 */
class Autoloader {

	/**
	 * Autoloader constructor.
	 *
	 * @since 1.1.6
	 */
	private function __construct() {
	}

	/**
	 * Require the autoloader and return the result.
	 *
	 * If the autoloader is not present, let's log the failure and display a nice admin notice.
	 *
	 * @return void
	 */
	public static function init() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Autoload function.
	 *
	 * @param string $class_name Class name.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function autoload( $class_name ) {
		// Bail out if the class name doesn't start with our prefix.
		if ( strpos( $class_name, 'EverAccounting\\' ) !== 0 ) {
			return;
		}

		// Remove the prefix from the class name.
		$class_name = substr( $class_name, strlen( 'EverAccounting\\' ) );

		// Replace the namespace separator with the directory separator.
		$class_name = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name );

		// Add the .php extension.
		$class_name = $class_name . '.php';

		$file_paths = array(
			dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class_name,
			dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $class_name,
		);

		foreach ( $file_paths as $file_path ) {
			if ( file_exists( $file_path ) ) {
				require_once $file_path;

				return;
			}
		}
	}

}
