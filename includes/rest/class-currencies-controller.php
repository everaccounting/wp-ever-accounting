<?php
/**
 * Currencies Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Abstracts\Entities_Controller;
use EverAccounting\Models\Currency;
use WP_Error;

defined( 'ABSPATH' ) || die();

/**
 * Class Currencies_Controller
 *
 * @package EverAccounting\REST
 */
class Currencies_Controller extends Entities_Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $rest_base = 'currencies';

	/**
	 * entity type.
	 *
	 * @var string
	 *
	 * @since 1.1.2
	 */
	protected $entity_type = 'currency';

	/**
	 * Entity model class.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $entity_model = Currency::class;

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
			'/' . $this->rest_base . '/(?P<code>[\w-]+)',
			array(
				'args'   => array(
					'code' => array(
						'description' => __( 'Unique identifier for the entity.', 'wp-ever-accounting' ),
						'type'        => 'string',
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
	 * Check if a given request has access to read items.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_eaccounting' ) ) {
			return new WP_Error( 'eaccounting_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to read an item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		return $this->get_items_permissions_check( $request );
	}

	/**
	 * Get a single object.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	public function get_item( $request ) {

		// Fetch the item.
		$object = $this->get_object( $request['code'] );

		if ( is_wp_error( $object ) ) {
			return $object;
		}

		// Generate a response.
		return rest_ensure_response( $this->prepare_item_for_response( $object, $request ) );
	}

	/**
	 * Check if a given request has access to create an item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_eaccounting' ) ) {
			return new \WP_Error( 'eaccounting_rest_cannot_create', __( 'Sorry, you are not allowed to create resources.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Create a single object.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	// public function create_item( $request ) {
	// try {
	// if ( empty( $this->entity_model ) || ! class_exists( $this->entity_model ) ) {
	// throw new \Exception( __( 'You need to specify a entity model class for this controller', 'wp-ever-accounting' ), 400 );
	// }
	// if ( ! empty( $request['id'] ) ) {
	// throw new \Exception( __( 'Cannot create existing resource.', 'wp-ever-accounting' ), 400 );
	// }
	// $object = new $this->entity_model();
	// $object = $this->prepare_object_for_database( $object, $request );
	// $object->save();
	// $this->update_additional_fields_for_object( (array) $object, $request );
	//
	// $request->set_param( 'context', 'edit' );
	// $response = $this->prepare_item_for_response( $object, $request );
	// $response = rest_ensure_response( $response );
	// $response->set_status( 201 );
	// $response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $object->get_id() ) ) );
	//
	// return $response;
	//
	// } catch ( \Exception $e ) {
	// return new \WP_Error( 'create_item', $e->getMessage() );
	// }
	// }

	/**
	 * Get objects.
	 *
	 * @param array            $query_args Query args.
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since  1.1.0
	 * @return array|int|WP_Error
	 */
	protected function get_objects( $query_args, $request ) {
		return eaccounting_get_currencies( $query_args );
	}

	/**
	 * Retrieves the items's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 *
	 * @since 1.1.0
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Currency', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'                 => array(
					'description' => __( 'Unique identifier for the currency.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'name'               => array(
					'description' => __( 'Name of the currency.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'code'               => array(
					'description' => __( 'Unique code for the currency.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'rate'               => array(
					'description' => __( 'Current rate for the currency.', 'wp-ever-accounting' ),
					'type'        => array( 'string', 'numeric' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'precision'          => array(
					'description' => __( 'Precision count.', 'wp-ever-accounting' ),
					'type'        => array( 'string', 'numeric' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'symbol'             => array(
					'description' => __( 'Currency Sumbol.', 'wp-ever-accounting' ),
					'type'        => array( 'string' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'position'           => array(
					'description' => __( 'Position.', 'wp-ever-accounting' ),
					'type'        => array( 'string' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'enum'        => array( 'before', 'after' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'decimal_separator'  => array(
					'description' => __( 'Decimal separator count.', 'wp-ever-accounting' ),
					'type'        => array( 'string' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'thousand_separator' => array(
					'description' => __( 'Thousand separator count.', 'wp-ever-accounting' ),
					'type'        => array( 'string' ),
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'date_created'       => array(
					'description' => __( 'Created date of the currency.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),

			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Retrieves the query params for the items collection.
	 *
	 * @return array Collection parameters.
	 *
	 * @since 1.1.0
	 */
	public function get_collection_params() {
		$query_params                       = parent::get_collection_params();
		$query_params['context']['default'] = 'view';

		$params['orderby'] = array(
			'description'       => __( 'Sort collection by object attribute.', 'wp-ever-accounting' ),
			'type'              => 'string',
			'default'           => 'id',
			'enum'              => array(
				'name',
				'id',
				'code',
				'rate',
				'enabled',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $query_params;
	}
}
