<?php
/**
 * Bill Items Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Document_Item;
use EverAccounting\Document_Items;

defined( 'ABSPATH' ) || die();

/**
 * Class REST_Bill_Items_Controller
 * @package EverAccounting\REST
 */
class REST_Bill_Items_Controller extends REST_Controller {
	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'bill_items';

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
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check whether a given request has permission to read accounts.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'ea_manage_bill' ) ) {
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
		$items = Document_Items::get_document_items( $args );
		$total = Document_Items::get_document_items( $args, true );

		$results = array();
		foreach ( $items as $item ) {
			$data      = $this->prepare_item_for_response( $item, $request );
			$results[] = $this->prepare_response_for_collection( $data );
		}

		$page      = (int) $args['paged'];
		$max_pages = ceil( $total / (int) $args['per_page'] );

		if ( $page > $max_pages && $total > 0 ) {
			return new \WP_Error(
				'rest_bill_item_invalid_page_number',
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
		$resource = Document_Items::get_document_item( $request['id'] );
		if ( empty( $resource ) ) {
			return new \WP_Error(
				'rest_invalid_id',
				__( 'Invalid ID.', 'wp-ever-accounting' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( 'ea_manage_bill' ) ) {
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
		$item = Document_Items::get_document_item( $request['id'] );
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
	 * Prepares a single item output for response.
	 *
	 * @param Document_Item $item Document_Item object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.1.4
	 * @return \WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data        = $item->get_data();
		$format_date = array( 'date_created' );
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
	 * @since 1.0.2
	 *
	 * @return array Item schema data.
	 *
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Bill Items', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'           => array(
					'description' => __( 'Unique identifier for the bill item.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'bill_id'      => array(
					'description' => __( 'Invoice id for the bill item.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
				),
				'item_id'      => array(
					'description' => __( 'Item id for the bill item.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
				),
				'name'         => array(
					'description' => __( 'Name of the item.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
				),
				'sku'          => array(
					'description' => __( 'Sku of the item.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
				),
				'quantity'     => array(
					'description' => __( 'Purchase price of the item.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'default'     => '1',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
				),
				'price'        => array(
					'description' => __( 'Price of the item', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
				),
				'total'        => array(
					'description' => __( 'Total of the item', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
				),
				'tax_id'       => array(
					'description' => __( 'Tax id of the item.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
					'readonly'    => true,
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Tax ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'rate' => array(
							'description' => __( 'Tax Rate.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
					),
				),
				'tax_name'     => array(
					'description' => __( 'Tax name of the bill item', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
				),
				'tax_total'    => array(
					'description' => __( 'Tax name of the bill item', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
				),
				'date_created' => array(
					'description' => __( 'Created date of the bill item.', 'wp-ever-accounting' ),
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
