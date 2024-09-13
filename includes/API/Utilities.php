<?php

namespace EverAccounting\API;

defined( 'ABSPATH' ) || exit;

/**
 * Class Utilities
 *
 * @package EverAccounting\API
 */
class Utilities extends Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'utilities';

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @see register_rest_route()
	 * @since 1.1.0
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/next-number',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_next_number' ),
				'permission_callback' => call_user_func_array( 'current_user_can', array( 'manage_accounting' ) ),
				'args'                => array(
					'type' => array(
						'default'           => 'invoice',
						'sanitize_callback' => 'sanitize_text_field',
						'required'          => true,
						'validate_callback' => 'rest_validate_request_arg',
					),
				),
			)
		);
	}

	/**
	 * Get next number.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_next_number( $request ) {
		$type = $request->get_param( 'type' );

		return rest_ensure_response( array( 'next_number' => eac_get_next_number( $type ) ) );
	}
}
