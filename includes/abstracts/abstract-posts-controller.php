<?php
/**
 * REST Posts controller class.
 *
 * Handles CPT data.
 *
 * @version 1.1.5
 * @subpackage  Abstracts
 * @package     EverAccounting
 */

namespace  EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

abstract class Posts_Controller extends Entities_Controller {
	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Controls visibility on frontend.
	 *
	 * @var string
	 */
	public $public = false;

	/**
	 * Check if a given request has access to read items.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		return $this->check_post_permissions() ? true : new \WP_Error( 'rest_cannot_view', __( 'Sorry, you cannot list resources.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
	}

	/**
	 * Check if a given request has access to read an item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		$post = get_post( (int) $request['id'] );

		if ( $post && ! $this->check_post_permissions( 'read', $post->ID ) ) {
			return new \WP_Error( 'rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to update an item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 */
	public function update_item_permissions_check( $request ) {
		$post = get_post( (int) $request['id'] );

		if ( $post && ! $this->check_post_permissions( 'edit', $post->ID ) ) {
			return new \WP_Error( 'rest_cannot_edit', __( 'Sorry, you are not allowed to edit this resource.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to delete an item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|\WP_Error
	 */
	public function delete_item_permissions_check( $request ) {
		$post = get_post( (int) $request['id'] );

		if ( $post && ! $this->check_post_permissions( 'delete', $post->ID ) ) {
			return new \WP_Error( 'rest_cannot_delete', __( 'Sorry, you are not allowed to delete this resource.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access batch create, update and delete items.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return boolean|\WP_Error
	 */
	public function batch_items_permissions_check( $request ) {
		return $this->check_post_permissions( 'batch' ) ? true : new \WP_Error( 'rest_cannot_batch', __( 'Sorry, you are not allowed to batch manipulate this resource.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );

	}

	/**
	 * Check permissions of items on REST API.
	 *
	 * @since 1.1.5
	 * @param string $context   Request context.
	 * @param int    $object_id Post ID.
	 * @return bool
	 */
	public function check_post_permissions( $context = 'read', $object_id = 0 ) {

		$contexts = array(
			'read'   => 'read_private_posts',
			'create' => 'publish_posts',
			'edit'   => 'edit_post',
			'delete' => 'delete_post',
			'batch'  => 'edit_others_posts',
		);

		$cap              = $contexts[ $context ];
		$post_type_object = get_post_type_object( $this->post_type );
		$permission       = current_user_can( $post_type_object->cap->$cap, $object_id );

		return apply_filters( 'eaccounting_rest_check_permissions', $permission, $context, $object_id, $this->post_type );
	}

}
