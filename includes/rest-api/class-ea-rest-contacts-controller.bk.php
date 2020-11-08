<?php
/**
 * Contacts Rest Controller Class.
 *
 * @package     EverAccounting
 * @subpackage  Api
 * @since       1.0.2
 */

namespace EverAccounting\API;

defined( 'ABSPATH' ) || exit();

class Contacts_Controller extends Controller {
	/**
	 * @var string
	 */
	protected $namespace = 'ea/v1';

	/**
	 * @var string
	 */
	protected $rest_base = 'contacts';

	/**
	 * @since 1.0.2
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
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
						'description' => __( 'Unique identifier for the contact.', 'wp-ever-accounting' ),
						'type'        => 'integer',
						'required'    => true,
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $get_item_args,
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/types',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_contact_types' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/bulk',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'handle_bulk_actions' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
			)
		);

	}

	/**
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|WP_Error|WP_REST_Response
	 * @since 1.0.2
	 */
	public function get_items( $request ) {
		$args = array(
			'type'         => $request['type'],
			'city'         => $request['city'],
			'state'        => $request['state'],
			'postcode'     => $request['postcode'],
			'country'      => $request['country'],
			'date_created' => $request['date_created'],
			'include'      => $request['include'],
			'exclude'      => $request['exclude'],
			'search'       => $request['search'],
			'orderby'      => $request['orderby'],
			'order'        => $request['order'],
			'per_page'     => $request['per_page'],
			'page'         => $request['page'],
			'offset'       => $request['offset'],
		);

		$query_result   = eaccounting_get_contacts( $args );
		$total_contacts = eaccounting_get_contacts( $args, true );
		$response       = array();

		foreach ( $query_result as $item ) {
			$data       = $this->prepare_item_for_response( $item, $request );
			$response[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $response );

		$per_page = (int) $args['per_page'];

		$response->header( 'X-WP-Total', (int) $total_contacts );

		$max_pages = ceil( $total_contacts / $per_page );

		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		return rest_ensure_response( $response );
	}

	/***
	 * @param WP_REST_Request $request
	 *
	 * @return int|mixed|WP_Error|WP_REST_Response|null
	 * @since 1.0.2
	 */
	public function create_item( $request ) {
		$request->set_param( 'context', 'edit' );

		$prepared = $this->prepare_item_for_database( $request );

		$item_id = eaccounting_insert_contact( (array) $prepared );
		if ( is_wp_error( $item_id ) ) {
			return $item_id;
		}

		$contact = eaccounting_get_contact( $item_id );

		$request->set_param( 'context', 'view' );

		$response = $this->prepare_item_for_response( $contact, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );

		return $response;
	}

	/**
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|WP_Error|WP_REST_Response
	 * @since 1.0.2
	 */
	public function get_item( $request ) {
		$item_id = intval( $request['id'] );
		$request->set_param( 'context', 'view' );
		$item = eaccounting_get_contact( $item_id );
		if ( is_null( $item ) ) {
			return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the contact', 'wp-ever-accounting' ) );
		}

		$response = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $response );
	}

