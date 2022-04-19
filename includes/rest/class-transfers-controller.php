<?php
/**
 * Transfers Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  Rest
 * @package     Ever_Accounting
 */

namespace Ever_Accounting\REST;

use Ever_Accounting\Transfer;
use Ever_Accounting\Transfers;

defined( 'ABSPATH' ) || die();

/**
 * Class Transfers_Controller
 * @package Ever_Accounting\REST
 */
class Transfers_Controller extends REST_Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 *
	 */
	protected $rest_base = 'transfers';

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 1.1.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		$get_item_args = array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the transfer.', 'wp-ever-accounting' ),
					),
				),
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $get_item_args,
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to read transfers.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'ea_manage_transfer' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view transfers.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a collection of transfers.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		// Ensure an include parameter is set in case the orderby is set to 'include'.
		if ( ! empty( $request['orderby'] ) && 'include' === $request['orderby'] && empty( $request['include'] ) ) {
			return new \WP_Error(
				'rest_orderby_include_missing_include',
				__( 'You need to define an include parameter to order by include.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		// Filter the query arguments for a request.
		$args = apply_filters( 'ever_accounting_rest_transfer_query', $request->get_params(), $request );

		$transfers = Transfers::query( $args );
		$total      = Transfers::query( $args, true );

		$items = array();
		foreach ( $transfers as $transfer ) {
			$data    = $this->prepare_item_for_response( $transfer, $request );
			$items[] = $this->prepare_response_for_collection( $data );
		}

		$page      = (int) $args['paged'];
		$max_pages = ceil( $total / (int) $args['per_page'] );

		if ( $page > $max_pages && $total > 0 ) {
			return new \WP_Error(
				'rest_transfer_invalid_page_number',
				__( 'The page number requested is larger than the number of pages available.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$response = rest_ensure_response( $items );

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
	 * Checks if a given request has access to read a transfer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return true|\WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$transfer = Transfers::get( $request['id'] );
		if ( empty( $transfer ) ) {
			return new \WP_Error(
				'rest_transfer_invalid_id',
				__( 'Invalid transfer id..', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( 'ea_manage_transfer' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to edit transfers.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a single transfer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$transfer = Transfers::get( $request['id'] );
		if ( empty( $transfer ) ) {
			return new \WP_Error(
				'rest_transfer_invalid_id',
				__( 'Invalid transfer id.', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		$data = $this->prepare_item_for_response( $transfer, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has access to create a transfer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return true|\WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_transfer_exists',
				__( 'Cannot create existing transfer.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		if ( ! current_user_can( 'ea_manage_transfer' ) ) {
			return new \WP_Error(
				'rest_cannot_create',
				__( 'Sorry, you are not allowed to edit transfers.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Creates a single transfer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_transfer_exists',
				__( 'Cannot create existing transfer.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$prepared_transfer = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $prepared_transfer ) ) {
			return $prepared_transfer;
		}

		$transfer = Transfers::insert( wp_slash( (array) $prepared_transfer ) );

		if ( is_wp_error( $transfer ) ) {

			if ( 'db_insert_error' === $transfer->get_error_code() ) {
				$transfer->add_data( array( 'status' => 500 ) );
			} else {
				$transfer->add_data( array( 'status' => 400 ) );
			}

			return $transfer;
		}

		$response = $this->prepare_item_for_response( $transfer, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $transfer->get_id() ) ) );

		return $response;
	}


	/**
	 * Checks if a given request has access to update a transfer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return true|\WP_Error True if the request has access to update the item, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$transfer = Transfers::get( $request['id'] );
		if ( empty( $transfer ) ) {
			return new \WP_Error(
				'rest_transfer_invalid_id',
				__( 'Invalid transfer id..', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( 'ea_manage_transfer' ) ) {
			return new \WP_Error(
				'rest_cannot_edit',
				__( 'Sorry, you are not allowed to edit this transfer.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Updates a single transfer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$transfer = Transfers::get( $request['id'] );
		$props    = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $props ) ) {
			return $props;
		}
		$transfer->set_props( $props );
		$is_error = $transfer->save();

		if ( is_wp_error( $is_error ) ) {
			if ( 'db_update_error' === $is_error->get_error_code() ) {
				$is_error->add_data( array( 'status' => 500 ) );
			} else {
				$is_error->add_data( array( 'status' => 400 ) );
			}

			return $is_error;
		}

		$response = $this->prepare_item_for_response( $transfer, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Checks if a given request has access to delete a transfer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return true|\WP_Error True if the request has access to delete the item, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$transfer = Transfers::get( $request['id'] );
		if ( empty( $transfer ) ) {
			return new \WP_Error(
				'rest_transfer_invalid_id',
				__( 'Invalid transfer id..', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( 'ea_manage_transfer' ) ) {
			return new \WP_Error(
				'rest_cannot_delete',
				__( 'Sorry, you are not allowed to delete transfer.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Deletes a single transfer.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		$transfer = Transfers::get( $request['id'] );
		$request->set_param( 'context', 'edit' );
		$data = $this->prepare_item_for_response( $transfer, $request );

		if ( ! $transfer->delete() ) {
			return new \WP_Error( 'rest_cannot_delete', __( 'The transfer cannot be deleted.', 'wp-ever-accounting' ), array( 'status' => 500 ) );
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
	 * Prepares a single transfer output for response.
	 *
	 * @param Transfer $transfer Transfer object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.1.4
	 * @return \WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $transfer, $request ) {
		$data        = $transfer->get_data();
		$format_date = array( 'date_created' );
		// Format date values.
		foreach ( $format_date as $key ) {
			$data[ $key ] = $this->prepare_date_response( $data[ $key ] );
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $transfer, $request ) );

		/**
		 * Filter transfer data returned from the REST API.
		 *
		 * @param \WP_REST_Response $response The response object.
		 * @param Transfer $transfer Transfer object used to create response.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'ever_accounting_rest_prepare_transfer', $response, $transfer, $request );
	}

	/**
	 * Prepares a single transfer for create or update.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.1.4
	 * @return array|\WP_Error Transfer object or WP_Error.
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
		 * Filters transfer before it is inserted via the REST API.
		 *
		 * @param array $props Transfer props.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'ever_accounting_rest_pre_insert_transfer', $props, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param Transfer $transfer Object data.
	 * @param \WP_REST_Request $request Request transfer.
	 *
	 * @return array Links for the given transfer.
	 */
	protected function prepare_links( $transfer, $request ) {
		return array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $transfer->get_id() ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);
	}



	/**
	 * Retrieves the items's schema, conforming to JSON Schema.
	 *
	 * @since 1.1.0
	 *
	 * @return array Item schema data.
	 *
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Transfer', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'           => array(
					'description' => __( 'Unique identifier for the transfer.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'income_id'    => array(
					'description' => __( 'Income ID of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'expense_id'   => array(
					'description' => __( 'Expense ID of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
//				'creator'      => array(
//					'description' => __( 'Creator of the transfer', 'wp-ever-accounting' ),
//					'type'        => 'object',
//					'context'     => array( 'view', 'edit' ),
//					'properties'  => array(
//						'id'    => array(
//							'description' => __( 'Creator ID.', 'wp-ever-accounting' ),
//							'type'        => 'integer',
//							'context'     => array( 'view', 'edit' ),
//							'readonly'    => true,
//						),
//						'name'  => array(
//							'description' => __( 'Creator name.', 'wp-ever-accounting' ),
//							'type'        => 'string',
//							'context'     => array( 'view', 'edit' ),
//						),
//						'email' => array(
//							'description' => __( 'Creator Email.', 'wp-ever-accounting' ),
//							'type'        => 'string',
//							'context'     => array( 'view', 'edit' ),
//						),
//					),
//				),
				'date_created' => array(
					'description' => __( 'Created date of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),

			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Retrieves the query params for the items collection.
	 *
	 * @since 1.1.0
	 *
	 * @return array Collection parameters.
	 *
	 */
	public function get_collection_params() {
		$query_params                       = parent::get_collection_params();
		$query_params['context']['default'] = 'view';

		$params['orderby'] = array(
			'description'       => __( 'Sort collection by object attribute.', 'wp-ever-accounting' ),
			'type'              => 'string',
			'default'           => 'id',
			'enum'              => array(
				'income_id',
				'expense_id',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $query_params;
	}
}
