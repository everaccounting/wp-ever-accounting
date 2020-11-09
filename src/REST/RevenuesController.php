<?php

/**
 * Transactions Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

defined( 'ABSPATH' ) || die();

/**
 * Class RevenuesController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\REST
 */
class RevenuesController extends TransactionsController {
	/**
	 * Route base.
	 *
	 * @var string
	 *
	 * @since   1.1.0
	 */
	protected $rest_base = 'revenues';

	/**
	 * Check whether a given request has permission to read revenues.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 *
	 * @since   1.1.0
	 */
	public function get_items_permissions_check( $request ) {
		return true; //current_user_can( 'manage_transfers' );
	}

	/**
	 * Check if a given request has access create revenues.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 *
	 * @since   1.1.0
	 */
	public function create_item_permissions_check( $request ) {
		return true; //current_user_can( 'manage_transfers' );
	}

	/**
	 * Check if a given request has access to read a revenue.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 *
	 * @since   1.1.0
	 */
	public function get_item_permissions_check( $request ) {
		return true; //current_user_can( 'manage_transfers' );
	}

	/**
	 * Check if a given request has access update a revenue.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 *
	 * @since   1.1.0
	 */
	public function update_item_permissions_check( $request ) {
		return true; //current_user_can( 'manage_transfers' );
	}

	/**
	 * Check if a given request has access delete a revenue.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 *
	 * @since   1.1.0
	 */
	public function delete_item_permissions_check( $request ) {
		return true; //current_user_can( 'manage_transfers' );
	}

	/**
	 * Check if a given request has access batch create, update and delete items.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 *
	 * @since   1.1.0
	 */
	public function batch_items_permissions_check( $request ) {
		return true; //current_user_can( 'manage_transfers' );
	}

	/**
	 * Get all revenues.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 *
	 * @since   1.1.0
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

		$results  = \EverAccounting\Revenues\query( $args )->get_results( OBJECT, '\EverAccounting\Revenues\get' );
		$total    = \EverAccounting\Revenues\query( $args )->count();
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
	 * Create a revenue
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

		$item_id = \EverAccounting\Revenues\insert( (array) $prepared );
		if ( is_wp_error( $item_id ) ) {
			return $item_id;
		}

		$payment = \EverAccounting\Payments\get( $item_id );

		$request->set_param( 'context', 'view' );

		$response = $this->prepare_item_for_response( $payment, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );

		return $response;
	}


	/**
	 *
	 * Get a revenue
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
		$item = \EverAccounting\Revenues\get( $item_id );
		if ( is_null( $item ) ) {
			return new \WP_Error( 'rest_invalid_item_id', __( 'Could not find the revenue', 'wp-ever-accounting' ) );
		}

		$response = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $response );
	}

	/**
	 *
	 * Update a revenue
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
		$item    = \EverAccounting\Revenues\get( $item_id );
		if ( is_null( $item ) ) {
			return new \WP_Error( 'rest_invalid_item_id', __( 'Could not find the revenue', 'wp-ever-accounting' ) );
		}
		$prepared_args       = $this->prepare_item_for_database( $request );
		$prepared_args['id'] = $item_id;

		if ( ! empty( $prepared_args ) ) {
			$updated = \EverAccounting\Revenues\insert( (array) $prepared_args );

			if ( is_wp_error( $updated ) ) {
				return $updated;
			}
		}

		$request->set_param( 'context', 'view' );
		$item     = \EverAccounting\Revenues\get( $item_id );
		$response = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Delete a revenue
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 *
	 * @since 1.1.0
	 */
	public function delete_item( $request ) {
		$item_id = intval( $request['id'] );
		$item    = \EverAccounting\Revenues\get( $item_id );
		if ( is_null( $item ) ) {
			return new \WP_Error( 'rest_invalid_item_id', __( 'Could not find the revenue', 'wp-ever-accounting' ) );
		}

		$request->set_param( 'context', 'view' );

		$previous = $this->prepare_item_for_response( $item, $request );
		$retval   = \EverAccounting\Revenues\delete( $item_id );
		if ( ! $retval ) {
			return new \WP_Error( 'rest_cannot_delete', __( 'This revenue cannot be revenue.', 'wp-ever-accounting' ), array( 'status' => 500 ) );
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

}
