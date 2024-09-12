<?php

namespace EverAccounting\API;

use EverAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit();

/**
 * CurrenciesController.php
 *
 * @since     1.1.6
 * @subpackage EverAccounting\API
 * @package   EverAccounting
 */
class Currencies extends Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'currencies';

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @see register_rest_route()
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
	 * Checks if a given request has access to read currencies.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_currency' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view currencies.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to create a currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_currency' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to create currencies.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to read a currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function get_item_permissions_check( $request ) {
		$id       = ! empty( $request['id'] ) ? $request['id'] : $request['code'];
		$currency = eac_get_currency( $id );

		if ( empty( $currency ) || ! current_user_can( 'eac_manage_currency' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view this currency.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to update a currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function update_item_permissions_check( $request ) {
		$id       = ! empty( $request['id'] ) ? $request['id'] : $request['code'];
		$currency = eac_get_currency( $id );

		if ( empty( $currency ) || ! current_user_can( 'eac_manage_currency' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to update this currency.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to delete a currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function delete_item_permissions_check( $request ) {
		$id       = ! empty( $request['id'] ) ? $request['id'] : $request['code'];
		$currency = eac_get_currency( $id );

		if ( empty( $currency ) || ! current_user_can( 'eac_manage_currency' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to delete this currency.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a list of currencies.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function get_items( $request ) {
		$params = $this->get_collection_params();
		foreach ( $params as $key => $value ) {
			if ( isset( $request[ $key ] ) ) {
				$args[ $key ] = $request[ $key ];
			}
		}

		/**
		 * Filters the query arguments for a request.
		 *
		 * Enables adding extra arguments or setting defaults for a currency request.
		 *
		 * @param array $args Key value array of query var to query value.
		 * @param \WP_REST_Request $request The request used.
		 *
		 * @since 1.2.1
		 */
		$args = apply_filters( 'eac_rest_currency_query', $args, $request );

		$currencies = eac_get_currencies( $args );
		$total      = eac_get_currencies( $args, true );
		$page       = isset( $request['page'] ) ? absint( $request['page'] ) : 1;
		$max_pages  = ceil( $total / (int) $args['per_page'] );

		// If requesting page is greater than max pages, return empty array.
		if ( $page > $max_pages ) {
			return new \WP_Error(
				'rest_invalid_page_number',
				__( 'The page number requested is larger than the number of pages available.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$results = array();
		foreach ( $currencies as $currency ) {
			$data      = $this->prepare_item_for_response( $currency, $request );
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
	 * Retrieves a single currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function get_item( $request ) {
		$id       = ! empty( $request['id'] ) ? $request['id'] : $request['code'];
		$currency = eac_get_currency( $id );
		$data     = $this->prepare_item_for_response( $currency, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Creates a single currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_account_exists',
				__( 'Cannot create existing currency.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$data = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$currency = eac_insert_currency( $data );
		if ( is_wp_error( $currency ) ) {
			return $currency;
		}

		$response = $this->prepare_item_for_response( $currency, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $currency->id ) ) );

		return $response;

	}

	/**
	 * Updates a single currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function update_item( $request ) {
		$currency = eac_get_currency( $request['id'] );
		$data     = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$currency   = eac_insert_currency( $data );
		if ( is_wp_error( $currency ) ) {
			return $currency;
		}

		$response = $this->prepare_item_for_response( $currency, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Deletes a single currency.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function delete_item( $request ) {
		$currency = eac_get_currency( $request['id'] );
		$request->set_param( 'context', 'edit' );
		$data = $this->prepare_item_for_response( $currency, $request );

		if ( ! eac_delete_currency( $currency->id ) ) {
			return new \WP_Error(
				'rest_account_cannot_delete',
				__( 'The currency cannot be deleted.', 'wp-ever-accounting' ),
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
	 * Prepares a single currency output for response.
	 *
	 * @param Currency $item Currency object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = [];

		foreach ( array_keys( $this->get_schema_properties() ) as $key ) {
			switch ( $key ) {
				case 'date_created':
				case 'date_updated':
					$value = $this->prepare_date_response( $item->$key );
					break;
				default:
					$value = $item->$key;
					break;
			}

			$data[ $key ] = $value;
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $item, $request ) );

		/**
		 * Filter currency data returned from the REST API.
		 *
		 * @param \WP_REST_Response $response The response object.
		 * @param currency $currency currency object used to create response.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'eac_rest_prepare_currency', $response, $item, $request );
	}

	/**
	 * Prepares a single currency for create or update.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array|\WP_Error currency object or WP_Error.
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
		 * Filters currency before it is inserted via the REST API.
		 *
		 * @param array $props currency props.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'eac_rest_pre_insert_currency', $props, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param currency $currency Object data.
	 * @param \WP_REST_Request $request Request currency.
	 *
	 * @return array Links for the given currency.
	 */
	protected function prepare_links( $currency, $request ) {
		return array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $currency->id ) ),
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
			'title'      => __( 'currency', 'wp-ever-accounting' ),
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
				'code'               => array(
					'description' => __( 'currency code.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'name'               => array(
					'description' => __( 'currency name.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'precision'          => array(
					'description' => __( 'currency precision.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'symbol'             => array(
					'description' => __( 'currency symbol.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'decimal_separator'  => array(
					'description' => __( 'currency decimal separator.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'thousand_separator' => array(
					'description' => __( 'currency thousand separator.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'position'           => array(
					'description' => __( 'currency position.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'enum'        => array( 'before', 'after' ),
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'exchange_rate'      => array(
					'description' => __( 'currency exchange rate.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'auto_update'        => array(
					'description' => __( 'currency auto update.', 'wp-ever-accounting' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'status'             => array(
					'description' => __( 'currency status.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'enum'        => array( 'active', 'inactive' ),
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'date_updated'       => array(
					'description' => __( "The date the currency was last updated, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created'       => array(
					'description' => __( "The date the currency was created, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		/**
		 * Filters the currency's schema.
		 *
		 * @param array $schema Item schema data.
		 *
		 * @since 1.2.1
		 */
		$schema = apply_filters( 'eac_rest_currency_item_schema', $schema );

		return $this->add_additional_fields_schema( $schema );
	}

}
