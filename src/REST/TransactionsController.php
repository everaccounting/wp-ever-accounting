<?php
/**
 * Transaction Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

defined( 'ABSPATH' ) || exit();

/**
 * Class TransactionController
 *
 * @package EverAccounting\REST
 *
 * @since   1.1.0
 */
class TransactionsController extends Controller {
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
						'description' => __( 'Unique identifier for the transaction.', 'wp-ever-accounting' ),
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
	 * Check whether a given request has permission to read transactions.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 *
	 * @since   1.1.0
	 */
	public function get_items_permissions_check( $request ) {
		return true; //current_user_can( 'manage_transactions' );
	}

	/**
	 * Check if a given request has access create transactions.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 *
	 * @since   1.1.0
	 */
	public function create_item_permissions_check( $request ) {
		return true; //current_user_can( 'manage_transactions' );
	}

	/**
	 * Check if a given request has access to read a transaction.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 *
	 * @since   1.1.0
	 */
	public function get_item_permissions_check( $request ) {
		return true; //current_user_can( 'manage_transactions' );
	}

	/**
	 * Check if a given request has access update a manage_transaction.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 *
	 * @since   1.1.0
	 */
	public function update_item_permissions_check( $request ) {
		return true; //current_user_can( 'manage_transactions' );
	}

	/**
	 * Check if a given request has access delete a manage_transaction.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 *
	 * @since   1.1.0
	 */
	public function delete_item_permissions_check( $request ) {
		return true; //current_user_can( 'manage_transactions' );
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
		return true; //current_user_can( 'manage_transaction' );
	}

