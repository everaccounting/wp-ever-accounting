<?php
/**
 * Vendors Rest Controller Class.
 *
 * @since       1.1.2
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Vendor;
use EverAccounting\Contacts;

defined( 'ABSPATH' ) || die();

/**
 * Class REST_Vendors_Controller
 *
 * @since   1.1.2
 *
 * @package EverAccounting\REST
 */
class REST_Vendors_Controller extends REST_Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'vendors';

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 1.1.4
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
	 * @since 1.1.4
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'ea_manage_vendor' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view the items.', 'wp-ever-accounting' ),
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

		$args  = $request->get_params();
		$items = Contacts::get_vendors( $args );
		$total = Contacts::get_vendors( $args, true );

		$results = array();
		foreach ( $items as $item ) {
			$data      = $this->prepare_item_for_response( $item, $request );
			$results[] = $this->prepare_response_for_collection( $data );
		}

		$page      = (int) $args['paged'];
		$max_pages = ceil( $total / (int) $args['per_page'] );

		if ( $page > $max_pages && $total > 0 ) {
			return new \WP_Error(
				'rest_contact_invalid_page_number',
				__( 'The page number requested is larger than the number of pages available.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
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
	 * Checks if a given request has access to read the resource.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return true|\WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$resource = Contacts::get_vendor( $request['id'] );
		if ( empty( $resource ) ) {
			return new \WP_Error(
				'rest_invalid_id',
				__( 'Invalid ID.', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( 'ea_manage_vendor' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to edit the item.', 'wp-ever-accounting' ),
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
	 * @since 1.1.4
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$item = Contacts::get_vendor( $request['id'] );
		if ( empty( $item ) ) {
			return new \WP_Error(
				'rest_invalid_id',
				__( 'Invalid ID.', 'wp-ever-accounting' ),
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
	 * @since 1.1.4
	 * @return true|\WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_item_exists',
				__( 'Cannot create existing item.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		if ( ! current_user_can( 'ea_manage_vendor' ) ) {
			return new \WP_Error(
				'rest_cannot_create',
				__( 'Sorry, you are not allowed to create item.', 'wp-ever-accounting' ),
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
	 * @since 1.1.4
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_vendor_exists',
				__( 'Cannot create existing vendor.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$props = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $props ) ) {
			return $props;
		}

		$item = Contacts::insert_vendor( wp_slash( (array) $props ) );

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
	 * Checks if a given request has access to update an item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return true|\WP_Error True if the request has access to update the item, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$vendor = Contacts::get_vendor( $request['id'] );
		if ( empty( $vendor ) ) {
			return new \WP_Error(
				'rest_invalid_id',
				__( 'Invalid ID.', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( 'ea_manage_vendor' ) ) {
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
	 * @since 1.1.4
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$item  = Contacts::get_vendor( $request['id'] );
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
	 * Checks if a given request has access to delete an item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.4
	 * @return true|\WP_Error True if the request has access to delete the item, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$item = Contacts::get_vendor( $request['id'] );
		if ( empty( $item ) ) {
			return new \WP_Error(
				'rest_invalid_id',
				__( 'Invalid ID.', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( 'ea_manage_vendor' ) ) {
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
	 * @since 1.1.4
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		$contact = Contacts::get_vendor( $request['id'] );
		$request->set_param( 'context', 'edit' );
		$data = $this->prepare_item_for_response( $contact, $request );

		if ( ! $contact->delete() ) {
			return new \WP_Error( 'rest_cannot_delete', __( 'The vendor cannot be deleted.', 'wp-ever-accounting' ), array( 'status' => 500 ) );
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
	 * @param Vendor $item Vendor object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.1.4
	 * @return \WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data        = $item->get_data();
		$format_date = array( 'date_created', 'birth_date' );
		// Format date values.
		foreach ( $format_date as $key ) {
			$data[ $key ] = $this->prepare_date_response( $data[ $key ] );
		}
		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $item, $request ) );

		return $response;
	}

	/**
	 * Prepares a single item for create or update.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.1.4
	 * @return array|\WP_Error Item object or WP_Error.
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

		return $props;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param Object $item Object data.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.1.4
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
	 * Retrieves the items's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 * @since 1.1.2
	 *
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Venor', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'           => array(
					'description' => __( 'Unique identifier for the vendor.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'user_id'      => array(
					'description' => __( 'WP user ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
					'required'    => true,
				),
				'name'         => array(
					'description' => __( 'Name for the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'default'     => '',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'company'      => array(
					'description' => __( 'Company for the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'default'     => '',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => false,
				),
				'email'        => array(
					'description' => __( 'The email address for the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_email',
					),
				),
				'phone'        => array(
					'description' => __( 'Phone number for the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'website'      => array(
					'description' => __( 'website of the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'esc_url_raw',
					),
				),
				'birth_date'   => array(
					'description' => __( 'Birth date', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'vat_number'   => array(
					'description' => __( 'Vat number of the vendor', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'street'       => array(
					'description' => __( 'Street Address of the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'city'       => array(
					'description' => __( 'City of the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'state'        => array(
					'description' => __( 'State Address of the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'postcode'     => array(
					'description' => __( 'Postcode of the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'country'      => array(
					'description' => __( 'Country of the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'currency_code'     => array(
					'description' => __( 'Currency code for vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => true,
//					'properties'  => array(
//						'code' => array(
//							'description' => __( 'Currency code', 'wp-ever-accounting' ),
//							'type'        => 'string',
//							'context'     => array( 'embed', 'view', 'edit' ),
//							'enum'        => array_keys( eaccounting_get_global_currencies() ),
//							'arg_options' => array(
//								'sanitize_callback' => 'sanitize_text_field',
//							),
//						),
//					),
				),
				'thumbnail_id'    => array(
					'description' => __( 'Thumbnail of the vendor', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
//					'properties'  => array(
//						'id'  => array(
//							'description' => __( 'Thumbnail ID.', 'wp-ever-accounting' ),
//							'type'        => 'integer',
//							'context'     => array( 'embed', 'view', 'edit' ),
//							'arg_options' => array(
//								'sanitize_callback' => 'absint',
//							),
//						),
//						'src' => array(
//							'description' => __( 'Thumbnail src.', 'wp-ever-accounting' ),
//							'type'        => 'string',
//							'context'     => array( 'embed', 'view' ),
//							'arg_options' => array(
//								'sanitize_callback' => 'esc_url_raw',
//							),
//						),
//					),
				),
				'enabled'      => array(
					'description' => __( 'Status of the vendor.', 'wp-ever-accounting' ),
					'type'        => 'boolean',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'creator_id'      => array(
					'description' => __( 'Creator of the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
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
				),
				'date_created' => array(
					'description' => __( 'Created date of the vendor.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
