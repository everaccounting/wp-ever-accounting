<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class AbstractPlugin.
 *
 * @since   1.1.6
 * @package EverAccounting
 */
class BasePlugin {
	/**
	 * The plugin data store.
	 *
	 * @since 1.1.6
	 * @var array
	 */
	protected $data = array();

	/**
	 * The single instance of the class.
	 *
	 * @since 1.1.6
	 * @var self
	 */
	public static $instance;

	/**
	 * Gets the single instance of the class.
	 * This method is used to create a new instance of the class.
	 *
	 * @param string|array $args The plugin data.
	 * @param string       $version The plugin version.
	 *
	 * @since 1.1.6
	 * @return static
	 */
	final public static function create( $args = null, $version = null ) {
		if ( is_null( static::$instance ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			$called_class = get_called_class();
			if ( ! is_array( $args ) ) {
				$file         = $args;
				$args         = array();
				$args['file'] = $file;
			}
			$file        = $args['file'];
			$plugin_data = get_plugin_data( $file, false, false );
			$plugin_data = array_change_key_case( $plugin_data, CASE_LOWER );
			$plugin_data = array_merge( $plugin_data, $args );

			// If version is set, use it.
			if ( ! empty( $version ) ) {
				$plugin_data['version'] = $version;
			}

			static::$instance = new $called_class( $plugin_data );
		}

		return static::$instance;
	}

	/**
	 * Gets the instance of the class.
	 *
	 * @since 1.1.6
	 *
	 * @return static
	 */
	final public static function get_instance() {
		if ( null === static::$instance ) {
			_doing_it_wrong( __FUNCTION__, 'Plugin instance called before initiating the instance.', '1.0.0' );
		}

		return static::$instance;
	}

	/**
	 * Plugin constructor.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.1.6
	 */
	protected function __construct( $data ) {
		// Only set the data keys that are not already set.
		$this->data = array_merge( $this->data, $data );
		// If the slug is not set, then set it.
		if ( ! isset( $this->data['slug'] ) ) {
			$this->data['slug'] = basename( $this->data['file'], '.php' );
		}
		// If the version is not set, then set it.
		if ( ! isset( $this->data['version'] ) ) {
			$this->data['version'] = '1.0.0';
		}
		// If the prefix is not set, then set it.
		if ( ! isset( $this->data['prefix'] ) ) {
			$prefix = str_replace( '-', '_', $this->data['slug'] );
			// Replace wp_ from the beginning of the prefix.
			$this->data['prefix'] = preg_replace( '/^wp_/', '', $prefix );
		}
	}

	/**
	 * Prevents cloning.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	final public function __clone() {
		_doing_it_wrong( __FUNCTION__, 'Cloning is forbidden.', '1.0.0' );
	}

	/**
	 * Prevents unserializing.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	final public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, 'Unserializing is forbidden.', '1.0.0' );
	}

	/*
	|--------------------------------------------------------------------------
	| PLUGIN DATA
	|--------------------------------------------------------------------------
	|
	| Methods to get plugin data.
	|
	*/
	/**
	 * Gets the plugin data.
	 *
	 * @param string $key The data key.
	 *
	 * @since 1.1.6
	 *
	 * @return mixed
	 */
	public function get_data( $key ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
	}

	/**
	 * Gets the plugin file.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_file() {
		return $this->get_data( 'file' );
	}

	/**
	 * Gets the plugin version.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->get_data( 'version' );
	}

	/**
	 * Gets the plugin slug.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->get_data( 'slug' );
	}

	/**
	 * Get the plugin prefix.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_prefix() {
		return $this->get_data( 'prefix' );
	}

	/**
	 * Get the 'basename' for the plugin (e.g. my-plugin/my-plugin.php).
	 *
	 * @since  1.0.0
	 * @return string The plugin basename.
	 */
	public function get_basename() {
		return plugin_basename( $this->get_file() );
	}

	/**
	 * Gets the plugin name.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->get_data( 'name' );
	}

	/**
	 * Gets the plugin text domain.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_text_domain() {
		return $this->get_data( 'textdomain' );
	}

	/**
	 * Gets the plugin domain path.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_domain_path() {
		return $this->get_data( 'domainpath' );
	}

	/**
	 * Get the documentation URI for this plugin.
	 *
	 * @since  1.0.0
	 * @return string (URI)
	 */
	public function get_docs_url() {
		return $this->get_data( 'docs_url' );
	}

	/**
	 * Get the support URI for this plugin.
	 *
	 * @since  1.0.0
	 * @return string (URI)
	 */
	public function get_support_url() {
		return $this->get_data( 'support_url' );
	}

	/**
	 * Get the review URI for this plugin.
	 *
	 * @since  1.0.0
	 * @return string (URI)
	 */
	public function get_review_url() {
		return $this->get_data( 'review_url' );
	}

	/**
	 * Gets the plugin directory.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_dir_path() {
		$args   = func_get_args();
		$append = isset( $args[0] ) ? $args[0] : '';

		return trailingslashit( plugin_dir_path( $this->get_file() ) ) . ltrim( $append, '/' );
	}

	/**
	 * Gets the plugin url.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_dir_url() {
		// Get the first argument if passed and append it to the url.
		$args   = func_get_args();
		$append = isset( $args[0] ) ? $args[0] : '';

		return trailingslashit( plugin_dir_url( $this->get_file() ) ) . ltrim( $append, '/' );
	}

	/**
	 * Get assets path.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_assets_path() {
		return $this->get_dir_path() . 'assets/';
	}

	/**
	 * Get assets url.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_assets_url() {
		return $this->get_dir_url() . 'assets/';
	}

	/**
	 * Get template path.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_template_path() {
		return $this->get_dir_path() . 'templates/';
	}

	/**
	 * Gets the plugin language directory.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_lang_path() {
		// generate language directory path.
		$lang_dir = $this->get_slug() . rtrim( $this->get_domain_path(), '/' );

		// return language directory path.
		return $lang_dir;
	}

	/*
	|--------------------------------------------------------------------------
	| HELPER METHODS
	|--------------------------------------------------------------------------
	|
	| This section is for helper methods.
	|
	*/
	/**
	 * Register plugin textdomain.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function register_textdomain() {
		load_plugin_textdomain( $this->get_text_domain(), false, $this->get_lang_path() );
	}

	/**
	 * Get plugin database version.
	 *
	 * @since 1.1.6
	 * @return string (version)
	 */
	public function get_db_version() {
		return get_option( $this->get_prefix() . '_version', null );
	}

	/**
	 * Update plugin database version.
	 *
	 * @param string $version Version.
	 * @param bool   $update  Whether to update or not.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function update_db_version( $version = null, $update = true ) {
		if ( empty( $version ) ) {
			$version = $this->get_version();
		}

		if ( $update ) {
			update_option( $this->get_prefix() . '_version', $version );

			return;
		}

		add_option( $this->get_prefix() . '_version', $version );
	}
	/**
	 * Enqueue scripts helper.
	 *
	 * @param string $handle Name of the script. Should be unique.
	 * @param string $src Relative path to the script from the plugin's assets directory.
	 * @param array  $deps An array of registered script handles this script depends on. Default empty array.
	 * @param bool   $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function register_script( $handle, $src, $deps = [], $in_footer = false ) {
		// check if $src is relative or absolute.
		if ( ! preg_match( '/^(http|https):\/\//', $src ) ) {
			$url  = $this->get_assets_url() . ltrim( $src );
			$path = $this->get_assets_path() . ltrim( $src );
		} else {
			$url  = $src;
			$path = str_replace( $this->get_dir_url(), $this->get_dir_path(), $src );
		}
		$php_file = str_replace( '.js', '.asset.php', $path );
		$asset    = $php_file && file_exists( $php_file ) ? require $php_file : [
			'dependencies' => [],
			'version'      => $this->get_version(),
		];

		$deps = array_merge( $asset['dependencies'], $deps );
		$ver  = $asset['version'];

		wp_register_script( $handle, $url, $deps, $ver, $in_footer );
	}

	/**
	 * Enqueue styles helper.
	 *
	 * @param string $handle Name of the stylesheet. Should be unique.
	 * @param string $src Relative path to the stylesheet from the plugin's assets directory.
	 * @param array  $deps An array of registered stylesheet handles this stylesheet depends on. Default empty array.
	 * @param string $media The media for which this stylesheet has been defined. Default 'all'.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function register_style( $handle, $src, $deps = [], $media = 'all' ) {
		if ( ! preg_match( '/^(http|https):\/\//', $src ) ) {
			$url  = $this->get_assets_url() . ltrim( $src );
			$path = $this->get_assets_path() . ltrim( $src );
		} else {
			$url  = $src;
			$path = str_replace( $this->get_dir_url(), $this->get_dir_path(), $src );
		}
		$php_file = str_replace( '.css', '.asset.php', $path );
		$asset    = $php_file && file_exists( $php_file ) ? require $php_file : [
			'version' => $this->get_version(),
		];
		$ver      = $asset['version'];

		wp_register_style( $handle, $url, $deps, $ver, $media );

		// Add RTL support.
		wp_style_add_data( $handle, 'rtl', 'replace' );
	}

	/**
	 * Enqueue scripts helper.
	 *
	 * @param string $handle Name of the script. Should be unique.
	 * @param string $src Relative path to the script from the plugin's assets directory.
	 * @param array  $deps An array of registered script handles this script depends on. Default empty array.
	 * @param bool   $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function enqueue_script( $handle, $src, $deps = [], $in_footer = false ) {
		$this->register_script( $handle, $src, $deps, $in_footer );
		wp_enqueue_script( $handle );
	}

	/**
	 * Enqueue styles helper.
	 *
	 * @param string $handle Name of the stylesheet. Should be unique.
	 * @param string $src Relative path to the stylesheet from the plugin's assets directory.
	 * @param array  $deps An array of registered stylesheet handles this stylesheet depends on. Default empty array.
	 * @param string $media The media for which this stylesheet has been defined. Default 'all'.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function enqueue_style( $handle, $src, $deps = [], $media = 'all' ) {
		$this->register_style( $handle, $src, $deps, $media );
		wp_enqueue_style( $handle );
	}

	/**
	 * Check if the plugin is active.
	 *
	 * @param string $plugin The plugin slug or basename.
	 *
	 * @since 1.1.6
	 * @return bool
	 */
	public function is_plugin_active( $plugin ) {
		// Check if the $plugin is a basename or a slug. If it's a slug, convert it to a basename.
		if ( false === strpos( $plugin, '/' ) ) {
			$plugin = $plugin . '/' . $plugin . '.php';
		}

		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( $plugin, $active_plugins, true ) || array_key_exists( $plugin, $active_plugins );
	}

	/**
	 * What type of request is this?
	 *
	 * @param string $type admin, ajax, cron or frontend.
	 *
	 * @since 1.1.6
	 * @return bool
	 */
	protected function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin() || ( defined( 'WP_CLI' ) && WP_CLI );
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_request( 'rest' );
			case 'rest':
				if ( empty( $_SERVER['REQUEST_URI'] ) ) {
					return false;
				}
				$rest_prefix = trailingslashit( rest_get_url_prefix() );

				return strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) === 0; // phpcs:ignore

		}

		return false;
	}
}
