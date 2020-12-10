<?php
/**
 * Taxes Rest Controller Class.
 *
 * @package     EverAccounting
 * @subpackage  Api
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

class EAccounting_Transactions_Controller extends EAccounting_REST_Controller {
	/**
	 * @var string
	 */
	protected $namespace = 'ea/v1';

	/**
	 * @var string
	 */
	protected $rest_base = 'transactions';

	/**
	 * @since 1.0.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the object.', 'wp-ever-accounting' ),
						'type'        => 'integer',
						'required'    => true,
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->get_context_param( array( 'default' => 'view' ) ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/import',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'handle_import' ],
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
			)
		);
	}

	/**
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|WP_Error|WP_REST_Response
	 * @since       1.0.2
	 */
	public function get_items( $request ) {
		$dates = $this->get_query_dates( $request['date'] );
		$args  = array(
			'account_id'       => $request['account_id'],
			'category_id'      => $request['category_id'],
			'parent_id'        => $request['parent_id'],
			'contact_id'       => $request['contact_id'],
			'type'             => $request['type'],
			'include'          => $request['include'],
			'exclude'          => $request['exclude'],
			'search'           => $request['search'],
			'orderby'          => $request['orderby'],
			'start_date'       => $dates['start_date'],
			'end_date'         => $dates['end_date'],
			'order'            => $request['order'],
			'per_page'         => $request['per_page'],
			'page'             => $request['page'],
			'offset'           => $request['offset'],
			'include_transfer' => $request['include_transfer'],
			'nopaging'         => $request['nopaging'],
		);

		$query_result = eaccounting_get_transactions( $args );
		$total_items  = eaccounting_get_transactions( $args, true );
		$response     = array();

		foreach ( $query_result as $item ) {
			$data       = $this->prepare_item_for_response( $item, $request );
			$response[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $response );

		$per_page = (int) $args['per_page'];

		$response->header( 'X-WP-Total', (int) $total_items );

		$max_pages = ceil( $total_items / $per_page );

		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		return rest_ensure_response( $response );
	}

	/***
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return int|mixed|WP_Error|WP_REST_Response|null
	 * @since       1.0.2
	 */
	public function create_item( $request ) {
		$request->set_param( 'context', 'edit' );

		$prepared = $this->prepare_item_for_database( $request );

		$item_id = eaccounting_insert_transaction( (array) $prepared );
		if ( is_wp_error( $item_id ) ) {
			return $item_id;
		}

		$item = eaccounting_get_transaction( $item_id );

		$request->set_param( 'context', 'view' );

		$response = $this->prepare_item_for_response( $item, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );

		return $response;
	}

	/**
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|WP_Error|WP_REST_Response
	 * @since       1.0.2
	 */
	public function get_item( $request ) {
		$item_id = intval( $request['id'] );
		$request->set_param( 'context', 'view' );
		$item = eaccounting_get_transaction( $item_id );
		if ( is_null( $item ) ) {
			return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the transaction', 'wp-ever-accounting' ) );
		}

		$response = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $response );
	}

	/**
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return int|mixed|WP_Error|WP_REST_Response|null
	 * @since       1.0.2
	 */
	public function update_item( $request ) {
		$request->set_param( 'context', 'edit' );
		$item_id = intval( $request['id'] );

		$item = eaccounting_get_transaction( $item_id );
		if ( is_null( $item ) ) {
			return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the revenue', 'wp-ever-accounting' ) );
		}
		$prepared_args = $this->prepare_item_for_database( $request );

		$prepared_args->id = $item_id;

		if ( ! empty( $prepared_args ) ) {
			$updated = eaccounting_insert_transaction( (array) $prepared_args );

			if ( is_wp_error( $updated ) ) {
				return $updated;
			}
		}

		$request->set_param( 'context', 'view' );
		$item     = eaccounting_get_transaction( $item_id );
		$response = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $response );
	}

	/**
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return void|WP_Error|WP_REST_Response
	 * @since       1.0.2
	 */
	public function delete_item( $request ) {
		$item_id = intval( $request['id'] );
		$item    = eaccounting_get_transaction( $item_id );
		if ( is_null( $item ) ) {
			return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the transaction', 'wp-ever-accounting' ) );
		}

		$request->set_param( 'context', 'view' );

		$previous = $this->prepare_item_for_response( $item, $request );
		$retval   = eaccounting_delete_transaction( $item_id );
		if ( is_wp_error( $retval ) ) {
			return new WP_Error( 'rest_cannot_delete', $retval->get_error_message(), array( 'status' => 500 ) );
		}

		$response = new WP_REST_Response();
		$response->set_data(
			array(
				'deleted'  => true,
				'previous' => $previous->get_data(),
			)
		);

		return $response;
	}

	public function handle_import( $request ) {

	}

	/**
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return object|stdClass|WP_Error
	 * @since       1.0.2
	 */
	public function prepare_item_for_database( $request ) {
		$prepared_item = new stdClass();
		$schema        = $this->get_item_schema();

		if ( ! empty( $schema['properties']['id'] ) && isset( $request['id'] ) ) {
			$prepared_item->id = $request['id'];
		}
		if ( ! empty( $schema['properties']['type'] ) && isset( $request['type'] ) ) {
			$prepared_item->type = $request['type'];
		}
		if ( ! empty( $schema['properties']['payment_date'] ) && isset( $request['payment_date'] ) ) {
			$prepared_item->payment_date = $request['payment_date'];
		}
		if ( ! empty( $schema['properties']['amount'] ) && isset( $request['amount'] ) ) {
			$prepared_item->amount = $request['amount'];
		}
		if ( ! empty( $schema['properties']['account_id'] ) && isset( $request['account_id'] ) ) {
			$prepared_item->account_id = $request['account_id'];
		}
		if ( ! empty( $schema['properties']['contact_id'] ) && isset( $request['contact_id'] ) ) {
			$prepared_item->contact_id = $request['contact_id'];
		}
		if ( ! empty( $schema['properties']['category_id'] ) && isset( $request['category_id'] ) ) {
			$prepared_item->category_id = $request['category_id'];
		}
		if ( ! empty( $schema['properties']['description'] ) && isset( $request['description'] ) ) {
			$prepared_item->description = $request['description'];
		}
		if ( ! empty( $schema['properties']['payment_method'] ) && isset( $request['payment_method'] ) ) {
			$prepared_item->payment_method = $request['payment_method'];
		}
		if ( ! empty( $schema['properties']['reference'] ) && isset( $request['reference'] ) ) {
			$prepared_item->reference = $request['reference'];
		}
		if ( ! empty( $schema['properties']['file_id'] ) && isset( $request['file_id'] ) ) {
			$prepared_item->file_id = $request['file_id'];
		}
		if ( ! empty( $schema['properties']['parent_id'] ) && isset( $request['parent_id'] ) ) {
			$prepared_item->parent_id = $request['parent_id'];
		}
		if ( ! empty( $schema['properties']['reconciled'] ) && isset( $request['reconciled'] ) ) {
			$prepared_item->reconciled = $request['reconciled'];
		}

		return $prepared_item;
	}

	/**
	 *
	 * @param mixed           $item
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|WP_Error|WP_REST_Response
	 * @since       1.0.2
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = array(
			'id'             => $item->id,
			'type'           => $item->type,
			'payment_date'        => $item->payment_date,
			'amount'         => eaccounting_money( $item->amount, $item->currency_code, true )->format(),
			'currency_code'  => $item->currency_code,
			'currency_rate'  => $item->currency_rate,
			'account'        => self::get_rest_object( 'accounts', $item->account_id ),
			'contact'        => eaccounting_get_contact( $item->contact_id ),
			'category'       => eaccounting_get_category( $item->category_id ),
			'description'    => $item->description,
			'payment_method' => $item->payment_method,
			'reference'      => $item->reference,
			'file'           => self::get_rest_object( 'files', $item->file_id ),
			'reconciled'     => $item->reconciled,
			'created_at'     => $this->prepare_date_response( $item->created_at ),
		);

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $item ) );

		return $response;
	}

	/**
	 *
	 * @param $item
	 *
	 * @return array
	 * @since       1.0.2
	 */
	protected function prepare_links( $item ) {
		$base = sprintf( '/%s/%s/', $this->namespace, $this->rest_base );
		$url  = $base . $item->id;

		// Entity meta.
		$links = array(
			'self'       => array(
				'href' => rest_url( $url ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
		);

		return $links;
	}

	/**
	 * Retrieves the items's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 * @since 1.0.2
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Transaction', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'             => array(
					'description' => __( 'Unique identifier for the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'type'           => array(
					'description' => __( 'Type of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'payment_date'        => array(
					'description' => __( 'Payment Date of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'embed', 'view' ),
					'required'    => true,
				),
				'amount'         => array(
					'description' => __( 'Amount of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'required'    => true,
					'readonly'    => true,
				),
				'account_id'     => array(
					'description' => __( 'Account id of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
					'readonly'    => true,
				),
				'description'    => array(
					'description' => __( 'Description of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'readonly'    => true,
				),
				'category_id'    => array(
					'description' => __( 'Category id of the transaction', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
					'required'    => true,
					'readonly'    => true,
				),
				'payment_method' => array(
					'description' => __( 'Method of the payment', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_key',
					),
					'required'    => true,
					'readonly'    => true,
				),
				'reference'      => array(
					'description' => __( 'Reference of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
				),
				'file'           => array(
					'description' => __( 'Attachment url of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'readonly'    => true,
				),
				'reconciled'     => array(
					'description' => __( 'Reconciliation of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'readonly'    => true,
				),
				'date_created'   => array(
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
	 * @return array Collection parameters.
	 * @since 1.0.2
	 */
	public function get_collection_params() {
		$query_params                       = parent::get_collection_params();
		$query_params['context']['default'] = 'view';
		$query_params['exclude']            = array(
			'description' => __( 'Ensure result set excludes specific ids.', 'wp-ever-accounting' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		$query_params['include'] = array(
			'description' => __( 'Limit result set to specific IDs.', 'wp-ever-accounting' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		$query_params['search'] = array(
			'description' => __( 'Limit result set to specific search.', 'wp-ever-accounting' ),
			'type'        => 'string',
			'default'     => '',
		);

		$params['type'] = array(
			'description'       => __( 'Limit the result with active or inactive type', 'wp-ever-accounting' ),
			'default'           => '',
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['orderby'] = array(
			'description' => __( 'Sort collection by contact attribute.', 'wp-ever-accounting' ),
			'type'        => 'string',
			'default'     => 'payment_date',
		);

		return $query_params;
	}

}
