<?php
/**
 * Currencies Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

defined( 'ABSPATH' ) || die();

class CurrenciesController extends Controller {
	/**
	 * Route base.
	 *
	 * @var string
	 *
	 * @since 1.1.0
	 */
	protected $rest_base = 'currencies';

	/**
	 * Register our routes.
	 *
	 * @since 1.1.0
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
						'description' => __( 'Unique identifier for the currency.', 'wp-ever-accounting' ),
						'type'        => 'integer',
						'required'    => true,
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
	 * Check whether a given request has permission to read currencies.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 *
	 * @since 1.1.0
	 */
	public function get_items_permissions_check( $request ) {
		return true; // current_user_can( 'manage_currency' );
	}

	/**
	 * Check if a given request has access create currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 *
	 * @since 1.1.0
	 */
	public function create_item_permissions_check( $request ) {
		return true; // current_user_can( 'manage_currency' );
	}

	/**
	 * Check if a given request has access to read a currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 *
	 * @since 1.1.0
	 */
	public function get_item_permissions_check( $request ) {
		return true; // current_user_can( 'manage_currency' );
	}

	/**
	 * Check if a given request has access update a currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 *
	 * @since 1.1.0
	 */
	public function update_item_permissions_check( $request ) {
		return true; // current_user_can( 'manage_currency' );
	}

	/**
	 * Check if a given request has access delete a currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 *
	 * @since 1.1.0
	 */
	public function delete_item_permissions_check( $request ) {
		return true; // current_user_can( 'manage_currency' );
	}

	/**
	 * Check if a given request has access batch create, update and delete items.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 *
	 * @since 1.1.0
	 */
	public function batch_items_permissions_check( $request ) {
		return true; // current_user_can( 'manage_currency' );
	}


	/**
	 * Get all currencies.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 *
	 * @since 1.1.0
	 */
	public function get_items( $request ) {
		$args = array(
			'enabled'  => wp_validate_boolean( $request['enabled'] ),
			'include'  => $request['include'],
			'exclude'  => $request['exclude'],
			'search'   => $request['search'],
			'orderby'  => $request['orderby'],
			'order'    => $request['order'],
			'per_page' => $request['per_page'],
			'page'     => $request['page'],
			'offset'   => $request['offset'],
		);

		$results  = \EverAccounting\Currencies\query( $args )->get_results( OBJECT, '\EverAccounting\Currencies\get' );
		$total    = \EverAccounting\Currencies\query( $args )->count();
		$response = array();
		foreach ( $results as $item ) {
			$data       = $this->prepare_item_for_response( $item, $request );
			$response[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $response );

		$per_page = (int) $args['per_page'];

		$response->header( 'X-WP-Total', (int) $total );

		$max_pages = ceil( $total / $per_page );

		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		return rest_ensure_response( $response );
	}


	/***
	 * Create a currency
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return int|mixed|\WP_Error|\WP_REST_Response|null
	 *
	 * @since 1.1.0
	 */
	public function create_item( $request ) {
		$request->set_param( 'context', 'edit' );
		$prepared = $this->prepare_item_for_database( $request );

		$item_id = eaccounting_insert_currency( (array) $prepared );
		if ( is_wp_error( $item_id ) ) {
			return $item_id;
		}

		$item = eaccounting_get_currency( $item_id );

		$request->set_param( 'context', 'view' );

		$response = $this->prepare_item_for_response( $item, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );

		return $response;
	}


	/**
	 *
	 * Get a single currency
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 *
	 * @since 1.1.0
	 */
	public function get_item( $request ) {
		$item_id = intval( $request['id'] );
		$request->set_param( 'context', 'view' );
		$item = eaccounting_get_currency( $item_id );
		if ( is_null( $item ) ) {
			return new \WP_Error( 'rest_invalid_item_id', __( 'Could not find the currency', 'wp-ever-accounting' ) );
		}

		$response = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Update a currency
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return int|mixed|\WP_Error|\WP_REST_Response|null
	 *
	 * @since 1.1.0
	 */
	public function update_item( $request ) {
		$request->set_param( 'context', 'edit' );
		$item_id = intval( $request['id'] );
		$item    = eaccounting_get_currency( $item_id );
		if ( is_null( $item ) ) {
			return new \WP_Error( 'rest_invalid_item_id', __( 'Could not find the currency', 'wp-ever-accounting' ) );
		}
		$prepared_args       = $this->prepare_item_for_database( $request );
		$prepared_args['id'] = $item_id;

		if ( ! empty( $prepared_args ) ) {
			$updated = eaccounting_insert_currency( (array) $prepared_args );

			if ( is_wp_error( $updated ) ) {
				return $updated;
			}
		}

		$request->set_param( 'context', 'view' );
		$item     = \EverAccounting\Currencies\get( $item_id );
		$response = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Delete a currency
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 *
	 * @since 1.1.0
	 */
	public function delete_item( $request ) {
		$item_id = intval( $request['id'] );
		$item    = eaccounting_get_currency( $item_id );
		if ( is_null( $item ) ) {
			return new \WP_Error( 'rest_invalid_item_id', __( 'Could not find the currency', 'wp-ever-accounting' ) );
		}

		$request->set_param( 'context', 'view' );
		$previous = $this->prepare_item_for_response( $item, $request );
		$retval   = eaccounting_delete_currency( $item_id );
		if ( ! $retval ) {
			return new \WP_Error( 'rest_cannot_delete', __( 'This currency cannot be deleted.', 'wp-ever-accounting' ), array( 'status' => 500 ) );
		}

		$response = new \WP_REST_Response();
		$response->set_data(
			array(
				'deleted'  => true,
				'previous' => $previous->get_data(),
			)
		);

		return $response;
	}

	/**
	 *
	 * Prepare item for response
	 *
	 * @param \WP_REST_Request                    $request
	 *
	 * @param \EverAccounting\Currencies\Currency $item
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 *
	 * @since 1.1.0
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = array(
			'id'                 => $item->get_id(),
			'name'               => $item->get_name(),
			'code'               => $item->get_code(),
			'rate'               => $item->get_rate(),
			'precision'          => $item->get_precision(),
			'symbol'             => $item->get_symbol(),
			'position'           => $item->get_position(),
			'decimal_separator'  => $item->get_decimal_separator(),
			'thousand_separator' => $item->get_thousand_separator(),
			'enabled'            => $item->get_enabled(),
			'created_at'         => eaccounting_rest_date_response( $item->get_date_created() ),
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
	 * Prepare item for database
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return array
	 *
	 * @since 1.1.0
	 */
	public function prepare_item_for_database( $request ) {
		$schema    = $this->get_item_schema();
		$data_keys = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );
		$params    = $request->get_params();
		$data      = array();

		foreach ( $params as $param_key => $param_value ) {
			if ( in_array( $param_key, $data_keys, true ) ) {
				$data[ $param_key ] = $param_value;
			}
		}

		return $data;
	}

	/**
	 * Retrieves the items's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 *
	 * @since 1.1.0
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Currency', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'                 => array(
					'description' => __( 'Unique identifier for the currency.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'name'               => array(
					'description' => __( 'Name of the currency.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'code'               => array(
					'description' => __( 'Unique code for the currency.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'rate'               => array(
					'description' => __( 'Current rate for the currency.', 'wp-ever-accounting' ),
					'type'        => array( 'string', 'numeric' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'precision'          => array(
					'description' => __( 'Precision count.', 'wp-ever-accounting' ),
					'type'        => array( 'string', 'numeric' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'symbol'             => array(
					'description' => __( 'Currency Sumbol.', 'wp-ever-accounting' ),
					'type'        => array( 'string' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'position'           => array(
					'description' => __( 'Position.', 'wp-ever-accounting' ),
					'type'        => array( 'string' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'enum'        => array( 'before', 'after' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'decimal_separator'  => array(
					'description' => __( 'Decimal separator count.', 'wp-ever-accounting' ),
					'type'        => array( 'number' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'thousand_separator' => array(
					'description' => __( 'Thousand separator count.', 'wp-ever-accounting' ),
					'type'        => array( 'number' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'enabled'            => array(
					'description' => __( 'Status of the item.', 'wp-ever-accounting' ),
					'type'        => 'boolean',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'date_created'       => array(
					'description' => __( 'Created date of the currency.', 'wp-ever-accounting' ),
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
	 *
	 * @since 1.1.0
	 */
	public function get_collection_params() {
		$query_params                       = parent::get_collection_params();
		$query_params['context']['default'] = 'view';

		$params['orderby'] = array(
			'description'       => __( 'Sort collection by object attribute.', 'wp-ever-accounting' ),
			'type'              => 'string',
			'default'           => 'id',
			'enum'              => array(
				'name',
				'id',
				'code',
				'rate',
				'enabled',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $query_params;
	}
}
