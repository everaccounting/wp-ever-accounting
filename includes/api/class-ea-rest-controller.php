<?php
/**
 * Main Rest Controller Class.
 *
 * @since       1.0.2
 * @subpackage  Api
 * @package     EverAccounting
 */

namespace EverAccounting\API;

defined( 'ABSPATH' ) || die();


abstract class Controller extends \WP_REST_Controller {

	/**
	 * @since       1.0.2
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return bool|\WP_Error
	 */
	public function get_items_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 *
	 * @since       1.0.2
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return bool|\WP_Error
	 */
	public function get_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 *
	 * @since       1.0.2
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return bool|\WP_Error
	 */
	public function create_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 *
	 * @since       1.0.2
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return bool|\WP_Error
	 */
	public function update_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 *
	 * @since       1.0.2
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return bool|\WP_Error
	 */
	public function delete_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Retrieves the query params for the collections.
	 *
	 * @since 1.0.2
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {
		return array(
			'context'  => $this->get_context_param(),
			'page'     => array(
				'description'       => __( 'Current page of the collection.', 'wp-ever-accounting' ),
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
				'minimum'           => 1,
			),
			'per_page' => array(
				'description'       => __( 'Maximum number of items to be returned in result set.', 'wp-ever-accounting' ),
				'type'              => 'integer',
				'default'           => 20,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'offset'   => array(
				'description'       => __( 'Offset the result set by a specific number of items.', 'wp-ever-accounting' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
			'search'   => array(
				'description'       => __( 'Limit results to those matching a string.', 'wp-ever-accounting' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'order'    => array(
				'description' => __( 'Order sort attribute ascending or descending.', 'wp-ever-accounting' ),
				'type'        => 'string',
				'default'     => 'desc',
				'enum'        => array( 'asc', 'desc', ),
			),
			'orderby'  => array(
				'description' => __( 'Sort collection by contact attribute.', 'wp-ever-accounting' ),
				'type'        => 'string',
				'default'     => 'id',
			),
		);
	}

	/**
	 * Checks the post_date_gmt or modified_gmt and prepare
	 *
	 * @since 1.0.2
	 *
	 * @param null $date
	 *
	 * @return string|null
	 */
	protected function prepare_date_response( $date = null ) {
		if ( '0000-00-00 00:00:00' === $date || '0000-00-00' === $date ) {
			return null;
		}

		return mysql_to_rfc3339( $date );
	}

	/**
	 * @since 1.0.2
	 *
	 * @param $user_id
	 *
	 * @return array
	 */
	protected function get_wp_user_data( $user_id ) {
		$user = get_user_by( 'ID', $user_id );

		$data = [
			'id'    => isset( $user->ID ) ? $user->ID : '',
			'name'  => isset( $user->display_name ) ? $user->display_name : '',
			'email' => isset( $user->user_email ) ? $user->user_email : '',
			'photo' => isset( $user->ID ) ? get_avatar_url( $user->ID ) : get_avatar_url( null ),
		];

		return $data;
	}


	/**
	 * The method is used for retriving single object from rest request
	 *
	 * @since 1.0.2
	 *
	 * @param        $id
	 * @param null   $default
	 * @param string $base
	 *
	 * @param        $endpoint
	 *
	 * @return array|null
	 */
	protected static function get_rest_object( $endpoint, $id, $default = null, $base = '/ea/v1/' ) {
		if ( empty( $id ) ) {
			return $default;
		}

		$endpoint = $base . untrailingslashit( ltrim( $endpoint, '/' ) ) . '/' . intval( $id );
		$response = eaccounting_rest_request( $endpoint, $args = array(), $method = 'GET' );

		return is_wp_error( $response ) ? $default : $response;
	}
}