	/**
	 * Get all transactions.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 *
	 * @since   1.1.0
	 */
	public function get_items( $request ) {
		$args = array(
			'include'  => $request['include'],
			'exclude'  => $request['exclude'],
			'search'   => $request['search'],
			'orderby'  => $request['orderby'],
			'order'    => $request['order'],
			'per_page' => $request['per_page'],
			'page'     => $request['page'],
			'offset'   => $request['offset'],
		);

		$results  = \EverAccounting\Transactions\query( $args )->get_results( OBJECT, '\EverAccounting\Transactions\get' );
		$total    = \EverAccounting\Transactions\query( $args )->count();
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
	 * Create a transaction
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return int|mixed|\WP_Error|\WP_REST_Response|null
	 *
	 * @since   1.1.0
	 *
	 *
	 */
	public function create_item( $request ) {
		$request->set_param( 'context', 'edit' );
		$prepared = $this->prepare_item_for_database( $request );

		$item_id = \EverAccounting\Transactions\insert( (array) $prepared );
		if ( is_wp_error( $item_id ) ) {
			return $item_id;
		}

		$transactions = \EverAccounting\Transactions\get( $item_id );

		$request->set_param( 'context', 'view' );

		$response = $this->prepare_item_for_response( $transactions, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );

		return $response;
	}


	/**
	 * Get a single transaction
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 *
	 * @since   1.1.0
	 *
	 */
	public function get_item( $request ) {
		$item_id = intval( $request['id'] );
		$request->set_param( 'context', 'view' );
		$item = \EverAccounting\Transactions\get( $item_id );
		if ( is_null( $item ) ) {
			return new \WP_Error( 'rest_invalid_item_id', __( 'Could not find the transaction', 'wp-ever-accounting' ) );
		}

		$response = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Update a transaction
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return int|mixed|\WP_Error|\WP_REST_Response|null
	 *
	 * @since   1.1.0
	 *
	 */
	public function update_item( $request ) {
		$request->set_param( 'context', 'edit' );
		$item_id = intval( $request['id'] );
		$item    = \EverAccounting\Transactions\get( $item_id );
		if ( is_null( $item ) ) {
			return new \WP_Error( 'rest_invalid_item_id', __( 'Could not find the transaction', 'wp-ever-accounting' ) );
		}
		$prepared_args       = $this->prepare_item_for_database( $request );
		$prepared_args['id'] = $item_id;

		if ( ! empty( $prepared_args ) ) {
			$updated = \EverAccounting\Transactions\insert( (array) $prepared_args );

			if ( is_wp_error( $updated ) ) {
				return $updated;
			}
		}

		$request->set_param( 'context', 'view' );
		$item     = \EverAccounting\Transactions\get( $item_id );
		$response = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Delete a transaction
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 *
	 * @since   1.1.0
	 *
	 */
	public function delete_item( $request ) {
		$item_id = intval( $request['id'] );
		$item    = \EverAccounting\Transactions\get( $item_id );
		if ( is_null( $item ) ) {
			return new \WP_Error( 'rest_invalid_item_id', __( 'Could not find the transactions', 'wp-ever-accounting' ) );
		}

		$request->set_param( 'context', 'view' );

		$previous = $this->prepare_item_for_response( $item, $request );
		$retval   = \EverAccounting\Transactions\delete( $item_id );
		if ( ! $retval ) {
			return new \WP_Error( 'rest_cannot_delete', __( 'This transaction cannot be deleted.', 'wp-ever-accounting' ), array( 'status' => 500 ) );
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
	 * Prepare items for database
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return array
	 *
	 * @since   1.1.0
	 *
	 */
	public function prepare_item_for_database( $request ) {
		$schema    = $this->get_item_schema();
		$data_keys = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );
		$data      = array();
		foreach ( $data_keys as $key ) {
			$value = $request[ $key ];
			switch ( $key ) {
				case 'currency_code':
					$data[ $key ] = eaccounting_rest_get_currency_code( $value );
					break;
				case 'account':
					$data['account_id'] = eaccounting_rest_get_account_id( $value );
					break;
				case 'category':
					$data['category_id'] = eaccounting_rest_get_category_id( $value );
					break;
				case 'vendor':
					$data['contact_id'] = eaccounting_rest_get_vendor_id( $value );
					break;
				case 'customer':
					$data['contact_id'] = eaccounting_rest_get_customer_id( $value );
					break;
				case 'creator':
					$data['creator_id'] = array();
					break;
				default:
					$data[ $key ] = $value;
			}
		}

		return $data;
	}


	/**
	 * Prepare item for response
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @param \EverAccounting\Transactions\Transaction $item
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
	 *
	 * @since   1.1.0
	 *
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = array(
			'id'             => $item->get_id(),
			'paid_at'        => eaccounting_rest_date_response( $item->get_paid_at() ),
			'amount'         => array(
				'formatted' => $item->get_formatted_amount(),
				'raw'       => $item->get_amount(),
			),
			'currency_code'  => $item->get_currency_code(),
			'currency_rate'  => $item->get_currency_rate(),
			'account'        => '',
			'contact'        => '',
			'category'       => '',
			'description'    => $item->get_description(),
			'payment_method' => $item->get_payment_method(),
			'reference'      => $item->get_reference(),
			'file'           => '',
			'reconciled'     => $item->get_reconciled(),
			'date_created'   => eaccounting_rest_date_response( $item->get_date_created() ),
		);

		$key          = 'payment' == $item->get_type() ? 'vendor' : 'customer';
		$contact      = \EverAccounting\Contacts\get( $item->get_contact_id() );
		$data[ $key ] = ( $contact ) ? $contact : array();

		$category         = \EverAccounting\Categories\get( $item->get_category_id() );
		$data['category'] = ( $category ) ? $category : array();

		$account         = \EverAccounting\Accounts\get( $item->get_account_id() );
		$data['account'] = ( $account ) ? $account : array();

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $item ) );

		return $response;
	}


	/**
	 * Retrieves the items's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 *
	 * @since   1.1.0
	 *
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Transaction', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'             => array(
					'description' => __( 'Unique identifier for the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'paid_at'        => array(
					'description' => __( 'Payment Date of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'embed', 'view' ),
					'required'    => true,
				),
				'amount'         => array(
					'description' => __( 'Amount of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'required'    => true,
				),
				'currency_code'  => array(
					'description' => __( 'Currency code for transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Currency Code ID.', 'wp-ever-accounting' ),
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
				'currency_rate'  => array(
					'description' => __( 'Currency rate of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'double',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'doubleval',
					),
					'readonly'    => true,
				),
				'account'        => array(
					'description' => __( 'Account id of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
					'required'    => true,
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Account ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'name' => array(
							'description' => __( 'Account name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),

					),
				),
				'invoice_id'     => array(
					'description' => __( 'Invoice id of the transaction', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'category'       => array(
					'description' => __( 'Category of the transaction', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
					'required'    => true,
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Category ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'type' => array(
							'description' => __( 'Category Type.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
				'description'    => array(
					'description' => __( 'Description of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
				),
				'payment_method' => array(
					'description' => __( 'Method of the payment', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_key',
					),
					'required'    => true,
				),
				'reference'      => array(
					'description' => __( 'Reference of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'attachment'     => array(
					'description' => __( 'Attachment url of the transaction', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view' ),
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Attachment ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'src'  => array(
							'description' => __( 'Attachment src.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'name' => array(
							'description' => __( 'Attachment Name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
				'reconciled'     => array(
					'description' => __( 'Reconciliation of the transaction', 'wp-ever-accounting' ),
					'type'        => 'boolean',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'creator'        => array(
					'description' => __( 'Creator of the transactions', 'wp-ever-accounting' ),
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
					'description' => __( 'Created date of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			),
		);

		$contact = array(
			'description' => __( 'Contact id of the transaction', 'wp-ever-accounting' ),
			'type'        => 'object',
			'context'     => array( 'embed', 'view', 'edit' ),
			'arg_options' => array(
				'sanitize_callback' => 'sanitize_text_field',
			),
			'properties'  => array(
				'id'   => array(
					'description' => __( 'Contact ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'name' => array(
					'description' => __( 'Contact name.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),

			),
		);

		//check rest base for the contacts and push proper contacts to the schema
		if ( 'payments' == $this->rest_base ) {
			$schema['properties']['vendor'] = $contact;
		} elseif ( 'revenues' == $this->rest_base ) {
			$schema['properties']['customer'] = $contact;
		}

		return $this->add_additional_fields_schema( $schema );
	}


	/**
	 * Retrieves the query params for the items collection.
	 *
	 * @return array Collection parameters.
	 *
	 * @since   1.1.0
	 *
	 */
	public function get_collection_params() {
		$query_params                       = parent::get_collection_params();
		$query_params['context']['default'] = 'view';
		$params['orderby']                  = array(
			'description' => __( 'Sort collection by transaction attribute.', 'wp-ever-accounting' ),
			'type'        => 'string',
			'default'     => 'paid_at',
			'enum'        => array(
				'paid_at',
				'amount',
				'account_id',
				'type',
				'category_id',
				'reference'
			),
		);

		return $query_params;
	}
}
