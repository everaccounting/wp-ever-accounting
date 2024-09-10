<?php

namespace EverAccounting\API;

use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customers
 *
 * @since 0.0.1
 * @package EverAccounting\API
 */
class Customers extends Contacts {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'customers';

	/**
	 * Checks if a given request has access to read customers.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_customer' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view customers.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to create a customer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_customer' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to create customers.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to read a customer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$customer = eac_get_customer( $request['id'] );

		if ( empty( $customer ) || ! current_user_can( 'eac_manage_customer' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view this customer.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to update a customer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$customer = eac_get_customer( $request['id'] );

		if ( empty( $customer ) || ! current_user_can( 'eac_manage_customer' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to update this customer.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to delete a customer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$customer = eac_get_customer( $request['id'] );

		if ( empty( $customer ) || ! current_user_can( 'eac_manage_customer' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to delete this customer.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a list of customers.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$params = $this->get_collection_params();
		$args   = array();
		foreach ( $params as $key => $value ) {
			if ( isset( $request[ $key ] ) ) {
				$args[ $key ] = $request[ $key ];
			}
		}

		/**
		 * Filters the query arguments for a request.
		 *
		 * Enables adding extra arguments or setting defaults for a customer request.
		 *
		 * @param array            $args Key value array of query var to query value.
		 * @param \WP_REST_Request $request The request used.
		 *
		 * @since 1.2.1
		 */
		$args = apply_filters( 'ever_accounting_rest_customer_query', $args, $request );

		$customers   = eac_get_customers( $args );
		$total     = eac_get_customers( $args, true );
		$page      = isset( $request['page'] ) ? absint( $request['page'] ) : 1;
		$max_pages = ceil( $total / (int) $args['per_page'] );


		$results = array();
		foreach ( $customers as $customer ) {
			$data      = $this->prepare_item_for_response( $customer, $request );
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
	 * Retrieves a single customer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$customer = eac_get_customer( $request['id'] );
		$data   = $this->prepare_item_for_response( $customer, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Creates a single customer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_exists',
				__( 'Cannot create existing customer.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$data = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$customer = eac_insert_customer( $data );
		if ( is_wp_error( $customer ) ) {
			return $customer;
		}

		$response = $this->prepare_item_for_response( $customer, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $customer->get_id() ) ) );

		return $response;

	}

	/**
	 * Updates a single customer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$customer = eac_get_customer( $request['id'] );
		$data   = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$saved = $customer->set_data( $data )->save();
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		$response = $this->prepare_item_for_response( $customer, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Deletes a single customer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		$customer = eac_get_customer( $request['id'] );
		$request->set_param( 'context', 'edit' );
		$data = $this->prepare_item_for_response( $customer, $request );

		if ( ! eac_delete_customer( $customer->get_id() ) ) {
			return new \WP_Error(
				'rest_cannot_delete',
				__( 'The customer cannot be deleted.', 'wp-ever-accounting' ),
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
	 * Prepares a single customer output for response.
	 *
	 * @param customer           $customer customer object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $customer, $request ) {
		$data = [];

		foreach ( array_keys( $this->get_schema_properties() ) as $key ) {
			switch ( $key ) {
				case 'date_created':
				case 'date_updated':
					$value = $this->prepare_date_response( $customer->$key );
					break;
				default:
					$value = $customer->$key;
					break;
			}

			$data[ $key ] = $value;
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $customer, $request ) );

		/**
		 * Filter customer data returned from the REST API.
		 *
		 * @param \WP_REST_Response $response The response object.
		 * @param customer          $customer customer object used to create response.
		 * @param \WP_REST_Request  $request Request object.
		 */
		return apply_filters( 'ever_accounting_rest_prepare_customer', $response, $customer, $request );
	}

	/**
	 * Prepares a single customer for create or update.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array|\WP_Error Category object or WP_Error.
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
		 * Filters customer before it is inserted via the REST API.
		 *
		 * @param array            $props customer props.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'ever_accounting_rest_pre_insert_customer', $props, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param customer           $customer Object data.
	 * @param \WP_REST_Request $request Request customer.
	 *
	 * @return array Links for the given customer.
	 */
	protected function prepare_links( $customer, $request ) {
		return array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $customer->get_id() ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);
	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @since 1.1.2
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$schema = parent::get_item_schema();
		$schema['title'] = __( 'customer', 'wp-ever-accounting' );
		/**
		 * Filters the customer's schema.
		 *
		 * @param array $schema Item schema data.
		 *
		 * @since 1.2.1
		 */
		$schema = apply_filters( 'ever_accounting_rest_customer_item_schema', $schema );

		return $this->add_additional_fields_schema( $schema );
	}
}
