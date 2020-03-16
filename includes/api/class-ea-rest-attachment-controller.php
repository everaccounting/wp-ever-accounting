<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Attachment_Controller extends EAccounting_REST_Controller {
	/**
	 * @var string
	 */
	protected $namespace = 'ea/v1';

	/**
	 * @var string
	 */
	protected $rest_base = 'attachment';

	/**
	 * @since 1.0.0
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/attachments', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'upload_attachments' ],
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'                => [],
			),
		) );
	}

	/**
	 * since 1.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|WP_Error|WP_REST_Response
	 */
	public function upload_attachments( $request ) {
		$file      = $_FILES['attachment'];
		$movefiles = eaccounting_upload_file( $file );

		$response = rest_ensure_response( $movefiles );
		$response->set_status( 200 );

		return $response;
	}


}
