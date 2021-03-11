<?php
/**
 * Plugin Name: Ever Accounting
 * Plugin URI: https://wpeveraccounting.com/
 * Description: Manage your business finances right from your WordPress dashboard.
 * Version: 1.1.1
 * Author: everaccounting
 * Author URI: https://wpeveraccounting.com/
 * Requires at least: 4.7.0
 * Tested up to: 5.6.1
 * Text Domain: wp-ever-accounting
 * Domain Path: /i18n/languages/
 * License: GPL2+
 *
 * @package EverAccounting
 */


use EverAccounting\Core\Logger;
use EverAccounting\Core\Options;

defined( 'ABSPATH' ) || exit();

/**
 * Class EverAccounting
 *
 * @property-read Logger $logger
 * @property-read Options $options
 *
 * @since 1.0.0
 */
final class EverAccounting {
	/**
	 * EverAccounting version.
	 *
	 * @var string
	 */
	public $version = '1.1.1';

	/**
	 * @var array all plugin's classes
	 *
	 * @var array
	 */
	protected $classes = [];

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 * @var EverAccounting
	 */
	protected static $instance = null;

	/**
	 * Main EverAccounting Instance.
	 *
	 * Ensures only one instance of EverAccounting is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return EverAccounting - Main instance.
	 * @see eaccounting()
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.2
	 * @return void
	 */

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-ever-accounting' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.2
	 * @return void
	 */

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-ever-accounting' ), '1.0.0' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @param mixed $key Key name.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( array_key_exists( $key, $this->classes ) ) {
			return $this->classes[ $key ];
		}

		return $this->{$key};
	}

	/**
	 * Function for add classes to $this->classes
	 * for run using eaccounting()
	 *
	 * @param string $class_name
	 * @param bool $instance
	 *
	 * @since 1.1.4
	 *
	 */
	public function set_class( $class_name, $instance = false ) {
		if ( empty( $this->classes[ $class_name ] ) ) {
			$this->classes[ $class_name ] = $instance ? $class_name::instance() : new $class_name;
		}
	}


	/**
	 * EverAccounting constructor.
	 */
	public function __construct() {
		//register autoloader for include classes
		spl_autoload_register( array( $this, 'autoloader' ) );
		$this->define_constants();
		$this->define_tables();
		$this->includes();

		add_action( 'init', array( $this, 'localization_setup' ) );
		add_action( 'switch_blog', array( $this, 'wpdb_table_fix' ), 0 );
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), - 1 );
		add_action( 'init', array( $this, 'init_classes' ), 0 );
	}

	/**
	 * Auto-load classes on demand to reduce memory consumption.
	 *
	 * @param string $class Class name.
	 */
	public function autoloader( $class ) {
		// project-specific namespace prefix.
		$prefix = 'EverAccounting\\';
		// base directory for the namespace prefix.
		$base_dir = __DIR__ . '/includes/';

		// does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}
		$class = strtolower( $class );
		$relative_class               = substr( $class, $len );
		$array                        = explode( '\\', $relative_class );
		$array[ count( $array ) - 1 ] = 'class-' . end( $array );
		$relative_class               = implode( '\\', $array );
		$file = $base_dir . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		if ( empty( $file ) || ! is_readable( $file ) ) {
			return;
		}

		require_once $file;
	}

	/**
	 * define all required constants
	 *
	 * since 1.0.0
	 *
	 * @return void
	 */
	public function define_constants() {
		$upload_dir = wp_upload_dir( null, false );

		define( 'EACCOUNTING_VERSION', $this->version );
		define( 'EACCOUNTING_BASENAME', plugin_basename( __FILE__ ) );
		define( 'EACCOUNTING_PLUGIN_FILE', __FILE__ );
		define( 'EACCOUNTING_ABSPATH', dirname( EACCOUNTING_PLUGIN_FILE ) );
		define( 'EACCOUNTING_URL', plugins_url( '', EACCOUNTING_PLUGIN_FILE ) );
		define( 'EACCOUNTING_ASSETS_URL', EACCOUNTING_URL . '/assets' );
		define( 'EACCOUNTING_TEMPLATES_DIR', EACCOUNTING_ABSPATH . '/templates' );
		define( 'EACCOUNTING_LOG_DIR', $upload_dir['basedir'] . '/ea-logs/' );
	}

	/**
	 * Register custom tables within $wpdb object.
	 */
	private function define_tables() {
		global $wpdb;

		// List of tables without prefixes.
		$tables = array(
			'contactmeta' => 'ea_contactmeta',
		);

		foreach ( $tables as $name => $table ) {
			$wpdb->$name    = $wpdb->prefix . $table;
			$wpdb->tables[] = $table;
		}
	}

	/**
	 * Include all required files
	 *
	 * since 1.0.0
	 *
	 * @return void
	 */
	public function includes() {

	}

	/**
	 * Initialize plugin for localization
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function localization_setup() {
		$locale = ( get_locale() != '' ) ? get_locale() : 'en_US';
		load_textdomain( 'wp-ever-accounting', WP_LANG_DIR . '/plugins/wp-ever-accounting-' . $locale . '.mo' );
		load_plugin_textdomain( 'wp-ever-accounting', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Set table names inside WPDB object.
	 * @since 1.1.0
	 * @return void
	 */
	public function wpdb_table_fix() {
		$this->define_tables();
	}

	/**
	 * When WP has loaded all plugins, trigger the `eaccounting_loaded` hook.
	 *
	 * This ensures `eaccounting_loaded` is called only after all other plugins
	 * are loaded, to avoid issues caused by plugin directory naming changing
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_plugins_loaded() {
		do_action( 'eaccounting_loaded' );
	}

	/**
	 * Initialize plugin classes.
	 */
	public function init_classes() {
		$this->classes['options'] = new Options();
		$this->classes['logger'] = new Logger();
		$this->classes['ajax'] = new EverAccounting\Ajax();

	}

	/**
	 * Plugin URL getter.
	 *
	 * @param string $path
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	public function plugin_url( $path = '' ) {
		$url = untrailingslashit( plugins_url( '/', EACCOUNTING_PLUGIN_FILE ) );
		if ( $path && is_string( $path ) ) {
			$url = trailingslashit( $url );
			$url .= ltrim( $path, '/' );
		}

		return $url;
	}
}

/**
 * Returns the main instance of Plugin.
 *
 * @since  1.0.0
 * @return EverAccounting
 */
function eaccounting() {
	return EverAccounting::instance();
}

eaccounting();
