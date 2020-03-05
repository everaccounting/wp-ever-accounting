<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Scripts{

	/**
	 * Gets the path for the asset depending on file type.
	 *
	 * @return string Folder path of asset.
	 */
	private static function get_path() {
		return EACCOUNTING_ABSPATH .'/dist';
	}

	/**
	 * Gets the URL to an asset file.
	 *
	 * @param  string $file name.
	 * @return string URL to asset.
	 */
	public static function get_url( $file ) {
		return plugins_url( self::get_path( $file ) . $file, EACCOUNTING_PLUGIN_FILE );
	}

	/**
	 * Gets the file modified time as a cache buster if we're in dev mode, or the plugin version otherwise.
	 *
	 * @since 1.0.2
	 * @param $file Local path to the file.
	 *
	 * @return string
	 */
	public static function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$file = trim( $file, '/' );
			return filemtime( EACCOUNTING_PLUGIN_FILE . self::get_path( $file ) );
		}
		return EACCOUNTING_VERSION;
	}




}
