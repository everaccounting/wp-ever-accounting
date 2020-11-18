<?php
/**
 * Customers Rest Controller Class.
 *
 * @package     EverAccounting
 * @subpackage  Api
 * @since       1.1.0
 */

namespace EverAccounting\API;

defined( 'ABSPATH' ) || exit();

class Customers_Controller extends Controller {
	/**
	 * @var string
	 */
	protected $namespace = 'ea/v1';

	/**
	 * @var string
	 */
	protected $rest_base = 'customers';

	/**
	 * @since 1.0.2
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'  => \WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_items' ),
					// 'permission_callback' => current_user_can( 'ea_manage_customer' ),
					'args'     => $this->get_collection_params(),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => current_user_can( 'ea_manage_customer' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		$get_item_args = array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);

	}

	/**
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
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

	/**
	 *
	 * @param mixed            $item
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed|\WP_Error|\WP_REST_Response
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
