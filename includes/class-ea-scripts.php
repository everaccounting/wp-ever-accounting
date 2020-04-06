<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Scripts {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @return self Main instance.
	 * @since  1.0.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * EAccounting_Scripts constructor.
	 */
	private function __construct() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Register scripts & styles.
	 *
	 * @since 1.0.0
	 * Moved data related enqueuing to new AssetDataRegistry class
	 * as part of ongoing refactoring.
	 */
	public static function register_assets() {
		//styles
		self::register_style( 'ea-components', plugins_url( self::get_block_asset_dist_path( 'components', 'css' ), __DIR__ ), array( 'wp-components' ) );
		self::register_style( 'ea-fontawesome', plugins_url( '/assets/vendor/font-awesome/css/font-awesome.min.css', __DIR__ ), array() );
		self::register_style( 'ea-client', plugins_url( self::get_block_asset_dist_path( 'client', 'css' ), __DIR__ ), array(
			'ea-components',
			'ea-fontawesome'
		) );

		//scripts
		self::register_script( 'ea-data', plugins_url( self::get_block_asset_dist_path( 'data' ), __DIR__ ) );
		self::register_script( 'ea-components', plugins_url( self::get_block_asset_dist_path( 'components' ), __DIR__ ) );
		self::register_script( 'ea-hoc', plugins_url( self::get_block_asset_dist_path( 'hoc' ), __DIR__ ) );
		self::register_script( 'ea-store', plugins_url( self::get_block_asset_dist_path( 'store' ), __DIR__ ) );
		self::register_script( 'ea-helpers', plugins_url( self::get_block_asset_dist_path( 'helpers' ), __DIR__ ) );

		$client_dependencies = array(
			'ea-data',
			'ea-components',
			'ea-hoc',
			'ea-store',
			'ea-helpers',
		);
		self::register_script( 'ea-client', plugins_url( self::get_block_asset_dist_path( 'client' ), __DIR__ ), $client_dependencies );

	}


	/**
	 * since 1.0.0
	 *
	 * @param $hook
	 */
	public static function enqueue_scripts( $hook ) {
		if ( ! preg_match( '/accounting/', $hook ) ) {
			return;
		}
		wp_localize_script( 'ea-data', 'eajsdata', self::get_data() );
		wp_enqueue_script( 'ea-client' );
		wp_enqueue_style( 'ea-client' );
	}

	/**
	 * Registers a style according to `wp_register_style`.
	 *
	 * @param string $handle Name of the stylesheet. Should be unique.
	 * @param string $src Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 * @param array $deps Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
	 * @param string $media Optional. The media for which this stylesheet has been defined. Default 'all'. Accepts media types like
	 *                       'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 *
	 * @since 1.0.0
	 *
	 */
	protected static function register_style( $handle, $src, $deps = [], $media = 'all' ) {
		$filename = str_replace( plugins_url( '/', __DIR__ ), '', $src );
		$ver      = self::get_file_version( $filename );
		wp_register_style( $handle, $src, $deps, $ver, $media );
	}

	/**
	 * Registers a script according to `wp_register_script`, additionally loading the translations for the file.
	 *
	 * @param string $handle Name of the script. Should be unique.
	 * @param string $src Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param array $dependencies Optional. An array of registered script handles this script depends on. Default empty array.
	 * @param bool $has_i18n Optional. Whether to add a script translation call to this file. Default 'true'.
	 *
	 * @since 1.0.0
	 *
	 */
	protected static function register_script( $handle, $src, $dependencies = [], $has_i18n = true ) {
		$relative_src = str_replace( plugins_url( '/', __DIR__ ), '', $src );
		$asset_path   = dirname( __DIR__ ) . '/' . str_replace( '.js', '.asset.php', $relative_src );

		if ( file_exists( $asset_path ) ) {
			$asset        = require $asset_path;
			$dependencies = isset( $asset['dependencies'] ) ? array_merge( $asset['dependencies'], $dependencies ) : $dependencies;
			$version      = ! empty( $asset['version'] ) ? $asset['version'] : self::get_file_version( $relative_src );
		} else {
			$version = self::get_file_version( $relative_src );
		}

		wp_register_script( $handle, $src, $dependencies, $version, true );

		if ( $has_i18n && function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $handle, 'wp-ever-accounting', dirname( __DIR__ ) . '/languages' );
		}
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @param string $file Local path to the file.
	 *
	 * @return string The cache buster value to use for the given file.
	 */
	protected static function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			return time();
		}

		return eaccounting()->version;
	}


	/**
	 * Returns the appropriate asset path for loading either legacy builds or
	 * current builds.
	 *
	 * @param string $filename Filename for asset path (without extension).
	 * @param string $type File type (.css or .js).
	 *
	 * @return  string             The generated path.
	 */
	protected static function get_block_asset_dist_path( $filename, $type = 'js' ) {
		return "assets/dist/$filename.$type";
	}

	/**
	 * Returns all the data registered for localization
	 *
	 * since 1.0.0
	 */
	protected static function get_data() {
//		$date_format = eaccounting_get_settings( 'ea_date_format' );
//		$time_format = eaccounting_get_settings( 'ea_time_format' );
		$locale_data = [
			'data' => [
				'site_title'        => get_bloginfo( 'name ' ),
				'wp_version'        => get_bloginfo( 'version' ),
				'ea_version'        => eaccounting()->version,
				'api_nonce'         => wp_create_nonce( 'wp_rest' ),
				'per_page'          => 50,
				'paths'             => [
					'site_url'        => site_url(),
					'admin_url'       => admin_url(),
					'asset_url'       => EACCOUNTING_ASSETS_URL,
					'base_rest_route' => rest_url(),
					'rest_route'      => rest_url( '/ea/v1/' ),
					'namespace'       => '/ea/v1',
				],
				'locale'            => [
					'user' => get_user_locale(),
					'site' => get_locale()
				],
				'date_formats'      => eaccounting_convert_php_to_moment_formats(),
				'global_currencies' => eaccounting_get_global_currencies(),
				'countries'         => self::to_js_options( eaccounting_get_countries() ),
				'contact_types'     => self::to_js_options( eaccounting_get_contact_types() ),
				'category_types'    => self::to_js_options( eaccounting_get_category_types() ),
				'tax_rate_types'    => self::to_js_options( eaccounting_get_tax_types() ),
				'payment_methods'   => self::to_js_options( eaccounting_get_payment_methods() ),
			]
		];

		return apply_filters( 'eaccounting_localized_data', $locale_data );
	}


	/**
	 * since 1.0.0
	 *
	 * @param $keyvalues
	 *
	 * @return array
	 */
	public static function to_js_options( $keyvalues ) {
		$options = [];
		foreach ( $keyvalues as $key => $value ) {
			$options[] = [
				'label' => $value,
				'value' => $key,
			];
		}

		return $options;
	}
}

EAccounting_Scripts::instance();
