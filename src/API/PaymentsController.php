<?php

namespace EverAccounting\API;
use EverAccounting\Models\Payment;
defined( 'ABSPATH' ) || exit;

/**
 * Class PaymentsController
 *
 * @since 0.0.1
 * @package EverAccounting\API
 */

class PaymentsController extends TransactionsController {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'payments';

	/**
	 * Checks if a given request has access to read payments.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_payment' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view payments.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to create a payment.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_payment' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to create payment.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to read a payment.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function get_item_permissions_check( $request ) {
		$payment = eac_get_payment( $request['id'] );

		if ( empty( $payment ) || ! current_user_can( 'eac_manage_payment' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view this payment.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to update a payment.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function update_item_permissions_check( $request ) {
		$payment = eac_get_payment( $request['id'] );

		if ( empty( $payment ) || ! current_user_can( 'eac_manage_payment' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to update this payment.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to delete a payment.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function delete_item_permissions_check( $request ) {
		$payment = eac_get_payment( $request['id'] );

		if ( empty( $payment ) || ! current_user_can( 'eac_manage_payment' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to delete this payment.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a list of payments.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function get_items( $request ) {
		$params = $this->get_collection_params();
		$args   = array();
		foreach ( $params as $key => $value ) {
			if ( isset( $request[ $key ] ) ) {
				$args[ $key ] = $request[ $key ];
			}
		}
		foreach ( ( new Payment() )->get_core_data_keys() as $key ) {
			if ( isset( $request[ $key ] ) ) {
				$args[ $key ] = $request[ $key ];
			}
		}


		/**
		 * Filters the query arguments for a request.
		 *
		 * Enables adding extra arguments or setting defaults for a payment request.
		 *
		 * @param array $args Key value array of query var to query value.
		 * @param \WP_REST_Request $request The request used.
		 *
		 * @since 1.2.1
		 */
		$args = apply_filters( 'ever_accounting_rest_payment_query', $args, $request );

		$payments = eac_get_payments( $args );
		$total      = eac_get_payments( $args, true );
		$page       = isset( $request['page'] ) ? absint( $request['page'] ) : 1;
		$max_pages  = ceil( $total / (int) $args['per_page'] );

		$results = array();
		foreach ( $payments as $payment ) {
			$data      = $this->prepare_item_for_response( $payment, $request );
			$results[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $results );

		$response->header( 'X-WP-Total', (int) $total );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$request_params = $request->get_query_params();
		$base           = add_query_arg( urlencode_deep( $request_params ), rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );

		if ( $page > 1 ) {
			$prev_page = $page - 1;

			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}

			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );

			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Retrieves a single payment.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function get_item( $request ) {
		$payment = eac_get_payment( $request['id'] );
		$data     = $this->prepare_item_for_response( $payment, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Creates a single payment.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_exists',
				__( 'Cannot create existing payment.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$data = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$payment = eac_insert_payment( $data );
		if ( is_wp_error( $payment ) ) {
			return $payment;
		}

		$response = $this->prepare_item_for_response( $payment, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $payment->get_id() ) ) );

		return $response;

	}

	/**
	 * Updates a single payment.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function update_item( $request ) {
		$payment = eac_get_payment( $request['id'] );
		$data     = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$saved = $payment->set_data( $data )->save();
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		$response = $this->prepare_item_for_response( $payment, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Deletes a single payment.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function delete_item( $request ) {
		$payment = eac_get_payment( $request['id'] );
		$request->set_param( 'context', 'edit' );
		$data = $this->prepare_item_for_response( $payment, $request );

		if ( ! eac_delete_payment( $payment->get_id() ) ) {
			return new \WP_Error(
				'rest_cannot_delete',
				__( 'The payment cannot be deleted.', 'wp-ever-accounting' ),
				array( 'status' => 500 )
			);
		}

		$response = new \WP_REST_Response();
		$response->set_data(
			array(
				'deleted'  => true,
				'previous' => $this->prepare_response_for_collection( $data ),
			)
		);

		return $response;
	}

	/**
	 * Prepares a single payment output for response.
	 *
	 * @param Payment $payment payment object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function prepare_item_for_response( $payment, $request ) {
		$data = [];

		foreach ( array_keys( $this->get_schema_properties() ) as $key ) {
			switch ( $key ) {
				case 'date_created':
				case 'date_updated':
					$value = $this->prepare_date_response( $payment->$key );
					break;
				default:
					$value = $payment->$key;
					break;
			}

			$data[ $key ] = $value;
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $payment, $request ) );

		/**
		 * Filter payment data returned from the REST API.
		 *
		 * @param \WP_REST_Response $response The response object.
		 * @param Payment $payment Category object used to create response.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'ever_accounting_rest_prepare_payment', $response, $payment, $request );
	}

	/**
	 * Prepares a single payment for create or update.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array|\WP_Error payment object or WP_Error.
	 * @since 1.2.1
	 */
	protected function prepare_item_for_database( $request ) {
		$schema    = $this->get_item_schema();
		$data_keys = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );
		$props     = [];
		// Handle all writable props.
		foreach ( $data_keys as $key ) {
			$value = $request[ $key ];
			if ( ! is_null( $value ) ) {
				switch ( $key ) {
					default:
						$props[ $key ] = $value;
						break;
				}
			}
		}

		/**
		 * Filters payment before it is inserted via the REST API.
		 *
		 * @param array $props Payment props.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'ever_accounting_rest_pre_insert_payment', $props, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param Payment $payment Object data.
	 * @param \WP_REST_Request $request Request payment.
	 *
	 * @return array Links for the given payment.
	 */
	protected function prepare_links( $payment, $request ) {
		return array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $payment->get_id() ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);
	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 * @since 1.1.2
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Payment', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'           => array(
					'description' => __( 'Unique identifier for the payment.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'name'         => array(
					'description' => __( 'Payment name.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'description'  => array(
					'description' => __( 'Payment description.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'parent_id'    => array(
					'description' => __( 'Parent payment ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'date_updated' => array(
					'description' => __( "The date the payment was last updated, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created' => array(
					'description' => __( "The date the payment was created, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		/**
		 * Filters the payment's schema.
		 *
		 * @param array $schema Item schema data.
		 *
		 * @since 1.2.1
		 */
		$schema = apply_filters( 'ever_accounting_rest_payment_item_schema', $schema );

		return $this->add_additional_fields_schema( $schema );
	}
}
