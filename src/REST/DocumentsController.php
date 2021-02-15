<?php
/**
 * Accounts Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Models\Document;

defined( 'ABSPATH' ) || die();

abstract class DocumentsController extends EntitiesController {
	/**
	 * Route base.
	 *
	 * @var string
	 *
	 * @since 1.1.0
	 */
	protected $rest_base = 'documents';

	/**
	 * Entity model class.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $entity_model = Document::class;

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

		return eaccounting_get_documents( $query_args );
	}


	/**
	 * Retrieves the items's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 * @since 1.1.0
	 *
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Invoice', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'              => array(
					'description' => __( 'Unique identifier for the Document.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'document_number' => array(
					'description' => __( 'Number of Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
					'required'    => true,
				),
				'order_number'    => array(
					'description' => __( 'Order Number of Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly'    => true,
					'required'    => false,
				),
				'status'          => array(
					'description' => __( 'Status of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'default'     => 'draft',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'issue_date'      => array(
					'description' => __( 'Issue Date of Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'due_date'        => array(
					'description' => __( 'Due Date of Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'payment_date'    => array(
					'description' => __( 'Payment Date of Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'category_id'     => array(
					'description' => __( 'Category id of the Document.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_filed',
					),
					'required'    => true,
//					'properties'  => array(
//						'id'   => array(
//							'description' => __( 'Category ID.', 'wp-ever-accounting' ),
//							'type'        => 'integer',
//							'context'     => array( 'view', 'edit' ),
//							'readonly'    => true,
//						),
//						'type' => array(
//							'description' => __( 'Category Type.', 'wp-ever-accounting' ),
//							'type'        => 'string',
//							'context'     => array( 'view', 'edit' ),
//						),
//					),
				),
				'contact_id'      => array(
					'description' => __( 'Contact id of the Document.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
//					'properties'  => array(
//						'id' => array(
//							'description' => __( 'Contact ID.', 'wp-ever-accounting' ),
//							'type'        => 'integer',
//							'context'     => array( 'view', 'edit' ),
//							'readonly'    => true,
//						),
//					),
				),
				'discount'        => array(
					'description' => __( 'Discount of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'discount_type'   => array(
					'description' => __( 'Discount type of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => 'percentage',
					'enum'        => array( 'percentage', 'fixed' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'subtotal' => array(
					'description' => __( 'Subtotal of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'total_tax'           => array(
					'description' => __( 'Total Tax of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'total_discount'           => array(
					'description' => __( 'Total Discount of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'total_fees'           => array(
					'description' => __( 'Total Fees of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'total_shipping'      => array(
					'description' => __( 'Total Shipping of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'total'         => array(
					'description' => __( 'Total of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'tax_inclusive'         => array(
					'description' => __( 'Total of the Document.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => false,
				),
				'note'               => array(
					'description' => __( 'Note for the Document', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
				),
				'terms'               => array(
					'description' => __( 'Terms for the Document', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
				),
				'attachment_id'         => array(
					'description' => __( 'Attachment id of the Document', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
//					'properties'  => array(
//						'id'   => array(
//							'description' => __( 'Attachment ID.', 'wp-ever-accounting' ),
//							'type'        => 'integer',
//							'context'     => array( 'view', 'edit' ),
//							'readonly'    => true,
//						),
//						'src'  => array(
//							'description' => __( 'Attachment src.', 'wp-ever-accounting' ),
//							'type'        => 'string',
//							'context'     => array( 'view', 'edit' ),
//						),
//						'name' => array(
//							'description' => __( 'Attachment Name.', 'wp-ever-accounting' ),
//							'type'        => 'string',
//							'context'     => array( 'view', 'edit' ),
//						),
//					),
				),
				'currency_code' => array(
					'description' => __( 'Currency code of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
//					'properties'  => array(
//						'id'   => array(
//							'description' => __( 'Currency code ID.', 'wp-ever-accounting' ),
//							'type'        => 'integer',
//							'context'     => array( 'view', 'edit' ),
//							'readonly'    => true,
//						),
//						'code' => array(
//							'description' => __( 'Currency code.', 'wp-ever-accounting' ),
//							'type'        => 'string',
//							'context'     => array( 'view', 'edit' ),
//						),
//
//					),
				),
				'currency_rate' => array(
					'description' => __( 'Currency rate of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'key' => array(
					'description' => __( 'Key of the Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'parent_id'            => array(
					'description' => __( 'Creator of the Document', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'creator_id'            => array(
					'description' => __( 'Creator of the Document', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
//					'properties'  => array(
//						'id'    => array(
//							'description' => __( 'Creator ID.', 'wp-ever-accounting' ),
//							'type'        => 'integer',
//							'context'     => array( 'view', 'edit' ),
//							'readonly'    => true,
//						),
//						'name'  => array(
//							'description' => __( 'Creator name.', 'wp-ever-accounting' ),
//							'type'        => 'string',
//							'context'     => array( 'view', 'edit' ),
//						),
//						'email' => array(
//							'description' => __( 'Creator Email.', 'wp-ever-accounting' ),
//							'type'        => 'string',
//							'context'     => array( 'view', 'edit' ),
//						),
//					),
				),
				'date_created'       => array(
					'description' => __( 'Created date of the Document.', 'wp-ever-accounting' ),
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
	 *
	 */
	public function get_collection_params() {
		$query_params                       = parent::get_collection_params();
		$query_params['context']['default'] = 'view';

		$params['orderby'] = array(
			'description'       => __( 'Sort collection by object attribute.', 'wp-ever-accounting' ),
			'type'              => 'string',
			'default'           => 'id',
			'enum'              => array(
				'name',
				'id',
				'total',
				'issue_date',
				'due_date',
				'status',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $query_params;
	}
}
