<?php

namespace EverAccounting;

use EverAccounting\Controllers\Categories;
use EverAccounting\Controllers\Customers;
use EverAccounting\Controllers\Items;
use EverAccounting\Controllers\Bills;
use EverAccounting\Controllers\Accounts;
use EverAccounting\Controllers\Expenses;
use EverAccounting\Controllers\Invoices;
use EverAccounting\Controllers\Payments;
use EverAccounting\Controllers\Taxes;
use EverAccounting\Controllers\Vendors;

/**
 * Class Plugin.
 *
 * @since 1.2.1
 * @package EverAccounting
 *
 * @property Items      $items Items controller.
 * @property Payments   $payments Payments controller.
 * @property Invoices   $invoices Invoices controller.
 * @property Customers  $customers Customers controller.
 * @property Expenses   $expenses Expenses controller.
 * @property Bills      $bills Bills controller.
 * @property Vendors    $vendors Vendors controller.
 * @property Accounts   $accounts Accounts controller.
 * @property Categories $categories Categories controller.
 * @property Taxes      $taxes Taxes controller.
 */
class Plugin extends \ByteKit\Plugin {
	/**
	 * Plugin constructor.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.0.0
	 */
	protected function __construct( $data ) {
		$data['prefix'] = 'eac';
		parent::__construct( $data );
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function define_constants() {
		$upload_dir = wp_upload_dir( null, false );

		define( 'EAC_VERSION', $this->get_version() );
		define( 'EAC_PLUGIN_FILE', $this->get_file() );
		define( 'EAC_PLUGIN_BASENAME', $this->get_basename() );
		define( 'EAC_PLUGIN_PATH', $this->get_dir_path() . '/' );
		define( 'EAC_PLUGIN_URL', $this->get_dir_url() . '/' );
		define( 'EAC_ADMIN_PATH', $this->get_dir_path() . '/admin/' );
		define( 'EAC_UPLOADS_BASEDIR', $upload_dir['basedir'] . '/eac/' );
		define( 'EAC_UPLOADS_DIR', $upload_dir['basedir'] . '/eac/' );
		define( 'EAC_UPLOADS_URL', $upload_dir['baseurl'] . '/eac/' );
		define( 'EAC_LOG_DIR', $upload_dir['basedir'] . '/eac-logs/' );
		define( 'EAC_ASSETS_URL', $this->get_assets_url() . '/' );
		define( 'EAC_ASSETS_DIR', $this->get_assets_path() . '/' );
		define( 'EAC_TEMPLATES_DIR', $this->get_template_path() . '/' );
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function includes() {
		require_once __DIR__ . '/functions.php';
		require_once dirname( __DIR__ ) . '/vendor/woocommerce/action-scheduler/action-scheduler.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init_hooks() {
		register_activation_hook( $this->get_file(), array( Installer::class, 'install' ) );
		add_action( 'plugins_loaded', array( $this, 'on_init' ), 0 );
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Run on init.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_init() {
		$this->services->add( 'installer', new Installer() );

		$this->services->add( 'items', new Controllers\Items() );
		$this->services->add( 'accounts', new Controllers\Accounts() );
		$this->services->add( 'items', new Controllers\Items() );
		$this->services->add( 'invoices', new Controllers\Invoices() );
		$this->services->add( 'payments', new Controllers\Payments() );
		$this->services->add( 'expenses', new Controllers\Expenses() );
		$this->services->add( 'bills', new Controllers\Bills() );
		$this->services->add( 'vendors', new Controllers\Vendors() );
		$this->services->add( 'customers', new Controllers\Customers() );
		$this->services->add( 'categories', new Controllers\Categories() );
		$this->services->add( 'taxes', new Controllers\Taxes() );

		$this->services->add( 'currencies', new Handlers\Currencies() );
		$this->services->add( 'transactions', new Handlers\Transactions() );
		$this->services->add( 'documents', new Handlers\Documents() );
		$this->services->add( 'shortcodes', new Handlers\Shortcodes() );

		if ( is_admin() ) {
			$this->services->add( Admin\Admin::class );
			$this->services->add( Admin\Menus::class );
			$this->services->add( Admin\Scripts::class );
			$this->services->add( Admin\Ajax::class );

			// Dashboard.
			$this->services->add( Admin\Dashboard::class );

			// Items.
			$this->services->add( Admin\Items::class );

			// Sales.
			$this->services->add( Admin\Payments::class );
			$this->services->add( Admin\Invoices::class );
			$this->services->add( Admin\Customers::class );

			// Purchases.
			$this->services->add( Admin\Expenses::class );
			$this->services->add( Admin\Bills::class );
			$this->services->add( Admin\Vendors::class );

			// Banking.
			$this->services->add( Admin\Accounts::class );
			$this->services->add( Admin\Transactions::class );
			$this->services->add( Admin\Transfers::class );

			// Tools.
			$this->services->add( Admin\Tools::class );

			// Reports.
			$this->services->add( Admin\Reports::class );

			// Settings.
			$this->services->add( Admin\Settings::class );
			$this->services->add( Admin\Currencies::class );
			$this->services->add( Admin\Taxes::class );
			$this->services->add( Admin\Categories::class );
		}

		/**
		 * Fires when the plugin is initialized.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_init' );
	}

	/**
	 * Register REST routes.
	 *
	 * @since 1.6.1
	 */
	public function register_routes() {
		$rest_handlers = apply_filters(
			'eac_rest_handlers',
			array(
				'EverAccounting\API\Items',
				'EverAccounting\API\Taxes',
				'EverAccounting\API\Categories',
//				'EverAccounting\API\Currencies',
				'EverAccounting\API\Customers',
				'EverAccounting\API\Vendors',
				'EverAccounting\API\Customers',
				'EverAccounting\API\Accounts',
				'EverAccounting\API\Utilities',
			)
		);
		foreach ( $rest_handlers as $controller ) {
			if ( class_exists( $controller ) ) {
				$this->$controller = new $controller();
				$this->$controller->register_routes();
			}
		}
	}

	/**
	 * Get queue instance.
	 *
	 * @since 1.0.0
	 * @return Utilities\Queue
	 */
	public function queue() {
		return Utilities\Queue::instance();
	}
}
