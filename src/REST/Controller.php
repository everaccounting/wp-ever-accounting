<?php
/**
 * Main Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */
namespace EverAccounting\REST;

defined( 'ABSPATH' ) || die();

/**
 * Class Controller
 *
 * @since   1.1.0
 *
 * @package EverAccounting\REST
 */
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
	 *
	 * @since 1.0.2
	 *
	 * @param $item
	 *
	 * @return array
	 */
	protected function prepare_links( $item ) {
		$base = sprintf( '/%s/%s/', $this->namespace, $this->rest_base );
		$url  = $base . $item->get_id();

		// Entity meta.
		return array(
			'self'       => array(
				'href' => rest_url( $url ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
		);
	}

	/**
	 *
	 * @since 1.0.2
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return array
	 */
	public function prepare_item_for_database( $request ) {
		$schema    = $this->get_item_schema();
		$data_keys = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );
		$params    = $request->get_params();
		$data      = array();

		foreach ( $params as $param_key => $param_value ) {
			if ( in_array( $param_key, $data_keys, true ) ) {
				$data[ $param_key ] = $param_value;
			}
		}

		return $data;
	}

	/**
	 * Only return writable props from schema.
	 *
	 * @param array $schema
	 *
	 * @return bool
	 */
	protected function filter_writable_props( $schema ) {
		return empty( $schema['readonly'] );
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
			'default'           => 50,
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
