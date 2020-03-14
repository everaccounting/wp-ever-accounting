<?php
defined( 'ABSPATH' ) || die();

class EAccounting_API {

	/**
	 * The single instance of the class.
	 *
	 * @var EAccounting_API
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main EAccounting_API Instance.
	 *
	 * Ensures only one instance of EAccounting is loaded or can be loaded.
	 *
	 * @return EAccounting_API - Main instance.
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
	 * ECRM_API constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
	}

	/**
	 * Register our rest controllers.
	 * s
	 * @since 1.0.0
	 */
	public function register_rest_routes() {
		require_once( dirname( __FILE__ ) . '/api/class-ea-rest-controller.php' );

		$rest_handlers = array(
			dirname( __FILE__ ) . '/api/class-ea-rest-contacts-controller.php'     => 'EAccounting_Contacts_Controller',
			dirname( __FILE__ ) . '/api/class-ea-rest-categories-controller.php'   => 'EAccounting_Categories_Controller',
			dirname( __FILE__ ) . '/api/class-ea-rest-currencies-controller.php'   => 'EAccounting_Currencies_Controller',
			dirname( __FILE__ ) . '/api/class-ea-rest-accounts-controller.php'     => 'EAccounting_Accounts_Controller',
			dirname( __FILE__ ) . '/api/class-ea-rest-payments-controller.php'     => 'EAccounting_Payments_Controller',
			dirname( __FILE__ ) . '/api/class-ea-rest-revenues-controller.php'     => 'EAccounting_Revenues_Controller',
			dirname( __FILE__ ) . '/api/class-ea-rest-transfers-controller.php'    => 'EAccounting_Transfers_Controller',
			dirname( __FILE__ ) . '/api/class-ea-rest-taxes-controller.php'        => 'EAccounting_Taxes_Controller',
			dirname( __FILE__ ) . '/api/class-ea-rest-transactions-controller.php' => 'EAccounting_Transactions_Controller',
			dirname( __FILE__ ) . '/api/class-ea-rest-items-controller.php'        => 'EAccounting_Items_Controller',
			dirname( __FILE__ ) . '/api/class-ea-rest-settings-controller.php'     => 'EAccounting_Settings_Controller',
		);

		foreach ( $rest_handlers as $file_name => $controller ) {
			if ( file_exists( $file_name ) ) {
				require_once( $file_name );
				$this->$controller = new $controller();
				$this->$controller->register_routes();
			}
		}
	}

}

EAccounting_API::instance();