	/**
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return int|mixed|WP_Error|WP_REST_Response|null
	 * @since 1.0.2
	 */
	public function update_item( $request ) {
		$request->set_param( 'context', 'edit' );
		$item_id = intval( $request['id'] );

		$item = eaccounting_get_contact( $item_id );
		if ( is_null( $item ) ) {
			return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the contact', 'wp-ever-accounting' ) );
		}
		$prepared_args = $this->prepare_item_for_database( $request );

		$prepared_args->id = $item_id;

		if ( ! empty( $prepared_args ) ) {
			$updated = eaccounting_insert_contact( (array) $prepared_args );

			if ( is_wp_error( $updated ) ) {
				return $updated;
			}
		}

		$request->set_param( 'context', 'view' );
		$item     = eaccounting_get_contact( $item_id );
		$response = $this->prepare_item_for_response( $item, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * since 1.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return void|WP_Error|WP_REST_Response
	 * @since 1.0.2
	 */
	public function delete_item( $request ) {
		$item_id = intval( $request['id'] );
		$item    = eaccounting_get_contact( $item_id );
		if ( is_null( $item ) ) {
			return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the contact', 'wp-ever-accounting' ) );
		}

		$request->set_param( 'context', 'view' );

		$previous = $this->prepare_item_for_response( $item, $request );
		$retval   = eaccounting_delete_contact( $item_id );
		if ( ! $retval ) {
			return new WP_Error( 'rest_cannot_delete', __( 'This contact cannot be deleted.', 'wp-ever-accounting' ), array( 'status' => 500 ) );
		}

		$response = new WP_REST_Response();
		$response->set_data(
			array(
				'deleted'  => true,
				'previous' => $previous->get_data(),
			)
		);

		return $response;
	}

	/**
	 *
	 * @param $request
	 *
	 * @return mixed|WP_REST_Response
	 * @since 1.0.2
	 */
	public function get_contact_types( $request ) {
		return rest_ensure_response( $this->assoc_to_options( eaccounting_get_contact_types() ) );
	}

	public function handle_bulk_actions( $request ) {
		$actions = array(
			'delete',
		);
		$action  = $request['action'];
		$items   = $request['items'];
		if ( empty( $action ) || ! in_array( $action, $actions ) ) {
			return new WP_Error( 'invalid_bulk_action', __( 'Invalid bulk action', 'wp-ever-accounting' ) );
		}
		$deleted = array();
		switch ( $action ) {
			case 'delete':
				foreach ( $items as $item ) {
					$is_wp_error = eaccounting_delete_contact( $item );
					if ( $is_wp_error ) {
						return $is_wp_error;
						break;
					}
					$deleted[ $item ] = $is_wp_error;
				}
				break;
		}

		return rest_ensure_response( $deleted );
	}


	/**
	 *
	 * @param mixed           $item
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|WP_Error|WP_REST_Response
	 * @since 1.0.2
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = array(
			'id'            => intval( $item->id ),
			'user_id'       => $item->user_id,
			'name'          => $item->name,
			'email'         => $item->email,
			'phone'         => $item->phone,
			'fax'           => $item->fax,
			'birth_date'    => $this->prepare_date_response( $item->birth_date ),
			'address'       => $item->address,
			'country'       => $item->country,
			'website'       => $item->website,
			'tax_number'    => $item->tax_number,
			'currency_code' => $item->currency_code,
			'note'          => $item->note,
			'file'          => self::get_rest_object( 'files', $item->file_id ),
			'type'          => $item->type,
			'created_at'    => $this->prepare_date_response( $item->created_at ),
		);

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $item ) );

		return $response;
	}

	/**
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return object|stdClass|WP_Error
	 * @since 1.0.2
	 */
	public function prepare_item_for_database( $request ) {
		$prepared_item = new stdClass();
		$schema        = $this->get_item_schema();

		if ( ! empty( $schema['properties']['id'] ) && isset( $request['id'] ) ) {
			$prepared_item->id = $request['id'];
		}
		if ( ! empty( $schema['properties']['user_id'] ) && isset( $request['user_id'] ) ) {
			$prepared_item->user_id = $request['user_id'];
		}
		if ( ! empty( $schema['properties']['name'] ) && isset( $request['name'] ) ) {
			$prepared_item->name = $request['name'];
		}
		if ( ! empty( $schema['properties']['email'] ) && isset( $request['email'] ) ) {
			$prepared_item->email = $request['email'];
		}
		if ( ! empty( $schema['properties']['phone'] ) && isset( $request['phone'] ) ) {
			$prepared_item->phone = $request['phone'];
		}
		if ( ! empty( $schema['properties']['fax'] ) && isset( $request['fax'] ) ) {
			$prepared_item->fax = $request['fax'];
		}
		if ( ! empty( $schema['properties']['birth_date'] )
			 && isset( $request['birth_date'] )
			 && eaccounting_sanitize_date( $request['birth_date'], false )
		) {
			$prepared_item->birth_date = $request['birth_date'];
		}
		if ( ! empty( $schema['properties']['address'] ) && isset( $request['address'] ) ) {
			$prepared_item->address = $request['address'];
		}
		if ( ! empty( $schema['properties']['country'] ) && isset( $request['country'] ) ) {
			$prepared_item->country = $request['country'];
		}
		if ( ! empty( $schema['properties']['website'] ) && isset( $request['website'] ) ) {
			$prepared_item->website = $request['website'];
		}
		if ( ! empty( $schema['properties']['tax_number'] ) && isset( $request['tax_number'] ) ) {
			$prepared_item->tax_number = $request['tax_number'];
		}
		if ( ! empty( $schema['properties']['currency_code'] ) && isset( $request['currency_code'] ) ) {
			$prepared_item->currency_code = $request['currency_code'];
		}
		if ( ! empty( $schema['properties']['note'] ) && isset( $request['note'] ) ) {
			$prepared_item->note = $request['note'];
		}
		if ( ! empty( $schema['properties']['file_id'] ) && isset( $request['file_id'] ) ) {
			$prepared_item->file_id = $request['file_id'];
		}
		if ( ! empty( $schema['properties']['type'] ) && isset( $request['type'] ) ) {
			$prepared_item->type = $request['type'];
		}

		return $prepared_item;
	}

	/**
	 *
	 * @param $item
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function prepare_links( $item ) {
		$base = sprintf( '/%s/%s/', $this->namespace, $this->rest_base );
		$url  = $base . $item->id;

		// Entity meta.
		$links = array(
			'self'       => array(
				'href' => rest_url( $url ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
		);

		return $links;
	}

	/**
	 * Retrieves the items's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 * @since 1.0.2
	 *
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Contact', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'            => array(
					'description' => __( 'Unique identifier for the contact.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'user_id'       => array(
					'description' => __( 'WP user ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
					'required'    => true,
				),
				'name'          => array(
					'description' => __( 'Name for the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'default'     => '',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'tax_number'    => array(
					'description' => __( 'Tax number of the contact', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'email'         => array(
					'description' => __( 'The email address for the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'phone'         => array(
					'description' => __( 'Phone number for the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'fax'           => array(
					'description' => __( 'Fax number for the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'currency_code' => array(
					'description' => __( 'Currency code for customer.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'address'       => array(
					'description' => __( 'Address 1 of the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'country'       => array(
					'description' => __( 'Country of the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'website'       => array(
					'description' => __( 'website of the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'esc_url_raw',
					),
				),
				'note'          => array(
					'description' => __( 'Note for the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
				),

				'file_id'       => array(
					'description' => __( 'Photo of the contact.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
				),

				'type'          => array(
					'description' => __( 'Types of the contact', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'edit', 'view', 'embed' ),
					'required'    => true,
				),
				'birth_date'    => array(
					'description' => __( 'Birth date', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'embed', 'view' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}


	/**
	 * Retrieves the query params for the items collection.
	 *
	 * @return array Collection parameters.
	 * @since 1.0.2
	 *
	 */
	public function get_collection_params() {
		$query_params                       = parent::get_collection_params();
		$query_params['context']['default'] = 'view';
		$query_params['exclude']            = array(
			'description' => __( 'Ensure result set excludes specific ids.', 'wp-ever-accounting' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		$query_params['include'] = array(
			'description' => __( 'Limit result set to specific IDs.', 'wp-ever-accounting' ),
			'type'        => 'array',
			'items'       => array(
				'type' => 'integer',
			),
			'default'     => array(),
		);

		$query_params['search'] = array(
			'description' => __( 'Limit result set to specific search.', 'wp-ever-accounting' ),
			'type'        => 'string',
			'default'     => '',
		);

		return $query_params;
	}

}
