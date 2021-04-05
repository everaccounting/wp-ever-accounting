<?php
/**
 * Bills Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Abstracts\Documents_Controller;
use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || die();

class Bills_Controller extends Documents_Controller {
	/**
	 * Route base.
	 *
	 * @var string
	 *
	 * @since 1.1.0
	 */
	protected $rest_base = 'bills';

	/**
	 * Entity type.
	 *
	 * @var string
	 * @since 1.1.1
	 */
	protected $entity_type = 'bill';

	/**
	 * Entity model class.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $entity_model = Bill::class;

	/**
	 * Get objects.
	 *
	 * @param array $query_args Query args.
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return array|int|\WP_Error
	 * @since  1.1.0
	 *
	 */
	protected function get_objects( $query_args, $request ) {
		$query_args['category_id']   = $request['category_id'];
		$query_args['contact_id']    = $request['contact_id'];
		$query_args['discount_type'] = $request['discount_type'];
		$query_args['currency_code'] = $request['currency_code'];

		// Set before into date query. Date query must be specified as an array of an array.
		if ( isset( $request['before'] ) ) {
			$args['payment_date'][0]['before'] = $request['before'];
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( isset( $request['after'] ) ) {
			$args['payment_date'][0]['after'] = $request['after'];
		}

		return eaccounting_get_bills( $query_args );
	}

	/**
	 * Retrieves the items's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 *
	 * @since   1.1.2
	 *
	 */
	public function get_item_schema() {
		$schema = parent::get_item_schema();

		$schema['properties']['line_item'] = array(
			'description' => __( 'Items of Document.', 'wp-ever-accounting' ),
			'type'        => 'object',
			'context'     => array( 'embed', 'view', 'edit' ),
			'properties'  => array(
				'id'         => array(
					'description' => __( 'Item ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'sale_price' => array(
					'description' => __( 'Item Sale Price.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'quantity'   => array(
					'description' => __( 'Item Quantity.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'default'     => 1,
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				)
			)
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
