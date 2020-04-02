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
		add_filter( 'eaccounting_rest_pre_get_setting', array( $this, 'customized_rest_settings_output' ), 10, 3 );
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
			dirname( __FILE__ ) . '/api/class-ea-rest-files-controller.php'        => 'EAccounting_Files_Controller',
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

	/**
	 * This is a workaround for fixing JS related problem
	 * Our JS select wants object as value but PHP saving string/id so we are hooking here to output
	 * Object instead of string/id
	 * since 1.0.0
	 *
	 * @param $value
	 * @param $name
	 * @param $args
	 *
	 * @return array|object|void|null
	 */
	public function customized_rest_settings_output( $value, $name, $args ) {

		switch ( $name ) {
			case 'default_account':
				$value = eaccounting_get_default_account();
				break;
			case 'default_currency':
				$value = eaccounting_get_default_currency();
				break;
		}

		return $value;
	}

}

EAccounting_API::instance();
