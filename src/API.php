<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class API
 *
 * @package EverAccounting
 * @since   1.6.1
 */
class API extends Singleton {

	/**
	 * API constructor.
	 */
	protected function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
	}

	/**
	 * Register REST routes.
	 *
	 * @since 1.6.1
	 */
	public function register_rest_routes() {
		$rest_handlers = apply_filters(
			'ever_accounting_rest_handlers',
			array(
				'EverAccounting\API\AccountsController',
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
