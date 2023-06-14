<?php

namespace EverAccounting\API;

defined( 'ABSPATH' ) || exit();

/**
 * CurrenciesController.php
 *
 * @since     1.1.6
 * @subpackage EverAccounting\API
 * @package   EverAccounting
 */
class CurrenciesController extends Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'currencies';

	/**
	 * Checks if a given request has access to read currencies.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
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
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
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
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$currency = eac_get_currency( $request['id'] );

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
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$currency = eac_get_currency( $request['id'] );

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
	 * @since 1.2.1
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$currency = eac_get_currency( $request['id'] );

		if ( empty( $currency ) || ! current_user_can( 'eac_manage_currency' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to delete this currency.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}
}
