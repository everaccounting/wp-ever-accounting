<?php

namespace EverAccounting\API;

defined( 'ABSPATH' ) || exit;

/**
 * Class PaymentsController
 *
 * @since 0.0.1
 * @package EverAccounting\API
 */
abstract class DocumentsController extends Controller {
	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @since 1.1.2
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Document', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'             => array(
					'description' => __( 'Unique identifier for the document.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'status'         => array(
					'description' => __( 'Status of this Document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'number'         => array(
					'description' => __( 'Document number.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'contact_id'      => array(
					'description' => __( 'Contact ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'items_total'         => array(
					'description' => __( 'Total Item of the document.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'discount_total'         => array(
					'description' => __( 'Total Discount of the document.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'shipping_total'         => array(
					'description' => __( 'Total Shipping of the document.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'fees_total'         => array(
					'description' => __( 'Total Fees of the document.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'tax_total'         => array(
					'description' => __( 'Total TAX of the document.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'total'         => array(
					'description' => __( 'Total document.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'total_paid'         => array(
					'description' => __( 'Total Paid of the document.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'balance'         => array(
					'description' => __( 'Balance of the document.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'discount_type'         => array(
					'description' => __( 'Discount type of the document.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'discount_amount'     => array(
					'description' => __( 'Discount amount of the document.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'billing_name' => array(
					'description' => __( 'Billing name.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'billing_company' => array(
					'description' => __( 'Company name.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'billing_address_1' => array(
					'description' => __( 'Address 1.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'billing_address_2' => array(
					'description' => __( 'Address 2.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'billing_city' => array(
					'description' => __( 'City', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'billing_state' => array(
					'description' => __( 'State', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'billing_postcode' => array(
					'description' => __( 'Postcode', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'billing_country' => array(
					'description' => __( 'Country', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'billing_email' => array(
					'description' => __( 'Email', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'billing_phone' => array(
					'description' => __( 'Phone', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'billing_vat_number' => array(
					'description' => __( 'Vat number', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'reference'      => array(
					'description' => __( 'Reference of the document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'note'           => array(
					'description' => __( 'Note of the document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'tax_inclusive'           => array(
					'description' => __( 'Tax inclusive of the document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'vat_exempt'           => array(
					'description' => __( 'Vat exempt of the document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'issue_date'     => array(
					'description' => __( "The date the document was created, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'due_date'     => array(
					'description' => __( "The Due date the document, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'sent_date'     => array(
					'description' => __( "The sent date the document, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'payment_date'     => array(
					'description' => __( "The Payment date the document, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'currency_code'       => array(
					'description' => __( 'Currency code of the document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'enum'        => array_keys( eac_get_currencies() ),
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'exchange_rate'  => array(
					'description' => __( 'Conversion rate of the document.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'parent_id'      => array(
					'description' => __( 'Parent document ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'uuid'           => array(
					'description' => __( 'Unique identifier for the document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'created_via'           => array(
					'description' => __( 'Created via of document.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'creator_id'      => array(
					'description' => __( 'Creator ID of this document.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'date_updated'     => array(
					'description' => __( "update date of the document, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'date_created'     => array(
					'description' => __( "update date of the document, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		/**
		 * Filters the category's schema.
		 *
		 * @param array $schema Item schema data.
		 *
		 * @since 1.2.1
		 */
		$schema = apply_filters( 'ever_accounting_rest_category_item_schema', $schema );

		return $this->add_additional_fields_schema( $schema );
	}
}
