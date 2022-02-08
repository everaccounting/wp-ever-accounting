<?php
/**
 * Handle main rest api Class.
 *
 * @since       1.1.0
 *
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Abstracts\Singleton;

defined( 'ABSPATH' ) || die();

/**
 * Class Manager
 * @package EverAccounting\REST
 */
class Manager extends Singleton {
	/**
	 * Manager constructor.
	 */
	public function __construct() {
		if ( ! class_exists( '\WP_REST_Server' ) ) {
			return;
		}
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
	}

	public function register_rest_routes() {
		$rest_handlers = apply_filters(
			'eaccounting_rest_controllers',
			array(
				REST_Items_Controller::class,
				REST_Accounts_Controller::class,
				REST_Customers_Controller::class,
				REST_Vendors_Controller::class,
				REST_Categories_Controller::class,
				REST_Currencies_Controller::class,
				REST_Revenues_Controller::class,
				REST_Payments_Controller::class,
				REST_Transfers_Controller::class,
				REST_Invoice_Items_Controller::class,
			)
		);
		foreach ( $rest_handlers as $controller ) {
			if ( class_exists( $controller ) ) {
				$this->$controller = new $controller();
				$this->$controller->register_routes();
			}
		}
	}
}
