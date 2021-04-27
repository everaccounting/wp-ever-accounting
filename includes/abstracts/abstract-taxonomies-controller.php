<?php
/**
 * REST Taxonomies controller class.
 *
 * Handles Custom Taxonomy data.
 *
 * @version 1.1.5
 * @subpackage  Abstracts
 * @package     EverAccounting
 */

namespace  EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Taxonomies_Controller
 *
 * @package     EverAccounting
 */
abstract class Taxonomies_Controller extends Entities_Controller {
	/**
	 * Taxonomy type.
	 *
	 * @var string
	 */
	protected $taxonomy_type;

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
		return $this->check_term_permissions() ? true : new \WP_Error( 'rest_cannot_view', __( 'Sorry, you cannot list resources.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
	}

	/**
	 * Check if a given request has access to read an item.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		$term = get_term( (int) $request['id'] );

		if ( $term && ! $this->check_term_permissions( 'read', $term->term_id ) ) {
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
		$term = get_term( (int) $request['id'] );

		if ( $term && ! $this->check_term_permissions( 'edit', $term->term_id ) ) {
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
		$term = get_term( (int) $request['id'] );

		if ( $term && ! $this->check_term_permissions( 'delete', $term->term_id ) ) {
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
		return $this->check_term_permissions( 'batch' ) ? true : new \WP_Error( 'rest_cannot_batch', __( 'Sorry, you are not allowed to batch manipulate this resource.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );

	}

	/**
	 * Check permissions of items on REST API.
	 *
	 * @since 1.1.5
	 * @param string $context   Request context.
	 * @param int    $object_id Post ID.
	 * @return bool
	 */
	public function check_term_permissions( $context = 'read', $object_id = 0 ) {

		$contexts = array(
			'read'   => 'manage_terms',
			'create' => 'edit_terms',
			'edit'   => 'edit_terms',
			'delete' => 'manage_terms',
			'batch'  => 'manage_terms',
		);

		$cap             = $contexts[ $context ];
		$taxonomy_object = get_taxonomy( $this->taxonomy_type );
		$permission      = current_user_can( $taxonomy_object->cap->$cap, $object_id );

		return apply_filters( 'eaccounting_rest_check_permissions', $permission, $context, $object_id, $this->taxonomy_type );
	}
}
