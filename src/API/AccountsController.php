<?php

namespace EverAccounting\API;

use EverAccounting\Models\Account;
use EverAccounting\Models\Item;

defined( 'ABSPATH' ) || exit;

/**
 * Class AccountsController
 *
 * @since   1.6.1
 * @package EverAccounting
 */
class AccountsController extends Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'accounts';

	/**
	 * Checks if a given request has access to read accounts.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_account' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view accounts.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to create a account.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_account' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to create accounts.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to read a account.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function get_item_permissions_check( $request ) {
		$account = eac_get_account( $request['id'] );

		if ( empty( $account ) || ! current_user_can( 'eac_manage_account' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view this account.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to update a account.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function update_item_permissions_check( $request ) {
		$account = eac_get_account( $request['id'] );

		if ( empty( $account ) || ! current_user_can( 'eac_manage_account' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to update this account.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to delete a account.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 1.2.1
	 */
	public function delete_item_permissions_check( $request ) {
		$account = eac_get_account( $request['id'] );

		if ( empty( $account ) || ! current_user_can( 'eac_manage_account' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to delete this account.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a list of accounts.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function get_items( $request ) {
		$params = $this->get_collection_params();
		$args   = array();
		foreach ( $params as $key => $value ) {
			if ( isset( $request[ $key ] ) ) {
				$args[ $key ] = $request[ $key ];
			}
		}
		foreach ( ( new Item() )->get_core_data_keys() as $key ) {
			if ( isset( $request[ $key ] ) ) {
				$args[ $key ] = $request[ $key ];
			}
		}

		/**
		 * Filters the query arguments for a request.
		 *
		 * Enables adding extra arguments or setting defaults for a account request.
		 *
		 * @param array $args Key value array of query var to query value.
		 * @param \WP_REST_Request $request The request used.
		 *
		 * @since 1.2.1
		 */
		$args = apply_filters( 'ever_accounting_rest_account_query', $args, $request );

		$accounts  = eac_get_accounts( $args );
		$total     = eac_get_accounts( $args, true );
		$page      = isset( $request['page'] ) ? absint( $request['page'] ) : 1;
		$max_pages = ceil( $total / (int) $args['per_page'] );

		// If requesting page is greater than max pages, return empty array.
		if ( $page > $max_pages ) {
			return new \WP_Error(
				'rest_account_invalid_page_number',
				__( 'The page number requested is larger than the number of pages available.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$results = array();
		foreach ( $accounts as $account ) {
			$data      = $this->prepare_item_for_response( $account, $request );
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
	 * Retrieves a single account.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function get_item( $request ) {
		$account = eac_get_account( $request['id'] );
		$data    = $this->prepare_item_for_response( $account, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Creates a single account.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_exists',
				__( 'Cannot create existing account.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$data = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$account = eac_insert_account( $data );
		if ( is_wp_error( $account ) ) {
			return $account;
		}

		$response = $this->prepare_item_for_response( $account, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $account->get_id() ) ) );

		return $response;

	}

	/**
	 * Updates a single account.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function update_item( $request ) {
		$account = eac_get_account( $request['id'] );
		$data    = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		$response = $account->set_data( $data )->save();
		if ( is_wp_error( $account ) ) {
			return $response;
		}

		$response = $this->prepare_item_for_response( $account, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Deletes a single account.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function delete_item( $request ) {
		$account = eac_get_account( $request['id'] );
		$request->set_param( 'context', 'edit' );
		$data = $this->prepare_item_for_response( $account, $request );

		if ( ! eac_delete_account( $account->get_id() ) ) {
			return new \WP_Error(
				'rest_account_cannot_delete',
				__( 'The account cannot be deleted.', 'wp-ever-accounting' ),
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
	 * Prepares a single account output for response.
	 *
	 * @param Account $account Account object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 1.2.1
	 */
	public function prepare_item_for_response( $account, $request ) {
		$data = [];

		foreach ( array_keys( $this->get_schema_properties() ) as $key ) {
			switch ( $key ) {
				case 'date_created':
				case 'date_updated':
					$value = $this->prepare_date_response( $account->$key );
					break;
				default:
					$value = $account->$key;
					break;
			}

			$data[ $key ] = $value;
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $account, $request ) );

		/**
		 * Filter account data returned from the REST API.
		 *
		 * @param \WP_REST_Response $response The response object.
		 * @param Account $account Account object used to create response.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'ever_accounting_rest_prepare_account', $response, $account, $request );
	}

	/**
	 * Prepares a single account for create or update.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array|\WP_Error Account object or WP_Error.
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
		 * Filters account before it is inserted via the REST API.
		 *
		 * @param array $props Account props.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'ever_accounting_rest_pre_insert_account', $props, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param Account $account Object data.
	 * @param \WP_REST_Request $request Request account.
	 *
	 * @return array Links for the given account.
	 */
	protected function prepare_links( $account, $request ) {
		return array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $account->get_id() ) ),
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
			'title'      => __( 'Account', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'              => array(
					'description' => __( 'Unique identifier for the account.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'name'            => array(
					'description' => __( 'Name of the account.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'type'            => array(
					'description' => __( 'Type of the account.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'enum'        => array_keys( eac_get_account_types() ),
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'number'          => array(
					'description' => __( 'Number of the account.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'opening_balance' => array(
					'description' => __( 'Opening balance of the account', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'currency_code'   => array(
					'description' => __( 'Currency code of the account', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => eac_get_base_currency(),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'currency'        => array(
					'description' => __( 'Currency code of the account', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Currency code ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'code' => array(
							'description' => __( 'Currency code.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),

					),
				),
				'bank_name'       => array(
					'description' => __( 'Bank name of the account', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'bank_phone'      => array(
					'description' => __( 'Phone number of the bank', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'bank_address'    => array(
					'description' => __( 'Address of the bank', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
				),
				'status'          => array(
					'description' => __( 'Status of the account', 'wp-ever-accounting' ),
					'type'        => 'string',
					'enum'        => array( 'active', 'inactive' ),
					'context'     => array( 'embed', 'view', 'edit' ),
					'default'     => 'active',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'creator'         => array(
					'description' => __( 'Creator of the account', 'wp-ever-accounting' ),
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
				'date_updated'    => array(
					'description' => __( "The date the account was last updated, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created'    => array(
					'description' => __( "The date the account was created, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		/**
		 * Filters the account's schema.
		 *
		 * @param array $schema Item schema data.
		 *
		 * @since 1.2.1
		 */
		$schema = apply_filters( 'ever_accounting_rest_account_item_schema', $schema );

		return $this->add_additional_fields_schema( $schema );
	}
}
