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
				'\EverAccounting\Rest\AccountsController',
				'\EverAccounting\Rest\CustomersController',
				'\EverAccounting\Rest\VendorsController',
				'\EverAccounting\Rest\ExpensesController',
				'\EverAccounting\Rest\PaymentsController',
				'\EverAccounting\Rest\RevenuesController',
				'\EverAccounting\Rest\ItemsController',
				'\EverAccounting\Rest\CategoriesController',
				'\EverAccounting\Rest\CurrenciesController',
				'\EverAccounting\Rest\TransfersController',
				'\EverAccounting\Rest\CodesController',
				'\EverAccounting\Rest\CountriesController',
				'\EverAccounting\Rest\DataController',
				'\EverAccounting\Rest\TaxesController',
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
