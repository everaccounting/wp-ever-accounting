<?php
/**
 * Categories Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Abstracts\Entities_Controller;
use EverAccounting\Models\Category;

defined( 'ABSPATH' ) || die();

/**
 * Class CategoriesController
 *
 * @package EverAccounting\REST
 */
class Categories_Controller extends Entities_Controller {
	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'categories';

	/**
	 * Entity type.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $entity_type = 'category';

	/**
	 * Entity model class.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $entity_model = Category::class;

	/**
	 * Get objects.
	 *
	 * @param array            $query_args Query args.
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return array|int|\WP_Error
	 * @since  1.1.0
	 */
	protected function get_objects( $query_args, $request ) {
		$query_args['type'] = $request['type'];

		return eaccounting_get_categories( $query_args );
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
			'title'      => __( 'Category', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'           => array(
					'description' => __( 'Unique identifier for the category.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'name'         => array(
					'description' => __( 'Name of the category.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'type'         => array(
					'description' => __( 'Type of the category.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'required'    => true,
					'enum'        => array_keys( eaccounting_get_category_types() ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'color'        => array(
					'description' => __( 'Color of the category.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_hex_color',
					),
				),
				'enabled'      => array(
					'description' => __( 'Status of the item.', 'wp-ever-accounting' ),
					'type'        => 'boolean',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'date_created' => array(
					'description' => __( 'Created date of the account.', 'wp-ever-accounting' ),
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
		$query_params = array_merge(
			parent::get_collection_params(),
			array(
				'orderby' => array(
					'description' => __( 'Sort collection by transaction attribute.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'default'     => 'id',
					'enum'        => array(
						'name',
						'id',
						'type',
						'color',
						'enabled',
					),
				),
				'type'    => array(
					'description'       => __( 'Limit results to those matching types.', 'wp-ever-accounting' ),
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => 'rest_validate_request_arg',
				),
			)
		);

		return $query_params;
	}
}
