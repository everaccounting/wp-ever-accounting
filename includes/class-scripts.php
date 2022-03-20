<?php
/**
 * Handle frontend scripts
 *
 * @since 1.1.5
 * @package Ever_Accounting
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit;

/**
 * Frontend_Scripts class.
 */
class Scripts {

	/**
	 * Hook in methods.
	 */
	public static function init() {
//		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_public_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_admin_scripts' ) );
	}

	/**
	 * Register public scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function register_public_scripts() {
		$scripts = array(
			'eaccounting' => array(
				'src'  => 'js/eaccounting.js',
				'deps' => array( 'jquery' ),
			),
		);

		$styles = array(
			'eaccounting' => array(
				'src'  => 'css/eaccounting.css',
				'deps' => array(),
			),
		);

		foreach ( $scripts as $name => $props ) {
			self::register_script( $name, $props['src'], $props['deps'] );
		}

		foreach ( $styles as $name => $props ) {
			self::register_style( $name, $props['src'], $props['deps'] );
		}
	}

	/**
	 * Register admin scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function register_admin_scripts() {
		$scripts = array(
//			'ea-components' => array(
//				'src' => 'js/components.js',
//			),
//			'ea-navigation' => array(
//				'src' => 'js/navigation.js',
//			),
//			'ea-data'       => array(
//				'src' => 'js/data.js',
//			),
//			'ea-api'       => array(
//				'src' => 'js/api.js',
//			),
			'ea-app'        => array(
				'src'  => 'js/app.js',
				'deps' => array(),
			),
		);

		$styles = array(
			'ea-components' => array(
				'src' => 'css/components.css',
				'deps' => array( 'wp-components' ),
			),
		);

//		foreach ( $scripts as $name => $props ) {
//			$props = wp_parse_args( $props, array(
//				'deps'      => array(),
//				'has_i18n'  => true,
//				'in_footer' => true,
//			) );
//			self::register_script( $name, $props['src'], $props['deps'], $props['has_i18n'], $props['in_footer'] );
//		}
//
//		foreach ( $styles as $name => $props ) {
//			$props = wp_parse_args( $props, array(
//				'deps'    => array(),
//				'has_rtl' => true,
//				'media'   => 'all',
//			) );
//			self::register_style( $name, $props['src'], $props['deps'], $props['has_rtl'], $props['media'] );
//		}

		wp_enqueue_script( 'ea-vue', plugins_url( 'assets/dist/js/vue.js', __DIR__ ), array(), '1.0.0', true );
		wp_enqueue_script( 'ea-components', plugins_url( 'assets/dist/js/components.js', __DIR__ ), array(), '1.0.0', true );
		wp_enqueue_script( 'ea-app', plugins_url( 'assets/dist/js/app.js', __DIR__ ), array( 'ea-vue', 'ea-components' ), '1.0.0', true );
//		wp_localize_script( 'ea-app', 'eaccounting_i18n', self::get_localized_data() );
//		wp_enqueue_style( 'ea-components' );
	}

	/**
	 * Registers a script according to `wp_register_script`, additionally loading the translations for the file.
	 *
	 * @param string $handle Name of the script. Should be unique.
	 * @param string $src Relative file path from dist directory.
	 * @param array $deps Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param bool $has_i18n Optional. Whether to add a script translation call to this file. Default 'true'.
	 * @param bool $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
	 *
	 * @since 1.1.3
	 */
	public static function register_script( $handle, $src = null, $deps = array(), $has_i18n = false, $in_footer = true ) {
		$file      = basename( $src );
		$filename  = pathinfo( $file, PATHINFO_FILENAME );
		$version   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : EACCOUNTING_VERSION;
		$file_path = EACCOUNTING_PATH . '/assets/' . str_replace( $file, "$filename.asset.php", '/dist/' . $src );
		$file_url  = EACCOUNTING_URL . '/assets/dist/' . ltrim( $src, '/' );

		if ( file_exists( $file_path ) ) {
			$asset   = require $file_path;
			$deps    = isset( $asset['dependencies'] ) ? array_merge( $asset['dependencies'], $deps ) : $deps;
			$version = ! empty( $asset['version'] ) ? $asset['version'] : $version;
		}

		wp_register_script( $handle, $file_url, $deps, $version, $in_footer );

		if ( $has_i18n && function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $handle, 'wp-ever-accounting', plugin_basename( untrailingslashit( EACCOUNTING_PATH ) ) . '/i18n/languages/' );
		}

		return $handle;
	}

	/**
	 * Register style.
	 *
	 * @param string $handle style handler.
	 * @param string $src Relative file path from dist directory.
	 * @param array $deps style dependencies.
	 * @param bool $has_rtl support RTL.
	 * @param string $media media.
	 *
	 * @since 1.1.3
	 */
	public static function register_style( $handle, $src, $deps = array(), $has_rtl = true, $media = 'all' ) {
		$file      = basename( $src );
		$filename  = pathinfo( $file, PATHINFO_FILENAME );
		$version   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : EACCOUNTING_VERSION;
		$file_path = EACCOUNTING_PATH . '/assets/' . str_replace( $file, "$filename.asset.php", '/dist/' . $src );
		$file_url  = EACCOUNTING_URL . '/assets/dist/' . ltrim( $src, '/' );

		if ( file_exists( $file_path ) ) {
			$asset   = require $file_path;
			$version = ! empty( $asset['version'] ) ? $asset['version'] : $version;
		}

		wp_register_style( $handle, $file_url, $deps, $version, $media );

		if ( $has_rtl && function_exists( 'wp_style_add_data' ) ) {
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}

		return $handle;
	}


	/**
	 * Get localized data.
	 *
	 * @since 1.1.3
	 * @return array
	 */
	public static function get_localized_data() {
		$data = array(
			'ajax_url'       => admin_url( 'admin-ajax.php' ),
			'i18n'           => array(),
			'currency_codes' => Currencies::get_codes(),
//			'payment_methods' => eaccounting_get_payment_methods(),
//			'countries'      => eaccounting_get_countries(),
		);

		return apply_filters( 'eaccounting_localized_data', $data );
	}
}
