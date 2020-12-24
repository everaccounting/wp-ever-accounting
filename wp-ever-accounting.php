<?php
/**
 * Plugin Name: WP Ever Accounting
 * Plugin URI: https://wpeveraccounting.com/
 * Description: Manage your business finances right from your WordPress dashboard.
 * Version: 1.0.4
 * Author: everaccounting
 * Author URI: https://wpeveraccounting.com/
 * Requires at least: 4.7.0
 * Tested up to: 5.5.1
 * Text Domain: wp-ever-accounting
 * Domain Path: /i18n/languages/
 * License: GPL2+
 *
 * @package wp-ever-accounting
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class EverAccounting
 *
 * @since 1.0.0
 */
final class EverAccounting {
	/**
	 * EAccounting version.
	 *
	 * @var string
	 */
	public $version = '1.0.4';

	/**
	 * @since 1.0.2
	 * @var \EverAccounting\Utilities;
	 */
	public $utils;

	/**
	 * @since 1.0.2
	 * @var \EAccounting_Settings
	 */
	public $settings;

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
	 * Insures that only one instance of EverAccounting exists in memory at any one
	 * time. Also prevents needing to define globals all over the place
	 *
	 * @since 1.0.0
	 * @return EverAccounting - Main instance.
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EverAccounting ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Return plugin version.
	 *
	 * @since 1.2.0
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Plugin URL getter.
	 *
	 * @since 1.2.0
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function plugin_url( $path = '' ) {
		$url = untrailingslashit( plugins_url( '/', EACCOUNTING_PLUGIN_FILE ) );
		if ( $path && is_string( $path ) ) {
			$url  = trailingslashit( $url );
			$url .= ltrim( $path, '/' );
		}

		return $url;
	}

	/**
	 * Plugin path getter.
	 *
	 * @since 1.2.0
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function plugin_path( $path = '' ) {
		$plugin_path = untrailingslashit( plugin_dir_path( EACCOUNTING_PLUGIN_FILE ) );
		if ( $path && is_string( $path ) ) {
			$plugin_path  = trailingslashit( $plugin_path );
			$plugin_path .= ltrim( $path, '/' );
		}

		return $plugin_path;
	}

	/**
	 * Plugin base path name getter.
	 *
	 * @since 1.2.0
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( __FILE__ );
	}

	/**
	 * Get the template path.
	 *
	 * @since 1.2.0
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'eaccounting_template_path', 'eaccounting/' );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * Initialize plugin for localization
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'wp-ever-accounting', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.2
	 * @return void
	 */

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-ever-accounting' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.2
	 * @return void
	 */

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-ever-accounting' ), '1.0.0' );
	}

	/**
	 * Ensures fatal errors are logged so they can be picked up in the status report.
	 *
	 * @since 1.0.2
	 * @return void
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
			$logger = eaccounting_logger();
			$logger->critical(
			/* translators: 1: error message 2: file name and path 3: line number */
				sprintf( __( '%1$s in %2$s on line %3$s', 'wp-ever-accounting' ), $error['message'], $error['file'], $error['line'] ) . PHP_EOL,
				array(
					'source' => 'fatal-errors',
				)
			);
		}
	}

	/**
	 * EverAccounting constructor.
	 */
	public function __construct() {
		$this->define_constants();
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
		define( 'EACCOUNTING_DB_VERSION', '20181123' );
		define( 'EACCOUNTING_BASENAME', plugin_basename( __FILE__ ) );
		define( 'EACCOUNTING_PLUGIN_FILE', __FILE__ );
		define( 'EACCOUNTING_ABSPATH', dirname( EACCOUNTING_PLUGIN_FILE ) );
		define( 'EACCOUNTING_URL', plugins_url( '', EACCOUNTING_PLUGIN_FILE ) );
		define( 'EACCOUNTING_ASSETS_URL', EACCOUNTING_URL . '/assets' );
		define( 'EACCOUNTING_TEMPLATES_DIR', EACCOUNTING_ABSPATH . '/templates' );
		define( 'EACCOUNTING_LOG_DIR', $upload_dir['basedir'] . '/ea-logs/' );
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
		require_once EACCOUNTING_ABSPATH . '/includes/abstracts/abstract-ea-widget.php';
		require_once EACCOUNTING_ABSPATH . '/includes/abstracts/abstract-ea-registry.php';

		// Core classes.
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-install.php';
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-utilities.php';
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-settings.php';
		require_once EACCOUNTING_ABSPATH . '/includes/class-ea-ajax.php';

		require_once EACCOUNTING_ABSPATH . '/includes/ea-core-functions.php';

		//
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			require_once EACCOUNTING_ABSPATH . '/includes/admin/class-ea-admin.php';
		}
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init_hooks() {
		register_activation_hook( EACCOUNTING_PLUGIN_FILE, array( 'EAccounting_Install', 'install' ) );
		register_shutdown_function( array( $this, 'log_errors' ) );

		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), - 1 );
		add_action( 'init', array( $this, 'init_plugin' ), 0 );
		add_action( 'init', array( $this, 'localization_setup' ) );
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
	 * Init EAccounting when WordPress Initialises.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function init_plugin() {
		// Before init action.
		do_action( 'before_eaccounting_init' );

//		\EverAccounting\Controllers\AccountController::instance();
//		\EverAccounting\Controllers\TransferController::instance();
//		\EverAccounting\Controllers\CategoryController::instance();
		\EverAccounting\Controllers\CurrencyController::instance();
//		\EverAccounting\Controllers\TransactionController::instance();
//		\EverAccounting\Controllers\TaxController::instance();
//		\EverAccounting\Controllers\ItemController::instance();
//		\EverAccounting\Controllers\IncomeController::instance();
//		\EverAccounting\Controllers\ExpenseController::instance();
		\EverAccounting\Controllers\InvoiceController::instance();

		\EverAccounting\REST\Manager::instance();
		\EverAccounting\Core\Emails::instance();

		$this->settings = new EAccounting_Settings();
		$this->utils    = new \EverAccounting\Utilities();

		// Init action.
		do_action( 'eaccounting_init' );
	}

	/**
	 * Email Class.
	 *
	 * @since 1.0.2
	 * @return \EverAccounting\Core\Mailer
	 */
	public function mailer() {
		return new \EverAccounting\Core\Mailer();
	}
}

/**
 * Returns the main instance of Plugin.
 *
 * @since  1.0.0
 * @return EverAccounting
 */
function eaccounting() {
	return EverAccounting::init();
}

eaccounting();
