<?php
/**
 * Plugin Name: WP Ever Accounting
 * Plugin URI: https://pluginever.com/plugins/wp-ever-crm
 * Description: Best WordPress Accounting plugin for small office
 * Version: 1.0.1
 * Author: pluginever
 * Author URI: https://pluginever.com/
 * Requires at least: 4.7.0
 * Tested up to: 5.3
 * Text Domain: wp-ever-accounting
 * Domain Path: /i18n/languages/
 * License: GPL2+
 *
 * @package wp-ever-accounting
 */

defined( 'ABSPATH' ) || exit();

final class EverAccounting {
	/**
	 * EAccounting version.
	 *
	 * @var string
	 */
	public $version = '1.0.1';

	/**
	 * The single instance of the class.
	 *
	 * @var EverAccounting
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main EAccounting Instance.
	 *
	 * Ensures only one instance of EAccounting is loaded or can be loaded.
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
	 * EverAccounting Constructor.
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
		define( 'EACCOUNTING_VERSION', $this->version );
		define( 'EACCOUNTING_DB_VERSION', '20181123' );
		define( 'EACCOUNTING_PLUGIN_FILE', __FILE__ );
		define( 'EACCOUNTING_ABSPATH', dirname( EACCOUNTING_PLUGIN_FILE ) );
		define( 'EACCOUNTING_URL', plugins_url( '', EACCOUNTING_PLUGIN_FILE ) );
		define( 'EACCOUNTING_ASSETS_URL', EACCOUNTING_URL . '/assets' );
		define( 'EACCOUNTING_TEMPLATES_DIR', EACCOUNTING_ABSPATH . '/templates' );
		define( 'EACCOUNTING_DB_PREFIX', 'ea_' );
	}

	/**
	 * Register custom tables within $wpdb object.
	 */
	private function define_tables() {
		global $wpdb;
		$tables = array(
			'ea_contacts',
			'ea_accounts',
			'ea_categories',
			'ea_payments',
			'ea_revenues',
			'ea_transfers',
			'ea_taxes',
			'ea_items',
			'ea_invoices',
			'ea_currencies',
			'ea_invoice_item_taxes',
			'ea_files',
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
		//classes
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-install.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-form.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-ajax.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-api.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-money.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-currency.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-csv-exporter.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-account.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-contact.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-item.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-revenue.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-payment.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/class-ea-scripts.php' );

		//functions
		require_once( EACCOUNTING_ABSPATH . '/includes/formatting-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/misc-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/eaccounting-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/contact-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/category-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/file-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/income-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/expense-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/tax-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/account-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/item-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/currency-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/settings-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/transaction-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/transfer-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/report-functions.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/helper-functions.php' );

		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			require_once( EACCOUNTING_ABSPATH . '/includes/admin/class-ea-admin.php' );
		}
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		register_activation_hook( EACCOUNTING_PLUGIN_FILE, array( 'EAccounting_Install', 'install' ) );

		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), - 1 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( $this, 'localization_setup' ) );
		add_action( 'init', array( $this, 'set_eaccounting_actions' ) );
		add_action( 'init', 'eaccounting_register_initial_settings' );
		add_action( 'activated_plugin', array( $this, 'activated_plugin' ) );
		add_action( 'deactivated_plugin', array( $this, 'deactivated_plugin' ) );
	}

	/**
	 * When WP has loaded all plugins, trigger the `eaccounting_loaded` hook.
	 *
	 * This ensures `eaccounting_loaded` is called only after all other plugins
	 * are loaded, to avoid issues caused by plugin directory naming changing
	 *
	 * @since 1.0.0
	 */
	public function on_plugins_loaded() {
		do_action( 'eaccounting_loaded' );
	}

	/**
	 * Init EAccounting when WordPress Initialises.
	 */
	public function init() {
		// Before init action.
		do_action( 'before_eaccounting_init' );
		// Init action.
		do_action( 'eaccounting_init' );
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
	public function set_eaccounting_actions() {
		$key = ! empty( $_GET['eaccounting_action'] ) ? sanitize_key( $_GET['eaccounting_action'] ) : false;

		if ( ! empty( $key ) ) {
			do_action( 'eaccounting_action_' . $key, $_GET );
		}

		$key = ! empty( $_POST['eaccounting_action'] ) ? sanitize_key( $_POST['eaccounting_action'] ) : false;

		if ( ! empty( $key ) ) {
			do_action( 'eaccounting_action_' . $key, $_GET );
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
		return untrailingslashit( plugins_url( '/', EACCOUNTING_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( EACCOUNTING_PLUGIN_FILE ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'eaccounting_template_path', 'ever-accounting/' );
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
