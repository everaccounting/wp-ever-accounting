<?php
/**
 * REST API Data controller.
 *
 * Handles requests to the /data endpoint.
 *
 * @package EverAccounting\REST
 * @since   1.1.0
 */

namespace EverAccounting\REST;

defined( 'ABSPATH' ) || exit;

/**
 * REST API Data controller class.
 *
 * @package EverAccounting\REST
 * @extends Controller
 */
class DataController extends Controller {
	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'data';

	/**
	 * Register routes.
	 *
	 * @since 3.5.0
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
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check whether a given request has permission to read site data.
	 *
	 * @param  \WP_REST_Request $request Full details about the request.
	 * @return \WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
//		if ( ! current_user_can( 'manage_eaccounting' ) ) {
//			return new \WP_Error( 'eaccounting_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
//		}

		return true;
	}

	/**
	 * Check whether a given request has permission to read site settings.
	 *
	 * @param  \WP_REST_Request $request Full details about the request.
	 * @return \WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
//		if ( ! current_user_can( 'manage_eaccounting' ) ) {
//			return new \WP_Error( 'eaccounting_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
//		}
		return true;
	}

	/**
	 * Return the list of data resources.
	 *
	 * @since  1.1.0
	 * 
	 * @param  \WP_REST_Request $request Request data.
	 * 
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_items( $request ) {
		$data      = array();
		$resources = array(
			array(
				'slug'        => 'countries',
				'description' => __( 'List of countries.', 'wp-ever-accounting' ),
			),
			array(
				'slug'        => 'currencies',
				'description' => __( 'List of currencies.', 'wp-ever-accounting' ),
			),
		);

		foreach ( $resources as $resource ) {
			$item   = $this->prepare_item_for_response( (object) $resource, $request );
			$data[] = $this->prepare_response_for_collection( $item );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Prepare a data resource object for serialization.
	 *
	 * @param \\stdClass        $resource Resource data.
	 * @param \WP_REST_Request $request  Request object.
	 * 
	 * @return \WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $resource, $request ) {
		$data = array(
			'slug'        => $resource->slug,
			'description' => $resource->description,
		);

		$data = $this->add_additional_fields_to_object( $data, $request );
		$data = $this->filter_response_by_context( $data, 'view' );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $resource ) );

		return $response;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param object $item Data object.
	 * 
	 * @return array Links for the given country.
	 */
	protected function prepare_links( $item ) {
		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%s', $this->namespace, $this->rest_base, $item->slug ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		return $links;
	}

	/**
	 * Get the data index schema, conforming to JSON Schema.
	 *
	 * @since  3.5.0
	 * 
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'data_index',
			'type'       => 'object',
			'properties' => array(
				'slug'        => array(
					'description' => __( 'Data resource ID.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( 'Data resource description.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}