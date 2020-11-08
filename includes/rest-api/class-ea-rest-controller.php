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
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'ea/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '';

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

		$data = array(
			'id'    => isset( $user->ID ) ? $user->ID : '',
			'name'  => isset( $user->display_name ) ? $user->display_name : '',
			'email' => isset( $user->user_email ) ? $user->user_email : '',
			'photo' => isset( $user->ID ) ? get_avatar_url( $user->ID ) : get_avatar_url( null ),
		);

		return $data;
	}


	/**
	 * The method is used for retriving single object from rest request
	 *
	 * @since 1.0.2
	 *
	 * @param        $endpoint
	 *
	 * @param        $id
	 * @param null   $default
	 * @param string $namespace
	 *
	 * @return array|null
	 */
	protected static function get_rest_object( $endpoint, $id, $default = null, $namespace = '/ea/v1/' ) {
		if ( empty( $id ) ) {
			return $default;
		}

		$endpoint = $namespace . untrailingslashit( ltrim( $endpoint, '/' ) ) . '/' . intval( $id );
		$response = eaccounting_rest_request( $endpoint, $args = array(), $method = 'GET' );

		return is_wp_error( $response ) ? $default : $response;
	}

	/**
	 * Get normalized rest base.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_normalized_rest_base() {
		return preg_replace( '/\(.*\)\//i', '', $this->rest_base );
	}

	/**
	 * Check batch limit.
	 *
	 * @since 1.1.0
	 *
	 * @param array $items Request items.
	 *
	 * @return bool|\WP_Error
	 */
	protected function check_batch_limit( $items ) {
		$limit = apply_filters( 'eaccounting_rest_batch_items_limit', 100, $this->get_normalized_rest_base() );
		$total = 0;

		if ( ! empty( $items['create'] ) ) {
			$total += count( $items['create'] );
		}

		if ( ! empty( $items['update'] ) ) {
			$total += count( $items['update'] );
		}

		if ( ! empty( $items['delete'] ) ) {
			$total += count( $items['delete'] );
		}

		if ( $total > $limit ) {
			/* translators: %s: items limit */
			return new \WP_Error( 'eaccounting_rest_request_entity_too_large', sprintf( __( 'Unable to accept more than %s items for this request.', 'wp-ever-accounting' ), $limit ), array( 'status' => 413 ) );
		}

		return true;
	}

	/**
	 * Get the batch schema, conforming to JSON Schema.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_public_batch_schema() {
		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'batch',
			'type'       => 'object',
			'properties' => array(
				'create' => array(
					'description' => __( 'List of created resources.', 'wp-ever-accounting' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type' => 'object',
					),
				),
				'update' => array(
					'description' => __( 'List of updated resources.', 'wp-ever-accounting' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type' => 'object',
					),
				),
				'delete' => array(
					'description' => __( 'List of delete resources.', 'wp-ever-accounting' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type' => 'integer',
					),
				),
			),
		);
	}


	/**
	 * Retrieves the query params for the collections.
	 *
	 * @since 1.1.0
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {
		$params                       = array();
		$params['context']            = $this->get_context_param();
		$params['context']['default'] = 'view';
		$params['page']               = array(
			'description'       => __( 'Current page of the collection.', 'wp-ever-accounting' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);

		$params['per_page'] = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'wp-ever-accounting' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 1,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['search'] = array(
			'description'       => __( 'Limit results to those matching a string.', 'wp-ever-accounting' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['exclude'] = array(
			'description'       => __( 'Ensure result set excludes specific IDs.', 'wp-ever-accounting' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['include'] = array(
			'description'       => __( 'Limit result set to specific ids.', 'wp-ever-accounting' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['offset']  = array(
			'description'       => __( 'Offset the result set by a specific number of items.', 'wp-ever-accounting' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order']   = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'wp-ever-accounting' ),
			'type'              => 'string',
			'default'           => 'desc',
			'enum'              => array( 'asc', 'desc' ),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orderby'] = array(
			'description'       => __( 'Sort collection by object attribute.', 'wp-ever-accounting' ),
			'type'              => 'string',
			'default'           => 'date_created',
			'enum'              => array(
				'date_created',
				'id',
				'include',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		return apply_filters( 'eaccounting_rest_collection_params', $params, $this );
	}
}
