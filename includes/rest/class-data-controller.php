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

use EverAccounting\Abstracts\Controller;

defined( 'ABSPATH' ) || exit;

/**
 * REST API Data controller class.
 *
 * @package EverAccounting\REST
 * @extends Controller
 */
class Data_Controller extends Controller {
	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'get_data';

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
					'callback'            => array( $this, 'get_data' ),
					'permission_callback' => array( $this, 'get_data_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Check whether a given request has permission to read site data.
	 *
	 * @param  \WP_REST_Request $request Full details about the request.
	 * @return \WP_Error|boolean
	 */
	public function get_data_permissions_check( $request ) {
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
		$type = $request['type'];

		if( empty( $type ) ){
			return new \WP_Error('rest_data_type_invalid', __('Please assign a data type', 'wp-ever-accounting'));
		}
		$data = [];
		switch ($type){
			case 'codes':
				$data = eaccounting_get_data( 'currencies' );
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


}
