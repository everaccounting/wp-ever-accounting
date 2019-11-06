<?php
/**
 * Plugin Name: WP Ever Accounting
 * Plugin URI: https://pluginever.com/plugins/wp-ever-crm
 * Description: Best WordPress CRM plugin
 * Version: 1.0.0
 * Author: pluginever
 * Author URI: https://pluginever.com/
 * Requires at least: 4.7.0
 * Tested up to: 5.1
 * Text Domain: wp-ever-accounting
 * Domain Path: /languages/
 * License: GPL2+
 *
 * @package wp-ever-accounting
 */

defined( 'ABSPATH' ) || exit();

final class EverAccounting {
	/**
	 * EverAccounting version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var EverAccounting
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Wrapper for custom cache
	 *
	 * @var EAccounting_Cache
	 */
	public $cache;

	/**
	 * Main EverAccounting Instance.
	 *
	 * Ensures only one instance of EverAccounting is loaded or can be loaded.
	 *
	 * @return EverAccounting - Main instance.
	 * @since 1.0.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	/**
	 * Cloning is forbidden.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'wp-ever-accounting' ), '1.0.0' );
	}

	/**
	 * Universalizing instances of this class is forbidden.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'wp-ever-accounting' ), '1.0.0' );
	}

	/**
	 * WooCommerce Constructor.
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
		define( 'EVER_ACCOUNTING_VERSION', $this->version );
		define( 'EVER_ACCOUNTING_DB_VERSION', '20181123' );
		define( 'EVER_ACCOUNTING_PLUGIN_FILE', __FILE__ );
		define( 'EVER_ACCOUNTING_ABSPATH', dirname( EVER_ACCOUNTING_PLUGIN_FILE ) );
		define( 'EVER_ACCOUNTING_URL', plugins_url( '', EVER_ACCOUNTING_PLUGIN_FILE ) );
		define( 'EVER_ACCOUNTING_ASSETS_URL', EVER_ACCOUNTING_URL . '/assets' );
		define( 'EVER_ACCOUNTING_TEMPLATES_DIR', EVER_ACCOUNTING_ABSPATH . '/templates' );
		define( 'EVER_ACCOUNTING_CACHE_KEY', 'eacc' );
		define( 'EVER_ACCOUNTING_DB_PREFIX', 'eacc_' );
	}

	/**
	 * Register custom tables within $wpdb object.
	 */
	private function define_tables() {
		global $wpdb;
		$tables = array(
			'ea_accounts',
			'ea_products',
			'ea_taxes',
			'ea_categories',
		);
		foreach ( $tables as $table ) {
			$wpdb->$table   = $wpdb->prefix . $table;
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
		require_once( EVER_ACCOUNTING_ABSPATH . '/includes/class-ea-install.php' );
		require_once( EVER_ACCOUNTING_ABSPATH . '/includes/class-ea-caching.php' );
        require_once( EVER_ACCOUNTING_ABSPATH . '/includes/misc-functions.php' );
        require_once( EVER_ACCOUNTING_ABSPATH . '/includes/account-functions.php' );
        require_once( EVER_ACCOUNTING_ABSPATH . '/includes/product-functions.php' );
        require_once( EVER_ACCOUNTING_ABSPATH . '/includes/category-functions.php' );
        require_once( EVER_ACCOUNTING_ABSPATH . '/includes/tax-functions.php' );
        require_once( EVER_ACCOUNTING_ABSPATH . '/includes/formatting-functions.php' );
        require_once( EVER_ACCOUNTING_ABSPATH . '/includes/template-functions.php' );

        require_once( EVER_ACCOUNTING_ABSPATH . '/includes/abstracts/class-ea-ajax.php' );

        if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			require_once( EVER_ACCOUNTING_ABSPATH . '/includes/admin/class-ea-admin.php' );
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			require_once( EVER_ACCOUNTING_ABSPATH . '/includes/class-ea-cli.php' );
		}
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		register_activation_hook( EVER_ACCOUNTING_PLUGIN_FILE, array( 'EAccounting_Install', 'install' ) );

		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), - 1 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( $this, 'localization_setup' ) );
		add_action( 'init', array( $this, 'set_get_actions' ) );
		add_action( 'activated_plugin', array( $this, 'activated_plugin' ) );
		add_action( 'deactivated_plugin', array( $this, 'deactivated_plugin' ) );
	}

	/**
	 * When WP has loaded all plugins, trigger the `ever_accounting_loaded` hook.
	 *
	 * This ensures `ever_accounting_loaded` is called only after all other plugins
	 * are loaded, to avoid issues caused by plugin directory naming changing
	 *
	 * @since 1.0.0
	 */
	public function on_plugins_loaded() {
		do_action( 'ever_accounting_loaded' );
	}

	/**
	 * Init EverAccounting when WordPress Initialises.
	 */
	public function init() {
		// Before init action.
		do_action( 'before_ever_accounting_init' );

		//setup our caching
		$this->cache = new EAccounting_Cache( 'eaccounting' );

		// Init action.
		do_action( 'ever_accounting_init' );
	}

	/**
	 * Initialize plugin for localization
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'wp-ever-accounting', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages/' );
	}

	/**
	 * Hooks Accounting actions, when present in the $_GET superglobal. Every eaccounting_action
	 * present in $_GET is called using WordPress's do_action function. These
	 * functions are called on init.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function set_get_actions() {
		$key = ! empty( $_GET['eaccounting_action'] ) ? sanitize_key( $_GET['eaccounting_action'] ) : false;
		if ( ! empty( $key ) ) {
			do_action( "eaccounting_action_{$key}", $_GET );
		}
	}

	/**
	 * Ran when any plugin is activated.
	 *
	 * @param string $filename The filename of the activated plugin.
	 *
	 * @since 1.0.0
	 */
	public function activated_plugin( $filename ) {

	}

	/**
	 * Ran when any plugin is deactivated.
	 *
	 * @param string $filename The filename of the deactivated plugin.
	 *
	 * @since 1.0.0
	 */
	public function deactivated_plugin( $filename ) {

	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', EVER_ACCOUNTING_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( EVER_ACCOUNTING_PLUGIN_FILE ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'ever_accounting_template_path', 'ever-accounting/' );
	}
}


/**
 * Returns the main instance of Plugin.
 *
 * @return EverAccounting
 * @since  1.0.0
 */
function ever_accounting() {
	return EverAccounting::instance();
}

ever_accounting();
