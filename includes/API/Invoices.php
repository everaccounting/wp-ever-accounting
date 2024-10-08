<?php

namespace EverAccounting\API;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoices
 *
 * @since 2.0.0
 * @package EverAccounting\API
 */
class Invoices extends Documents {


	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @since 2.0.0
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Invoice', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'              => array(
					'description' => __( 'Unique identifier for the invoice.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'number'          => array(
					'description' => __( 'Invoice number.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'required'    => true,
				),
				'status'          => array(
					'description' => __( 'Status of the invoice.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'enum'        => array( 'draft', 'sent', 'paid', 'void' ),
					'context'     => array( 'view', 'edit' ),
					'default'     => 'draft',
				),
				'contact_id'      => array(
					'description' => __( 'Contact ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'default'     => 0,
				),
				'billing'         => array(
					'description' => __( 'Billing address.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'properties'  => array(
						'name'       => array(
							'description' => __( 'Name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'company'    => array(
							'description' => __( 'Company.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'address'    => array(
							'description' => __( 'Address.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'city'       => array(
							'description' => __( 'City.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'state'      => array(
							'description' => __( 'State.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'zip'        => array(
							'description' => __( 'Zip.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'country'    => array(
							'description' => __( 'Country.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'phone'      => array(
							'description' => __( 'Phone.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'email'      => array(
							'description' => __( 'Email.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'tax_number' => array(
							'description' => __( 'Tax number.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
				'items'           => array(
					'description' => __( 'Invoice items.', 'wp-ever-accounting' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'          => array(
								'description' => __( 'Item ID.', 'wp-ever-accounting' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'name'        => array(
								'description' => __( 'Item name.', 'wp-ever-accounting' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'description' => array(
								'description' => __( 'Item description.', 'wp-ever-accounting' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'item_id'     => array(
								'description' => __( 'Item ID.', 'wp-ever-accounting' ),
								'type'        => 'mixed',
								'context'     => array( 'view', 'edit' ),
							),
							'quantity'    => array(
								'description' => __( 'Item quantity.', 'wp-ever-accounting' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
							),
							'unit'        => array(
								'description' => __( 'Item unit.', 'wp-ever-accounting' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'price'       => array(
								'description' => __( 'Item price per unit.', 'wp-ever-accounting' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'taxable'     => array(
								'description' => __( 'Item taxable.', 'wp-ever-accounting' ),
								'type'        => 'boolean',
								'context'     => array( 'view', 'edit' ),
								'default'     => true,
							),
							'taxes'       => array(
								'description' => __( 'Item taxes.', 'wp-ever-accounting' ),
								'type'        => 'array',
								'context'     => array( 'view', 'edit' ),
								'items'       => array(
									'type'       => 'object',
									'properties' => array(
										'id'       => array(
											'description' => __( 'Tax ID.', 'wp-ever-accounting' ),
											'type'        => 'integer',
											'context'     => array( 'view', 'edit' ),
											'readonly'    => true,
										),
										'tax_id'   => array(
											'description' => __( 'Tax ID.', 'wp-ever-accounting' ),
											'type'        => 'integer',
											'context'     => array( 'view', 'edit' ),
										),
										'name'     => array(
											'description' => __( 'Tax name.', 'wp-ever-accounting' ),
											'type'        => 'string',
											'context'     => array( 'view', 'edit' ),
										),
										'rate'     => array(
											'description' => __( 'Tax rate.', 'wp-ever-accounting' ),
											'type'        => 'number',
											'context'     => array( 'view', 'edit' ),
										),
										'amount'   => array(
											'description' => __( 'Tax amount.', 'wp-ever-accounting' ),
											'type'        => 'number',
											'context'     => array( 'view', 'edit' ),
										),
										'compound' => array(
											'description' => __( 'Compound tax.', 'wp-ever-accounting' ),
											'type'        => 'boolean',
											'context'     => array( 'view', 'edit' ),
										),
									),
								),
							),
							'subtotal'    => array(
								'description' => __( 'Item subtotal.', 'wp-ever-accounting' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'tax_total'   => array(
								'description' => __( 'Item tax.', 'wp-ever-accounting' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'total'       => array(
								'description' => __( 'Item total.', 'wp-ever-accounting' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
						),
					),
				),
				'taxes'           => array(
					'description' => __( 'Invoice taxes.', 'wp-ever-accounting' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'       => array(
								'description' => __( 'Tax ID.', 'wp-ever-accounting' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'tax_id'   => array(
								'description' => __( 'Tax ID.', 'wp-ever-accounting' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
							),
							'name'     => array(
								'description' => __( 'Tax name.', 'wp-ever-accounting' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'rate'     => array(
								'description' => __( 'Tax rate.', 'wp-ever-accounting' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'amount'   => array(
								'description' => __( 'Tax amount.', 'wp-ever-accounting' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'compound' => array(
								'description' => __( 'Compound tax.', 'wp-ever-accounting' ),
								'type'        => 'boolean',
								'context'     => array( 'view', 'edit' ),
							),
						),
					),
				),
				'vat_exempt'      => array(
					'description' => __( 'VAT exempt.', 'wp-ever-accounting' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'default'     => false,
				),
				'subtotal'        => array(
					'description' => __( 'Subtotal.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'edit' ),
				),
				'tax_total'       => array(
					'description' => __( 'Tax total.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'edit' ),
				),
				'discount_total'  => array(
					'description' => __( 'Discount total.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'edit' ),
				),
				'discount_tax'    => array(
					'description' => __( 'Discount tax.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'edit' ),
				),
				'total_tax'       => array(
					'description' => __( 'Total tax.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'edit' ),
				),
				'total'           => array(
					'description' => __( 'Total.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'edit' ),
				),
				'discount_amount' => array(
					'description' => __( 'Discount.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'edit' ),
				),
				'discount_type'   => array(
					'description' => __( 'Discount type.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'enum'        => array( 'fixed', 'percentage' ),
					'context'     => array( 'view', 'edit' ),
				),
				'issue_date'      => array(
					'description' => __( 'Issue date.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'due_date'        => array(
					'description' => __( 'Due date.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'sent_date'       => array(
					'description' => __( 'Sent date.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'payment_date'    => array(
					'description' => __( 'Payment date.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'reference'       => array(
					'description' => __( 'Reference.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'note'            => array(
					'description' => __( 'Note.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'currency'        => array(
					'description' => __( 'Currency code.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'exchange_rate'   => array(
					'description' => __( 'Exchange rate.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'edit' ),
				),
				'created_via'     => array(
					'description' => __( 'Created via.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'creator_id'      => array(
					'description' => __( 'Creator ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'uuid'            => array(
					'description' => __( 'Unique identifier for the resource.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'uuid',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'updated_at'      => array(
					'description' => __( 'The date the invoice was last updated, in the site\'s timezone.', 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'created_at'      => array(
					'description' => __( 'The date the invoice was created, in the site\'s timezone.', 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $schema;
	}
}
