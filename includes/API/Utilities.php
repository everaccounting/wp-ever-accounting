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
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'type' => array(
						'default'           => 'invoice',
						'sanitize_callback' => 'sanitize_text_field',
						'required'          => true,
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

		$number = 1;

		switch ( $type ) {
			case 'invoice':
				$invoice = new \EverAccounting\Models\Invoice();
				$number  = $invoice->get_next_number();
				break;

//			case 'bill':
//				$bill   = new \EverAccounting\Models\Bill();
//				$number = $bill->get_next_number();
//				break;
//
//			case 'expense':
//				$expense = new \EverAccounting\Models\Expense();
//				$number  = $expense->get_next_number();
//				break;
//
//			case 'payment':
//				$payment = new \EverAccounting\Models\Payment();
//				$number  = $payment->get_next_number();
//				break;

			default:
				$number = apply_filters( 'eac_get_next_' . $type . '_number', $number );
		}

		return rest_ensure_response( array( 'next_number' => apply_filters( 'eac_get_next_number', $number, $type ) ) );
	}
}
