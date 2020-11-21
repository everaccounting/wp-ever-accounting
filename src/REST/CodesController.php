<?php
/**
 * Currency codes Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

defined( 'ABSPATH' ) || die();

class CodesController extends Controller {
	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'codes';


	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 1.1.0
	 *
	 * @see   register_rest_route()
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
			)
		);

		$get_item_args = array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<code>[a-z]+)',
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
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check if a given request has access to read items.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		//      if ( ! current_user_can( "ea_manage_eaccounting" ) ) {
		//          return new \WP_Error( 'eaccounting_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
		//      }

		return true;
	}

	/**
	 * Check if a given request has access to read an item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		//      if ( ! current_user_can( "ea_manage_eaccounting" ) ) {
		//          return new \WP_Error( 'eaccounting_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
		//      }

		return true;
	}

	/**
	 * Get a collection of posts.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_items( $request ) {
		$codes = eaccounting_get_data( 'currencies' );

		return rest_ensure_response( $codes );
	}


	/**
	 * Get a single object.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_item( $request ) {
		$code  = $request['code'];
		$codes = eaccounting_get_data( 'currencies' );
		if ( is_wp_error( $codes ) || empty( $code ) || ! isset( $codes[ $code ] ) ) {
			return new \WP_Error( 'rest_object_invalid_code', __( 'Invalid currency code.', 'wp-ever-accounting' ), array( 'status' => 404 ) );
		}

		return rest_ensure_response( $codes[ $code ] );
	}
}
