<?php
/**
 * Items Rest Controller Class.
 *
 * @since       1.1.2
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Item;
use EverAccounting\Item_Query;

defined( 'ABSPATH' ) || die();

/**
 * Items controller class
 */
class Items_Controller extends REST_Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'items';

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
						'description' => __( 'Unique identifier for the item.', 'wp-ever-accounting' ),
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
	 * Checks if a given request has access to read items.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'ea_manage_item' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view items.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a collection of items.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
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

		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();
		$args       = array();

		/*
		 * This array defines mappings between public API query parameters whose
		 * values are accepted as-passed, and their internal WP_Query parameter
		 * name equivalents (some are the same). Only values which are also
		 * present in $registered will be set.
		 */
		$parameter_mappings = array();

		/*
		 * For each known parameter which is both registered and present in the request,
		 * set the parameter's value on the query $args.
		 */
		foreach ( $parameter_mappings as $api_param => $wp_param ) {
			if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
				$args[ $wp_param ] = $request[ $api_param ];
			}
		}

		$args['offset']   = $request['offset'];
		$args['order']    = $request['order'];
		$args['orderby']  = $request['orderby'];
		$args['paged']    = $request['paged'];
		$args['include']  = $request['include'];
		$args['per_page'] = $request['per_page'];
		$args['search']   = $request['search'];

		// Filter the query arguments for a request.
		$args = apply_filters( 'eaccounting_rest_item_query', $args, $request );

		$item_query   = new Item_Query( $args );
		$query_result = $item_query->get_results();
		$query_total  = $item_query->get_total();

		$items = array();

		foreach ( $query_result as $item ) {
			$data    = $this->prepare_item_for_response( $item, $request );
			$items[] = $this->prepare_response_for_collection( $data );
		}

		$page      = (int) $item_query->query_vars['paged'];
		$max_pages = ceil( $query_total / (int) $item_query->query_vars['number'] );

		if ( $page > $max_pages && $query_total > 0 ) {
			return new \WP_Error(
				'rest_item_invalid_page_number',
				__( 'The page number requested is larger than the number of pages available.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$response = rest_ensure_response( $items );

		$response->header( 'X-WP-Total', (int) $query_total );
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
	 * Checks if a given request has access to read an item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function get_item_permissions_check( $request ) {
		$item = eaccounting_get_item( $request['id'] );
		if ( empty( $item ) ) {
			return new \WP_Error(
				'rest_item_invalid_id',
				__( 'Invalid item id.', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( 'ea_manage_item' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to edit items.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a single item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function get_item( $request ) {
		$item = eaccounting_get_item( $request['id'] );
		if ( empty( $item ) ) {
			return new \WP_Error(
				'rest_item_invalid_id',
				__( 'Invalid item id.', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		$data = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Checks if a given request has access to create an item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has access to create items, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_item_exists',
				__( 'Cannot create existing item.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		if ( ! current_user_can( 'ea_manage_item' ) ) {
			return new \WP_Error(
				'rest_cannot_create',
				__( 'Sorry, you are not allowed to edit items.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Creates a single item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_item_exists',
				__( 'Cannot create existing item.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$prepared_item = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $prepared_item ) ) {
			return $prepared_item;
		}

		$item = eaccounting_insert_item( wp_slash( (array) $prepared_item ) );

		if ( is_wp_error( $item ) ) {

			if ( 'db_insert_error' === $item->get_error_code() ) {
				$item->add_data( array( 'status' => 500 ) );
			} else {
				$item->add_data( array( 'status' => 400 ) );
			}

			return $item;
		}

		$response = $this->prepare_item_for_response( $item, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $item->get_id() ) ) );

		return $response;
	}


	/**
	 * Checks if a given request has access to update a item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has access to update the item, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function update_item_permissions_check( $request ) {
		$item = eaccounting_get_item( $request['id'] );
		if ( empty( $item ) ) {
			return new \WP_Error(
				'rest_item_invalid_id',
				__( 'Invalid item id..', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( 'ea_manage_item' ) ) {
			return new \WP_Error(
				'rest_cannot_edit',
				__( 'Sorry, you are not allowed to edit this item.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Updates a single item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function update_item( $request ) {
		$item  = eaccounting_get_item( $request['id'] );
		$props = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $props ) ) {
			return $props;
		}
		$item->set_props( $props );
		$is_error = $item->save();

		if ( is_wp_error( $is_error ) ) {
			if ( 'db_update_error' === $is_error->get_error_code() ) {
				$is_error->add_data( array( 'status' => 500 ) );
			} else {
				$is_error->add_data( array( 'status' => 400 ) );
			}

			return $is_error;
		}

		$response = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Checks if a given request has access to delete a item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has access to delete the item, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function delete_item_permissions_check( $request ) {
		$item = eaccounting_get_item( $request['id'] );
		if ( empty( $item ) ) {
			return new \WP_Error(
				'rest_item_invalid_id',
				__( 'Invalid item id..', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( 'ea_manage_item' ) ) {
			return new \WP_Error(
				'rest_cannot_delete',
				__( 'Sorry, you are not allowed to delete item.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Deletes a single item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function delete_item( $request ) {
		$item = eaccounting_get_item( $request['id'] );
		$request->set_param( 'context', 'edit' );
		$data = $this->prepare_item_for_response( $item, $request );

		if ( ! $item->delete() ) {
			return new \WP_Error( 'rest_cannot_delete', __( 'The item cannot be deleted.', 'wp-ever-accounting' ), array( 'status' => 500 ) );
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
	 * Prepares a single item output for response.
	 *
	 * @param Item $item Item object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response Response object.
	 * @since 1.2.1
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data        = $item->to_array();
		$format_date = array( 'date_created' );
		// Format date values.
		foreach ( $format_date as $key ) {
			$data[ $key ] = $this->prepare_date_response( $data[ $key ] );
		}

		if ( ! empty( $data['category_id'] ) ) {
			$data['category'] = eaccounting_rest_request( 'categories', [ 'id' => $data['category_id'] ] );
		}

		unset( $data['category_id'] );

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $item, $request ) );

		/**
		 * Filter item data returned from the REST API.
		 *
		 * @param \WP_REST_Response $response The response object.
		 * @param Item $item Item object used to create response.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'eaccounting_rest_prepare_item', $response, $item, $request );
	}

	/**
	 * Prepares a single item for create or update.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array|\WP_Error Item object or WP_Error.
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
		 * Filters item before it is inserted via the REST API.
		 *
		 * @param array $props Item props.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'eaccounting_rest_pre_insert_item', $props, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param Item $item Object data.
	 * @param \WP_REST_Request $request Request item.
	 *
	 * @return array Links for the given item.
	 */
	protected function prepare_links( $item, $request ) {
		return array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $item->get_id() ) ),
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
			'title'      => __( 'Item', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'             => array(
					'description' => __( 'Unique identifier for the item.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'name'           => array(
					'description' => __( 'Name of the item.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'sku'            => array(
					'description' => __( 'Sku of the item.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'thumbnail'      => array(
					'description' => __( 'Thumbnail of the item', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view', 'edit' ),
					'properties'  => array(
						'id'  => array(
							'description' => __( 'Thumbnail ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'embed', 'view', 'edit' ),
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'src' => array(
							'description' => __( 'Thumbnail src.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'embed', 'view' ),
							'arg_options' => array(
								'sanitize_callback' => 'esc_url_raw',
							),
						),
					),
				),
				'description'    => array(
					'description' => __( 'Description of the item.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
				),
				'sale_price'     => array(
					'description' => __( 'Sale price of the item', 'wp-ever-accounting' ),
					'type'        => 'floatval',
					'context'     => array( 'embed', 'view', 'edit' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'purchase_price' => array(
					'description' => __( 'Purchase price of the item', 'wp-ever-accounting' ),
					'type'        => 'floatval',
					'context'     => array( 'embed', 'view', 'edit' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'quantity'       => array(
					'description' => __( 'Quantity of the item.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'default'     => '1',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => false,
				),
				'category'       => array(
					'description' => __( 'Category of the item.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => false,
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Category ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'embed', 'view', 'edit' ),
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'type' => array(
							'description' => __( 'Category Type.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'embed', 'view', 'edit' ),
							'arg_options' => array(
								'sanitize_callback' => 'esc_url_raw',
							),
						),
					),
				),
				'sales_tax'      => array(
					'description' => __( 'Sales tax of the item.', 'wp-ever-accounting' ),
					'type'        => 'floatval',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => false,
				),
				'purchase_tax'   => array(
					'description' => __( 'Purchase tax of the item.', 'wp-ever-accounting' ),
					'type'        => 'floatval',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => false,
				),
				'enabled'        => array(
					'description' => __( 'Status of the item.', 'wp-ever-accounting' ),
					'type'        => 'boolean',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'creator'        => array(
					'description' => __( 'Creator of the item', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'properties'  => array(
						'id'    => array(
							'description' => __( 'Creator ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'name'  => array(
							'description' => __( 'Creator name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'email' => array(
							'description' => __( 'Creator Email.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
				'date_created'   => array(
					'description' => __( 'Created date of the item.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),

			),
		);

		/**
		 * Filters the item's schema.
		 *
		 * @param array $schema Item schema data.
		 *
		 * @since 1.2.1
		 */
		$schema = apply_filters( 'eaccounting_rest_item_item_schema', $schema );

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Retrieves the query params for the items collection.
	 *
	 * @return array Collection parameters.
	 * @since 1.1.2
	 */
	public function get_collection_params() {
		$params = array(
			'context'  => $this->get_context_param(),
			'paged'    => array(
				'description'       => __( 'Current page of the collection.', 'wp-ever-accounting' ),
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
				'minimum'           => 1,
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of items to be returned in result set.', 'wp-ever-accounting' ),
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'search'   => array(
				'description'       => __( 'Limit results to those matching a string.', 'wp-ever-accounting' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'include'  => array(
				'description'       => __( 'Limit result set to specific ids.', 'wp-ever-accounting' ),
				'type'              => 'array',
				'items'             => array( 'type' => 'integer' ),
				'default'           => array(),
				'sanitize_callback' => 'wp_parse_id_list',
			),
			'order'    => array(
				'description'       => __( 'Order sort attribute ascending or descending.', 'wp-ever-accounting' ),
				'type'              => 'string',
				'default'           => 'desc',
				'enum'              => array( 'asc', 'desc' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
			'orderby'  => array(
				'description'       => __( 'Sort collection by object attribute.', 'wp-ever-accounting' ),
				'type'              => 'string',
				'default'           => 'date_created',
				'enum'              => array( 'name', 'sale_price', 'purchase_price', 'category_id', 'date_created', 'enabled' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
			'offset'   => array(
				'description'       => __( 'Offset the result set by a specific number of items.', 'wp-ever-accounting' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);

		/**
		 * Filter collection parameters for the item controller.
		 *
		 * @param array $query_params JSON Schema-formatted collection parameters.
		 *
		 * @since 1.2.1
		 */
		return apply_filters( 'eaccounting_rest_item_collection_params', $params );
	}
}
