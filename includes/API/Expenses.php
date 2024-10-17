<?php

namespace EverAccounting\API;

use EverAccounting\Models\Expense;

defined( 'ABSPATH' ) || exit;

/**
 * Class Expenses
 *
 * @since 2.0.0
 * @package EverAccounting\API
 */
class Expenses extends Transactions {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'expenses';

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @see register_rest_route()
	 * @since 2.0.0
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
						'description' => __( 'Unique identifier for the expense.', 'wp-ever-accounting' ),
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
	 * Checks if a given request has access to read expenses.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 2.0.0
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_expense' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view expenses.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to create expense.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 2.0.0
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! current_user_can( 'eac_manage_expense' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to create expenses.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to read expense.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 2.0.0
	 * @return true|\WP_Error True, if the request has read access, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {
		$expense = EAC()->expenses->get( $request['id'] );

		if ( empty( $expense ) || ! current_user_can( 'eac_manage_expense' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to view this expense.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to update expense.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 2.0.0
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function update_item_permissions_check( $request ) {
		$expense = EAC()->expenses->get( $request['id'] );

		if ( empty( $expense ) || ! current_user_can( 'eac_manage_expense' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to update this expense.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Checks if a given request has access to delete expense.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 2.0.0
	 * @return true|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function delete_item_permissions_check( $request ) {
		$expense = EAC()->expenses->get( $request['id'] );

		if ( empty( $expense ) || ! current_user_can( 'eac_manage_expense' ) ) {
			return new \WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to delete this expense.', 'wp-ever-accounting' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a list of expenses.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 2.0.0
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$params = $this->get_collection_params();
		$args   = array();
		foreach ( $params as $key => $value ) {
			if ( isset( $request[ $key ] ) ) {
				$args[ $key ] = $request[ $key ];
			}
		}

		/**
		 * Filters the query arguments for a request.
		 *
		 * Enables adding extra arguments or setting defaults for a expense request.
		 *
		 * @param array            $args Key value array of query var to query value.
		 * @param \WP_REST_Request $request The request used.
		 *
		 * @since 2.0.0
		 */
		$args = apply_filters( 'eac_rest_expense_query', $args, $request );

		$expenses  = EAC()->expenses->query( $args );
		$total     = EAC()->expenses->query( $args, true );
		$max_pages = ceil( $total / (int) $args['per_page'] );

		$results = array();
		foreach ( $expenses as $expense ) {
			$data      = $this->prepare_item_for_response( $expense, $request );
			$results[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $results );

		$response->header( 'X-WP-Total', (int) $total );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		return $response;
	}

	/**
	 * Retrieves a single expense.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 2.0.0
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$expense = EAC()->expenses->get( $request['id'] );
		$data    = $this->prepare_item_for_response( $expense, $request );

		return rest_ensure_response( $data );
	}

	/**
	 * Creates a single expense.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 2.0.0
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new \WP_Error(
				'rest_exists',
				__( 'Cannot create existing expense.', 'wp-ever-accounting' ),
				array( 'status' => 400 )
			);
		}

		$data = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$expense = EAC()->expenses->insert( $data );
		if ( is_wp_error( $expense ) ) {
			return $expense;
		}

		$response = $this->prepare_item_for_response( $expense, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );

		return $response;
	}

	/**
	 * Updates a single expense.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 2.0.0
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $request ) {
		$expense = EAC()->expenses->get( $request['id'] );
		$data    = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$saved = $expense->fill( $data )->save();
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		$response = $this->prepare_item_for_response( $saved, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Deletes a single expense.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 2.0.0
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		$expense = EAC()->expenses->get( $request['id'] );
		$request->set_param( 'context', 'edit' );
		$data = $this->prepare_item_for_response( $expense, $request );

		if ( ! EAC()->expenses->delete( $expense->id ) ) {
			return new \WP_Error(
				'rest_cannot_delete',
				__( 'The expense cannot be deleted.', 'wp-ever-accounting' ),
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
	 * Prepares a single expense output for response.
	 *
	 * @param Expense          $item Expense object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 2.0.0
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = array();

		foreach ( array_keys( $this->get_schema_properties() ) as $key ) {
			$value = null;
			switch ( $key ) {
				case 'category':
					if ( ! empty( $item->category ) ) {
						$value      = new \stdClass();
						$properties = array_keys( $this->get_schema_properties()[ $key ]['properties'] );
						foreach ( $properties as $property ) {
							$value->$property = $item->category->$property;
						}
					}
					break;
				case 'account':
					if ( ! empty( $item->account ) ) {
						$value      = new \stdClass();
						$properties = array_keys( $this->get_schema_properties()[ $key ]['properties'] );
						foreach ( $properties as $property ) {
							$value->$property = $item->account->$property;
						}
					}
					break;
				case 'bill':
					if ( ! empty( $item->bill ) ) {
						$value      = new \stdClass();
						$properties = array_keys( $this->get_schema_properties()[ $key ]['properties'] );
						foreach ( $properties as $property ) {
							$value->$property = $item->bill->$property;
						}
					}
					break;

				case 'vendor':
					if ( ! empty( $item->vendor ) ) {
						$value      = new \stdClass();
						$properties = array_keys( $this->get_schema_properties()[ $key ]['properties'] );
						foreach ( $properties as $property ) {
							$value->$property = $item->vendor->$property;
						}
					}
					break;

				case 'updated_at':
				case 'crated_at':
				case 'date':
					$value = $this->prepare_date_response( $item->$key );
					break;
				default:
					$value = isset( $item->$key ) ? $item->$key : null;
					break;
			}

			$data[ $key ] = $value;
		}

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );

		/**
		 * Filter expense data returned from the REST API.
		 *
		 * @param \WP_REST_Response $response The response object.
		 * @param Expense           $item Expense object used to create response.
		 * @param \WP_REST_Request  $request Request object.
		 */
		return apply_filters( 'eac_rest_prepare_expense', $response, $item, $request );
	}

	/**
	 * Prepares a single expense for create or update.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 2.0.0
	 * @return array|\WP_Error Expense object or WP_Error.
	 */
	protected function prepare_item_for_database( $request ) {
		$schema = $this->get_item_schema();
		$props  = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );
		$data   = array();
		foreach ( $props as $prop ) {
			if ( isset( $request[ $prop ] ) ) {
				switch ( $prop ) {
					case 'category':
						$category = EAC()->categories->get( $request[ $prop ]['id'] );
						if ( ! $category ) {
							return new \WP_Error(
								'rest_invalid_category',
								__( 'Invalid category.', 'wp-ever-accounting' ),
								array( 'status' => 400 )
							);
						}
						$data['category_id'] = $category->id;
						break;
					case 'account':
						$account = EAC()->accounts->get( $request[ $prop ]['id'] );
						if ( ! $account ) {
							return new \WP_Error(
								'rest_invalid_account',
								__( 'Invalid account.', 'wp-ever-accounting' ),
								array( 'status' => 400 )
							);
						}
						$data['account_id'] = $account->id;
						break;

					case 'bill':
						$bill = EAC()->bills->get( $request[ $prop ]['id'] );
						if ( ! $bill ) {
							return new \WP_Error(
								'rest_invalid_bill',
								__( 'Invalid bill.', 'wp-ever-accounting' ),
								array( 'status' => 400 )
							);
						}
						$data['bill_id'] = $bill->id;
						break;

					case 'vendor':
						$vendor = EAC()->vendors->get( $request[ $prop ]['id'] );
						if ( ! $vendor ) {
							return new \WP_Error(
								'rest_invalid_vendor',
								__( 'Invalid vendor.', 'wp-ever-accounting' ),
								array( 'status' => 400 )
							);
						}
						$data['vendor_id'] = $vendor->id;
						break;

					case 'attachment':
						$attachment_id = $request[ $prop ]['id'];
						if ( ! empty( $attachment_id ) && 'attachment' === get_post_type( $attachment_id ) ) {
							$data['attachment_id'] = $attachment_id;
						}
						break;
					default:
						$data[ $prop ] = $request[ $prop ];
						break;
				}
			}
		}

		/**
		 * Filters expense before it is inserted via the REST API.
		 *
		 * @param array            $data Expense data.
		 * @param \WP_REST_Request $request Request object.
		 */
		return apply_filters( 'eac_rest_pre_insert_expense', $data, $request );
	}


	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @since 2.0.0
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Expense', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'               => array(
					'description' => __( 'Unique identifier for the expense.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'number'           => array(
					'description' => __( 'Expense number.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'paid_at'             => array(
					'description' => __( 'The date the expense took place, in the site\'s timezone.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'amount'           => array(
					'description' => __( 'Total amount of the expense.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'formatted_amount' => array(
					'description' => __( 'Formatted total amount of the expense.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'currency'         => array(
					'description' => __( 'Currency code of the expense.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'default'     => eac_base_currency(),
				),
				'exchange_rate'    => array(
					'description' => __( 'Exchange rate of the expense.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
					'default'     => 1,
				),
				'reference'        => array(
					'description' => __( 'Reference of the expense.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'note'             => array(
					'description' => __( 'Note of the expense.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'payment_method'     => array(
					'description' => __( 'Payment method of the expense.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'account'          => array(
					'description' => __( 'Account of the expense.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Unique identifier for the account.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'embed', 'edit' ),
							'readonly'    => true,
							'required'    => true,
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'name' => array(
							'description' => __( 'Account name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'embed', 'edit' ),
						),
					),
				),
				'bill'             => array(
					'description' => __( 'Bill of the expense.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'embed', 'edit' ),
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Unique identifier for the document.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'embed', 'edit' ),
							'readonly'    => true,
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'name' => array(
							'description' => __( 'Bill name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'embed', 'edit' ),
						),
					),
				),
				'vendor'           => array(
					'description' => __( 'Vendor of the expense.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'embed', 'edit' ),
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Unique identifier for the vendor.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'embed', 'edit' ),
							'readonly'    => true,
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'name' => array(
							'description' => __( 'Vendor name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'embed', 'edit' ),
						),
					),
				),
				'category'         => array(
					'description' => __( 'Category of the expense.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'embed', 'edit' ),
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Unique identifier for the category.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'embed', 'edit' ),
							'readonly'    => true,
							'required'    => true,
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'name' => array(
							'description' => __( 'Category name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'embed', 'edit' ),
						),
					),
				),
				'attachment_id'    => array(
					'description' => __( 'Attachment ID of the expense.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'parent_id'        => array(
					'description' => __( 'Parent expense ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'reconciled'       => array(
					'description' => __( 'Whether the expense is reconciled.', 'wp-ever-accounting' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'status'           => array(
					'description' => __( 'Status of the expense.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'default'     => 'pending',
				),
				'uuid'             => array(
					'description' => __( 'UUID of the expense.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'created_via'      => array(
					'description' => __( 'Created via of the expense.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'creator_id'       => array(
					'description' => __( 'Author ID of the expense.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'updated_at'       => array(
					'description' => __( "The date the expense was last updated, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'created_at'       => array(
					'description' => __( "The date the expense was created, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		/**
		 * Filters the expense's schema.
		 *
		 * @param array $schema Item schema data.
		 *
		 * @since 2.0.0
		 */
		$schema = apply_filters( 'eac_rest_expense_item_schema', $schema );

		return $this->add_additional_fields_schema( $schema );
	}
}
