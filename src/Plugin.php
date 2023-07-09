<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin.
 *
 * @property-read Models\Category $categories
 * @property-read Models\Tax $taxes
 * @property-read Models\Currency $currencies
 * @property-read Models\Account $accounts
 * @property-read Models\Transfer $transfers
 * @property-read Models\Payment $payments
 * @property-read Models\Expense $expenses
 * @property-read Models\Invoice $invoices
 * @property-read Models\Bill $bills
 * @property-read Models\Customer $customers
 * @property-read Models\Vendor $vendors
 * @property-read Models\Item $items
 *
 *
 * @since   1.1.6
 * @package EverAccounting
 */
class Plugin extends BasePlugin {

	/**
	 * Plugin constructor.
	 *
	 * @param array $args Plugin arguments.
	 *
	 * @since 1.1.6
	 */
	public function __construct( $args ) {
		parent::__construct( $args );
		// Handle legacy version.
		$version = get_option( 'eaccounting_version' );
		if ( $version && empty( $this->get_db_version() ) ) {
			$this->update_db_version( $version );
		}
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Magic method to get property.
	 *
	 * @param string $name Property name.
	 *
	 * @since 1.1.6
	 */
	public function __get( $name ) {
		$properties = array(
			'categories' => Models\Category::class,
			'taxes'      => Models\Tax::class,
			'currencies' => Models\Currency::class,
			'accounts'   => Models\Account::class,
			'transfers'  => Models\Transfer::class,
			'payments'   => Models\Payment::class,
			'expenses'   => Models\Expense::class,
			'invoices'   => Models\Invoice::class,
			'bills'      => Models\Bill::class,
			'customers'  => Models\Customer::class,
			'vendors'    => Models\Vendor::class,
			'items'      => Models\Item::class,
		);

		if ( isset( $properties[ $name ] ) ) {
			return $properties[ $name ]::get_instance();
		}

		return null;
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

		define( 'EAC_VERSION', $this->get_version() );
		define( 'EAC_PLUGIN_BASENAME', $this->get_basename() );
		define( 'EAC_PLUGIN_FILE', $this->get_file() );
		define( 'EAC_PLUGIN_PATH', $this->get_dir_path() );
		define( 'EAC_PLUGIN_URL', $this->get_dir_url() );
		define( 'EAC_UPLOADS_DIR', $upload_dir['basedir'] . '/ever-accounting' );
		define( 'EAC_UPLOADS_URL', $upload_dir['baseurl'] . '/ever-accounting' );
		define( 'EAC_LOG_DIR', $upload_dir['basedir'] . '/ever-accounting-logs/' );
		define( 'EAC_ASSETS_URL', $this->get_assets_url() );
		define( 'EAC_ASSETS_DIR', $this->get_assets_path() );
		define( 'EAC_TEMPLATES_DIR', EAC_PLUGIN_FILE . '/templates' );
	}

	/**
	 * Include all required files
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function includes() {
		require_once __DIR__ . '/Functions.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function init_hooks() {
		register_activation_hook( $this->get_file(), array( Installer::class, 'install' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
	}

	/**
	 * Init plugin when WordPress Initialises.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		// Before init action.
		do_action( 'before_ever_accounting_init' );

		// Load class instances.
		Installer::instantiate();
		Rewrites::instantiate();
		Scripts::instantiate();
		Notices::instantiate();
		Actions::instantiate();
		Cache::instantiate();
		API::instantiate();
		Shortcodes::instantiate();

		// If frontend.
		if ( self::is_request( 'frontend' ) ) {
			Frontend\Frontend::instantiate();
		}

		if ( self::is_request( 'admin' ) ) {
			Admin\Admin::instantiate();
		}

		// Init action.
		do_action( 'ever_accounting_init' );
	}
}
