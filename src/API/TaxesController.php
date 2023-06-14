<?php

namespace EverAccounting\API;

defined( 'ABSPATH' ) || exit;

/**
 * Class PaymentsController
 *
 * @since 0.0.1
 * @package EverAccounting\API
 */
class TaxesController extends Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'taxes';

	/**
	 * Checks if a given request has access to read taxes.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_tax' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view taxes.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to create a tax.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_tax' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to create taxes.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to read a tax.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$tax = eac_get_tax( $request['id'] );

		if ( empty( $tax ) || ! current_user_can( 'eac_manage_tax' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view this tax.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to update a tax.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$tax = eac_get_tax( $request['id'] );

		if ( empty( $tax ) || ! current_user_can( 'eac_manage_tax' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to update this tax.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to delete a tax.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$tax = eac_get_tax( $request['id'] );

		if ( empty( $tax ) || ! current_user_can( 'eac_manage_tax' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to delete this tax.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a list of categories.
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
		 * Enables adding extra arguments or setting defaults for a category request.
		 *
		 * @param array            $args Key value array of query var to query value.
		 * @param \WP_REST_Request $request The request used.
		 *
		 * @since 1.2.1
		 */
		$args = apply_filters( 'ever_accounting_rest_category_query', $args, $request );

		$categories = eac_get_categories( $args );
		$total      = eac_get_categories( $args, true );
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
		foreach ( $categories as $category ) {
			$data      = $this->prepare_item_for_response( $category, $request );
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
	 * Retrieves a single category.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$category = eac_get_category( $request['id'] );
		$data     = $this->prepare_item_for_response( $category, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Creates a single category.
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
				__( 'Cannot create existing category.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$data = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$category = eac_insert_category( $data );
		if ( is_wp_error( $category ) ) {
			return $category;
		}

		$response = $this->prepare_item_for_response( $category, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $category->get_id() ) ) );

		return $response;

	}

	/**
	 * Updates a single category.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$category = eac_get_category( $request['id'] );
		$data     = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$category = eac_insert_category( $category->get_id(), $data );
		if ( is_wp_error( $category ) ) {
			return $category;
		}

		$response = $this->prepare_item_for_response( $category, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Deletes a single category.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		$category = eac_get_category( $request['id'] );
		$request->set_param( 'context', 'edit' );
		$data = $this->prepare_item_for_response( $category, $request );

		if ( ! eac_delete_category( $category->get_id() ) ) {
			return new \WP_Error(
				'rest_account_cannot_delete',
				__( 'The category cannot be deleted.', 'wp-ever-accounting' ),
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

}
