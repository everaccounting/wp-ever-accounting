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
 *
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

	/**
	 * Register routes
	 *
	 * @since 1.0.2
	 */
	public function register_rest_routes() {
		$rest_handlers = apply_filters(
			'eaccounting_rest_controllers',
			array(
				'\EverAccounting\REST\Accounts_Controller',
				'\EverAccounting\REST\Customers_Controller',
				'\EverAccounting\REST\Vendors_Controller',
				'\EverAccounting\REST\Payments_Controller',
				'\EverAccounting\REST\Revenues_Controller',
				'\EverAccounting\REST\Categories_Controller',
				'\EverAccounting\REST\Currencies_Controller',
				'\EverAccounting\REST\Transfers_Controller',
				'\EverAccounting\REST\Codes_Controller',
				'\EverAccounting\REST\Countries_Controller',
				'\EverAccounting\REST\Data_Controller',
				'\EverAccounting\REST\Items_Controller',
				'\EverAccounting\REST\Invoices_Controller',
				'\EverAccounting\REST\Bills_Controller',
			)
		);
		foreach ( $rest_handlers as $controller ) {
			if ( class_exists( $controller ) ) {
				$this->$controller = new $controller();
				$this->$controller->register_routes();
			} else {
				eaccounting()->logger->log_error( __( 'Controller Not Found', 'wp-ever-accounting' ) );
			}
		}
	}
}
