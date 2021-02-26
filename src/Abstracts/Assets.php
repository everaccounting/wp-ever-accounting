<?php
/**
 * Abstract Model.
 *
 * Handles loading assets.
 *
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

class Assets{

	protected static function register_style( $handle, $src, $dependencies = array(), $has_rtl = true  ){
		$version = eaccounting()->get_version();
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$version = time();
		}

		wp_register_style( $handle, $src, $dependencies, $version );

		if ( $has_rtl && function_exists( 'wp_style_add_data' ) ) {
			wp_style_add_data( $handle,'rtl', 'replace' );
		}
	}

	/**
	 * Registers a script according to `wp_register_script`, additionally loading the translations for the file.
	 *
	 * @param string $handle Name of the script. Should be unique.
	 * @param string $file_path file path from dist directory
	 * @param array $dependencies Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param bool $has_i18n Optional. Whether to add a script translation call to this file. Default 'true'.
	 *
	 * @since 1.0.0
	 *
	 */
	protected static function register_script( $handle, $file_path = null, $dependencies = array(), $has_i18n = true ) {
		$filename = is_null( $file_path )? $handle : $file_path;
		$filename = str_replace( ['.min', '.js'], '', $filename);
		$file_url = eaccounting()->plugin_url("/dist/{$filename}.js");
		$dependency_file = $filename . '.asset.php';
		$dependency_file_path = eaccounting()->plugin_path("/dist/{$dependency_file}");
		$version = eaccounting()->get_version();
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$version = time();
		}
		if ( file_exists( $dependency_file_path ) ) {
			$asset        = require $dependency_file_path;
			$dependencies = isset( $asset['dependencies'] ) ? array_merge( $asset['dependencies'], $dependencies ) : $dependencies;
			$version      = ! empty( $asset['version'] ) ? $asset['version'] : $version;
		}
		wp_register_script( $handle, $file_url, $dependencies, $version, true );

		if ( $has_i18n && function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $handle, 'wp-ever-accounting', dirname( __DIR__ ) . '/languages' );
		}
	}

	/**
	 * Returns the appropriate asset url
	 *
	 * @param string $filename Filename for asset url (without extension).
	 * @param string $type     File type (.css or .js).
	 *
	 * @return  string The generated path.
	 */
	protected static function get_asset_dist_url( $filename, $type = 'js' ) {
		return eaccounting()->plugin_url( "dist/$filename.$type" );
	}
}
