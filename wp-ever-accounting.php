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
 * @package wp-ever-accounting
 */

use EverAccounting\Controllers\Items;
use EverAccounting\Logger;

defined( 'ABSPATH' ) || exit();

/**
 * Class EverAccounting
 *
 * @property-read Logger $logger
 * @property-read Items $items
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
	 * @since 1.0.2
	 * @var \EverAccounting\Utilities;
	 */
	public $utils;

	/**
	 * @since 1.0.2
	 * @var \EverAccounting_Settings
	 */
	public $settings;

	/**
	 * Holds various class instances
	 *
	 * @var array
	 */
	protected $container = [];

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
	 * @see eaccounting()
	 * @return EverAccounting - Main instance.
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
	 * @return void
	 * @since 1.0.2
	 */

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-ever-accounting' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @return void
	 * @since 1.0.2
	 */

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-ever-accounting' ), '1.0.0' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @param mixed $key Key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( array_key_exists( $key, $this->container ) ) {
			return $this->container[ $key ];
		}

		return $this->{$key};
	}

	/**
	 * EverAccounting constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->define_tables();
		$this->includes();
		$this->init_hooks();
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
		require_once EACCOUNTING_ABSPATH . '/vendor/autoload.php';

		// Abstract classes.
		require_once EACCOUNTING_ABSPATH . '/includes/abstracts/abstract-ea-registry.php';

		// Core classes.
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-install.php';
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-utilities.php';
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-settings.php';
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-ajax.php';
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-assets.php';
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-rewrites.php';
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-license.php';
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-controller.php';
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-compatibility.php';
		require_once EACCOUNTING_ABSPATH . '/includes/ea-core-functions.php';

		//
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			require_once EACCOUNTING_ABSPATH . '/includes/admin/class-ea-admin.php';
		}


		//\EverAccounting\REST\Manager::instance();
		\EverAccounting\Core\Emails::instance();

		$this->settings = new EverAccounting_Settings();
		$this->utils    = new \EverAccounting\Utilities();
	}

	/**
	 * When WP has loaded all plugins, trigger the `eaccounting_loaded` hook.
	 *
	 * This ensures `eaccounting_loaded` is called only after all other plugins
	 * are loaded, to avoid issues caused by plugin directory naming changing
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function on_plugins_loaded() {
		do_action( 'eaccounting_loaded' );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function init_hooks() {
		register_activation_hook( EACCOUNTING_PLUGIN_FILE, array( 'EverAccounting_Install', 'install' ) );
		register_shutdown_function( array( $this, 'log_errors' ) );

		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), - 1 );
		add_action( 'init', array( $this, 'localization_setup' ) );
		add_action( 'switch_blog', array( $this, 'wpdb_table_fix' ), 0 );
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Ensures fatal errors are logged so they can be picked up in the status report.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function log_errors() {
		$error = error_get_last();
		if ( $error && in_array(
				$error['type'],
				array(
					E_ERROR,
					E_PARSE,
					E_COMPILE_ERROR,
					E_USER_ERROR,
					E_RECOVERABLE_ERROR,
				),
				true
			) ) {
			$this->logger->log_critical(
			/* translators: 1: error message 2: file name and path 3: line number */
				sprintf( __( '%1$s in %2$s on line %3$s', 'wp-ever-accounting' ), $error['message'], $error['file'], $error['line'] ) . PHP_EOL,
				array(
					'source' => 'fatal-errors',
				)
			);
		}
	}

	/**
	 * Initialize plugin for localization
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'wp-ever-accounting', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
	}

	/**
	 * Set table names inside WPDB object.
	 * @return void
	 * @since 1.1.0
	 */
	public function wpdb_table_fix() {
		$this->define_tables();
	}

	/**
	 * Init EverAccounting when WordPress Initialises.
	 */
	public function init() {
		// Before init action.
		do_action( 'before_eaccounting_init' );

		$this->container['logger'] = new Logger();
		$this->container['items'] = new Items();

		// Init action.
		do_action( 'eaccounting_init' );
	}

	/**
	 * Return plugin version.
	 *
	 * @return string
	 * @since 1.2.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Plugin URL getter.
	 *
	 * @param string $path
	 *
	 * @return string
	 * @since 1.2.0
	 *
	 */
	public function plugin_url( $path = '' ) {
		$url = untrailingslashit( plugins_url( '/', EACCOUNTING_PLUGIN_FILE ) );
		if ( $path && is_string( $path ) ) {
			$url = trailingslashit( $url );
			$url .= ltrim( $path, '/' );
		}

		return $url;
	}

	/**
	 * Plugin path getter.
	 *
	 * @param string $path
	 *
	 * @return string
	 * @since 1.2.0
	 *
	 */
	public function plugin_path( $path = '' ) {
		$plugin_path = untrailingslashit( plugin_dir_path( EACCOUNTING_PLUGIN_FILE ) );
		if ( $path && is_string( $path ) ) {
			$plugin_path = trailingslashit( $plugin_path );
			$plugin_path .= ltrim( $path, '/' );
		}

		return $plugin_path;
	}

	/**
	 * Plugin base path name getter.
	 *
	 * @return string
	 * @since 1.2.0
	 */
	public function plugin_basename() {
		return plugin_basename( __FILE__ );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 * @since 1.2.0
	 */
	public function template_path() {
		return apply_filters( 'eaccounting_template_path', 'eaccounting/' );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * Email Class.
	 *
	 * @return \EverAccounting\Core\Mailer
	 * @since 1.0.2
	 */
	public function mailer() {
		return new \EverAccounting\Core\Mailer();
	}
}

/**
 * Returns the main instance of Plugin.
 *
 * @return EverAccounting
 * @since  1.0.0
 */
function eaccounting() {
	return EverAccounting::instance();
}

eaccounting();
