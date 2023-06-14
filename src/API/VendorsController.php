<?php

namespace EverAccounting\API;

use EverAccounting\Models\Vendor;

defined( 'ABSPATH' ) || exit;

/**
 * Class VendorsController
 *
 * @since 0.0.1
 * @package EverAccounting\API
 */
class VendorsController extends Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'vendors';

	/**
	 * Checks if a given request has access to read vendors.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_vendor' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view vendors.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to create a vendor.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_vendor' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to create vendors.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to read a vendor.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$vendor = eac_get_vendor( $request['id'] );

		if ( empty( $vendor ) || ! current_user_can( 'eac_manage_vendor' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view this vendor.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to update a vendor.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$vendor = eac_get_vendor( $request['id'] );

		if ( empty( $vendor ) || ! current_user_can( 'eac_manage_vendor' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to update this vendor.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to delete a vendor.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$vendor = eac_get_vendor( $request['id'] );

		if ( empty( $vendor ) || ! current_user_can( 'eac_manage_vendor' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to delete this vendor.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a list of vendors.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
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
		 * Enables adding extra arguments or setting defaults for a vendor request.
		 *
		 * @param array            $args Key value array of query var to query value.
		 * @param \WP_REST_Request $request The request used.
		 *
		 * @since 1.2.1
		 */
		$args = apply_filters( 'ever_accounting_rest_vendor_query', $args, $request );

		$vendors = eac_get_vendors( $args );
		$total      = eac_get_vendors( $args, true );
		$page       = isset( $request['page'] ) ? absint( $request['page'] ) : 1;
		$max_pages  = ceil( $total / (int) $args['number'] );

		// If requesting page is greater than max pages, return empty array.
		if ( $page > $max_pages ) {
			return new \WP_Error(
				'rest_account_invalid_page_number',
				__( 'The page number requested is larger than the number of pages available.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$results = array();
		foreach ( $vendors as $vendor ) {
			$data      = $this->prepare_item_for_response( $vendor, $request );
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
	 * Retrieves a single vendor.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$vendor = eac_get_vendor( $request['id'] );
		$data     = $this->prepare_item_for_response( $vendor, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Creates a single vendor.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_account_exists',
				__( 'Cannot create existing vendor.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$data = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$vendor = eac_insert_vendor( $data );
		if ( is_wp_error( $vendor ) ) {
			return $vendor;
		}

		$response = $this->prepare_item_for_response( $vendor, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $vendor->get_id() ) ) );

		return $response;

	}

	/**
	 * Updates a single vendor.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$vendor = eac_get_vendor( $request['id'] );
		$data     = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$vendor = eac_insert_vendor( $vendor->get_id(), $data );
		if ( is_wp_error( $vendor ) ) {
			return $vendor;
		}

		$response = $this->prepare_item_for_response( $vendor, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Deletes a single vendor.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		$vendor = eac_get_vendor( $request['id'] );
		$request->set_param( 'context', 'edit' );
		$data = $this->prepare_item_for_response( $vendor, $request );

		if ( ! eac_delete_vendor( $vendor->get_id() ) ) {
			return new \WP_Error(
				'rest_account_cannot_delete',
				__( 'The vendor cannot be deleted.', 'wp-ever-accounting' ),
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
	 * Prepares a single vendor output for response.
	 *
	 * @param vendor         $vendor vendor object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $vendor, $request ) {
		$data = [];

		foreach ( array_keys( $this->get_schema_properties() ) as $key ) {
			switch ( $key ) {
				case 'created_at':
				case 'updated_at':
					$value = $this->prepare_date_response( $vendor->$key );
					break;
				default:
					$value = $vendor->$key;
					break;
			}

			$data[ $key ] = $value;
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $vendor, $request ) );

		/**
		 * Filter vendor data returned from the REST API.
		 *
		 * @param \WP_REST_Response $response The response object.
		 * @param vendor          $vendor vendor object used to create response.
		 * @param \WP_REST_Request  $request Request object.
		 */
		return apply_filters( 'ever_accounting_rest_prepare_vendor', $response, $vendor, $request );
	}

	/**
	 * Prepares a single vendor for create or update.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.2.1
	 * @return array|\WP_Error vendor object or WP_Error.
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
		 * Filters vendor before it is inserted via the REST API.
		 *
		 * @param array            $props vendor props.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'ever_accounting_rest_pre_insert_vendor', $props, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param vendor         $vendor Object data.
	 * @param \WP_REST_Request $request Request vendor.
	 *
	 * @return array Links for the given vendor.
	 */
	protected function prepare_links( $vendor, $request ) {
		return array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $vendor->get_id() ) ),
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
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'vendor', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'          => array(
					'description' => __( 'Unique identifier for the vendor.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'name'        => array(
					'description' => __( 'vendor name.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'description' => array(
					'description' => __( 'vendor description.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'type'        => array(
					'description' => __( 'vendor type.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'enum'        => array_keys( eac_get_vendor_types() ),
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'updated_at'  => array(
					'description' => __( "The date the vendor was last updated, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'created_at'  => array(
					'description' => __( "The date the vendor was created, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		/**
		 * Filters the vendor's schema.
		 *
		 * @param array $schema Item schema data.
		 *
		 * @since 1.2.1
		 */
		$schema = apply_filters( 'ever_accounting_rest_vendor_item_schema', $schema );

		return $this->add_additional_fields_schema( $schema );
	}

}
