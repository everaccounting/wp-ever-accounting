<?php
/**
 * Handle main rest api Class.
 *
 * @since       1.0.2
 *
 * @package     EverAccounting
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || die();

class API {
	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.2
	 * @var API
	 */
	protected static $_instance = null;

	/**
	 * Main API Instance.
	 *
	 * Ensures only one instance of EAccounting is loaded or can be loaded.
	 *
	 * @since 1.0.2
	 * @static
	 * @return API - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * ECRM_API constructor.
	 *
	 * @since 1.0.2
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
	}

	/**
	 * Register our rest controllers.
	 * s
	 *
	 * @since 1.0.2
	 */
	public function register_rest_routes() {
		require_once( dirname( __FILE__ ) . '/api/class-ea-rest-controller.php' );

		$rest_handlers = array(
<<<<<<< HEAD
			dirname( __FILE__ ) . '/api/class-ea-rest-customers-controller.php'  => 'EverAccounting\API\Customers_Controller',
			dirname( __FILE__ ) . '/api/class-ea-rest-categories-controller.php' => 'EverAccounting\API\Categories_Controller',
=======
			dirname( __FILE__ ) . '/api/class-ea-rest-contacts-controller.php'   => 'EverAccounting\API\Contacts_Controller',
			//dirname( __FILE__ ) . '/api/class-ea-rest-categories-controller.php' => 'EverAccounting\API\Categories_Controller',
>>>>>>> e0017115839eda986c97abc3d463023d3c04207f
			//          dirname( __FILE__ ) . '/api/class-ea-rest-currencies-controller.php'   => 'EverAccounting\API\Currencies_Controller',
			//          dirname( __FILE__ ) . '/api/class-ea-rest-accounts-controller.php'     => 'EverAccounting\API\Accounts_Controller',
			//          dirname( __FILE__ ) . '/api/class-ea-rest-transfers-controller.php'    => 'EverAccounting\API\Transfers_Controller',
			//          dirname( __FILE__ ) . '/api/class-ea-rest-transactions-controller.php' => 'EverAccounting\API\Transactions_Controller',
			//          dirname( __FILE__ ) . '/api/class-ea-rest-reports-controller.php'      => 'EverAccounting\API\Reports_Controller',
			//          dirname( __FILE__ ) . '/api/class-ea-rest-settings-controller.php'     => 'EverAccounting\API\Settings_Controller',
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

API::instance();
