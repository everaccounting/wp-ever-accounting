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

	public function register_rest_routes() {
		require_once( dirname( __FILE__ ) . '/api/class-ea-rest-controller.php' );

		$rest_handlers = array(
			dirname( __FILE__ ) . '/api/class-ea-rest-contacts-controller.php' => 'EAccounting_Contacts_Controller',
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
