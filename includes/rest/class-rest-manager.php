<?php
/**
 * Handle main rest api Class.
 *
 * @since       1.1.0
 * @package     Ever_Accounting
 */

namespace Ever_Accounting\REST;


defined( 'ABSPATH' ) || die();

/**
 * Class REST_Manager
 */
class REST_Manager {
	/**
	 * Manager constructor.
	 */
	public static function init() {
		if ( ! class_exists( '\WP_REST_Server' ) ) {
			return;
		}
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest_routes' ), 10 );
	}

	/**
	 * Register rest routes.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public static function register_rest_routes() {
		$rest_handlers = apply_filters(
			'ever_accounting_rest_controllers',
			array(
				Currencies_Controller::class
//				'\Ever_Accounting\REST\Accounts_Controller',
//				'\Ever_Accounting\REST\Customers_Controller',
//				'\Ever_Accounting\REST\Vendors_Controller',
//				'\Ever_Accounting\REST\Payments_Controller',
//				'\Ever_Accounting\REST\Revenues_Controller',
//				'\Ever_Accounting\REST\Categories_Controller',
//				'\Ever_Accounting\REST\Currencies_Controller',
//				'\Ever_Accounting\REST\Transfers_Controller',
//				'\Ever_Accounting\REST\Codes_Controller',
//				'\Ever_Accounting\REST\Countries_Controller',
//				'\Ever_Accounting\REST\Data_Controller',
//				'\Ever_Accounting\REST\Items_Controller',
			)
		);
		foreach ( $rest_handlers as $rest_handler ) {
			if ( class_exists( $rest_handler ) ) {
				$controller = new $rest_handler();
				$controller->register_routes();
			}
		}
	}
}
