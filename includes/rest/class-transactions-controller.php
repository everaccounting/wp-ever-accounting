<?php

namespace EverAccounting\REST;

use EverAccounting\Abstracts\Controller;
use EverAccounting\Transaction;

defined( 'ABSPATH' ) || die();

class Transactions_Controller extends Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 *
	 */
	protected $rest_base = 'transactions';

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
						'description' => __( 'Unique identifier for the entity.', 'wp-ever-accounting' ),
						'type'        => 'integer',
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
	 * Checks if a given request has access to read transactions.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool | \WP_Error True if the request has read access, WP_Error object otherwise.
	 * @since 4.7.0
	 *
	 */
	public function get_items_permissions_check( $request ) {

//		if ( ! current_user_can( "ea_manage_transaction" ) ) {
//			return new \WP_Error(
//				'rest_forbidden_context',
//				__( 'Sorry, you are not allowed to edit transactions.', 'wp-ever-accounting' ),
//				array( 'status' => rest_authorization_required_code() )
//			);
//		}

		return true;
	}

	/**
	 * Retrieves a collection of transactions.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return\ WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 * @since 4.7.0
	 *
	 */
	public function get_items( $request ) {
		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();
		$args       = array();

		$parameter_mappings = array(
			'order'   => 'order',
			'orderby' => 'orderby',
			'page'    => 'paged',
			'search'  => 'search',
		);

		/*
		 * For each known parameter which is both registered and present in the request,
		 * set the parameter's value on the query $args.
		 */
		foreach ( $parameter_mappings as $api_param => $wp_param ) {
			if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
				$args[ $wp_param ] = $request[ $api_param ];
			}
		}

		$args       = apply_filters( "eaccounting_rest_transactions_query", $args, $request );
		$query_args = $this->prepare_items_query( $args, $request );
		$results    = eaccounting_get_transactions( $query_args );
		$total      = (int) eaccounting_get_transactions( array_merge( $query_args, array( 'count_total' => true ) ) );
		if ( is_wp_error( $results ) || is_wp_error( $total ) ) {
			return $results;
		}

		$items = array();
		foreach ( $results as $result ) {
			$data    = $this->prepare_item_for_response( $result, $request );
			$items[] = $this->prepare_response_for_collection( $data );
		}

		$max_pages = ceil( $total / (int) $args['per_page'] );
		$response  = rest_ensure_response( $items );
		$response->header( 'X-WP-Total', (int) $total );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		return $response;
	}

	/**
	 * Checks if a given request has access to read a transaction.
	 *
	 * @since 4.7.0
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return true|\WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$transaction = $this->get_transaction( $request['id'] );
		if ( is_wp_error( $transaction ) ) {
			return $transaction;
		}

		return true;
	}



	public function get_transaction($id){
		$error = new \WP_Error(
			'rest_transaction_invalid_id',
			__( 'Invalid Transaction ID.' ),
			array( 'status' => 404 )
		);

		if ( (int) $id <= 0 ) {
			return $error;
		}

		$transaction = eaccounting_get_transaction( (int) $id );
		if ( empty( $transaction ) || empty( $transaction->id ) ) {
			return $error;
		}

		return $transaction;
	}

	/**
	 * Retrieves a single transaction.
	 *
	 * @since 1.2.1
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$transaction = $this->get_transaction( $request['id'] );
		if ( is_wp_error( $transaction ) ) {
			return $transaction;
		}

		$data     = $this->prepare_item_for_response( $transaction, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Determines the allowed query_vars for a get_items() response and prepares
	 * them for WP_Query.
	 *
	 * @param array $prepared_args Optional. Prepared WP_Query arguments. Default empty array.
	 * @param \WP_REST_Request $request Optional. Full details about the request.
	 *
	 * @return array Items query arguments.
	 * @since 4.7.0
	 *
	 */
	protected function prepare_items_query( $prepared_args = array(), $request = null ) {
		$query_args = array();

		foreach ( $prepared_args as $key => $value ) {
			/**
			 * Filters the query_vars used in get_items() for the constructed query.
			 *
			 * The dynamic portion of the hook name, `$key`, refers to the query_var key.
			 *
			 * @param string $value The query_var value.
			 *
			 * @since 4.7.0
			 *
			 */
			$query_args[ $key ] = apply_filters( "eaccounting_rest_query_var-{$key}", $value ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		}

		return $query_args;
	}

	/**
	 * Prepares a single post output for response.
	 *
	 * @param Transaction $transaction Post object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response Response object.
	 * @since 4.7.0
	 *
	 */
	public function prepare_item_for_response( $transaction, $request ) {
		$fields = $this->get_fields_for_response( $request );
		// Base fields for every transaction.
		$data = array();
		foreach ( $fields as $field ) {
			if ( property_exists($transaction, $field) ) {
				$data[ $field ] = $transaction->$field;
			}
		}

		$date_fields = ['payment_date', 'date_created'];
		foreach ($date_fields as $date_field){
			if( !empty( $data[$date_field])){
				$data[$date_field]	= $this->prepare_date_response( $data[$date_field] );
			}
		}


		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );
		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$links = $this->prepare_links( $transaction, $request );
		$response->add_links( $links );

		return apply_filters( "eaccounting_rest_prepare_transaction", $response, $transaction, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param Transaction $object Object data.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array                   Links for the given post.
	 */
	protected function prepare_links( $object, $request ) {
		return array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $object->id ) ),
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
	 *
	 * @since   1.1.2
	 *
	 */
	public function get_item_schema() {

		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

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
						'sanitize_callback' => 'absint',
					),
				),
				'payment_date'   => array(
					'description' => __( 'Payment Date of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'amount'         => array(
					'description' => __( 'Amount of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'currency_code'  => array(
					'description' => __( 'Currency code of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
				),
				'currency_rate'  => array(
					'description' => __( 'Currency rate of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'double',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'doubleval',
					),
					'readonly'    => true,
				),
				'account_id'        => array(
					'description' => __( 'Account id of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => true,
				),
				'document_id'    => array(
					'description' => __( 'Invoice id of the transaction', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'contact_id'    => array(
					'description' => __( 'Contact id of the transaction', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'category_id'       => array(
					'description' => __( 'Category ID of the transaction', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'description'    => array(
					'description' => __( 'Description of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
				),
				'payment_method' => array(
					'description' => __( 'Method of the payment', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'enum'        => array_keys( eaccounting_get_payment_methods() ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'reference'      => array(
					'description' => __( 'Reference of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'attachment_id'     => array(
					'description' => __( 'Attachment ID', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'reconciled'     => array(
					'description' => __( 'Reconciliation of the transaction', 'wp-ever-accounting' ),
					'type'        => 'boolean',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'creator_id'        => array(
					'description' => __( 'Creator of the transactions', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
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
			'properties'  => array(
				'id'   => array(
					'description' => __( 'Contact ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					"arg_options" => array(
						'sanitize_callback' => 'intval',
					)
				),
				'name' => array(
					'description' => __( 'Contact name.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					"arg_options" => array(
						'sanitize_callback' => 'sanitize_text_field',
					)
				),

			),
		);

		// check rest base for the contacts and push proper contacts to the schema
		if ( 'payments' === $this->rest_base ) {
			$schema['properties']['vendor'] = $contact;
		} elseif ( 'revenues' === $this->rest_base ) {
			$schema['properties']['customer'] = $contact;
		}

		return $this->add_additional_fields_schema( $schema );
	}


	/**
	 * Retrieves the query params for the items collection.
	 *
	 * @return array Collection parameters.
	 *
	 * @since   1.1.2
	 *
	 */
	public function get_collection_params() {
		$query_params = array_merge(
			parent::get_collection_params(),
			array(
				'orderby'    => array(
					'description' => __( 'Sort collection by transaction attribute.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'default'     => 'payment_date',
					'enum'        => array(
						'payment_date',
						'amount',
						'account_id',
						'type',
						'category_id',
						'contact_id',
						'reference',
					),
				),
				'account_id' => array(
					'description'       => __( 'Limit results to those matching accounts.', 'wp-ever-accounting' ),
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => 'rest_validate_request_arg',
				),
			)
		);

		return apply_filters( "eaccounting_rest_transactions_collection_params", $query_params );
	}
}
