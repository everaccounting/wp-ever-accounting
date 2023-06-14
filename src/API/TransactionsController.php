<?php

namespace EverAccounting\API;

defined( 'ABSPATH' ) || exit;

/**
 * Class TransactionsController
 *
 * @since 0.0.1
 * @package EverAccounting\API
 */
abstract class TransactionsController {

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @since 1.1.2
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Transaction', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'              => array(
					'description' => __( 'Unique identifier for the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'type'            => array(
					'description' => __( 'Type of transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'enum'        => array_keys( eac_get_transaction_types() ),
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'number'          => array(
					'description' => __( 'Transaction number.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'date'            => array(
					'description' => __( 'The date the transaction took place, in the site\'s timezone.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'amount'          => array(
					'description' => __( 'Total amount of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'reference'       => array(
					'description' => __( 'Reference of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'note'            => array(
					'description' => __( 'Note of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'account'         => array(
					'description' => __( 'Account of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'embed', 'edit' ),
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Unique identifier for the account.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'embed', 'edit' ),
							'readonly'    => true,
							'required'    => true,
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'name' => array(
							'description' => __( 'Account name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'embed', 'edit' ),
						),
					),
				),
				'document'        => array(
					'description' => __( 'Document of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'embed', 'edit' ),
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Unique identifier for the document.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'embed', 'edit' ),
							'readonly'    => true,
							'required'    => true,
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'name' => array(
							'description' => __( 'Document name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'embed', 'edit' ),
						),
					),
				),
				'category'        => array(
					'description' => __( 'Category of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'embed', 'edit' ),
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Unique identifier for the category.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'embed', 'edit' ),
							'readonly'    => true,
							'required'    => true,
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'name' => array(
							'description' => __( 'Category name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'embed', 'edit' ),
						),
					),
				),
				'contact'         => array(
					'description' => __( 'Contact of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'embed', 'edit' ),
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Unique identifier for the contact.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'embed', 'edit' ),
							'readonly'    => true,
							'required'    => true,
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'name' => array(
							'description' => __( 'Contact name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'embed', 'edit' ),
						),
					),
				),
				'payment_method'  => array(
					'description' => __( 'Payment method of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'enum'        => array_keys( eac_get_payment_methods() ),
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'attachment'      => array(
					'description' => __( 'Attachment of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'embed', 'edit' ),
					'properties'  => array(
						'id'  => array(
							'description' => __( 'Unique identifier for the attachment.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'embed', 'edit' ),
							'readonly'    => true,
							'required'    => true,
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'url' => array(
							'description' => __( 'Attachment URL.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'embed', 'edit' ),
						),
					),
				),
				'currency'        => array(
					'description' => __( 'Currency code of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'enum'        => array_keys( eac_get_currencies() ),
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'conversion_rate' => array(
					'description' => __( 'Conversion rate of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'number',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'reconciled'      => array(
					'description' => __( 'Whether the transaction is reconciled.', 'wp-ever-accounting' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'parent_id'       => array(
					'description' => __( 'Parent transaction ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'creator'         => array(
					'description' => __( 'User who created the transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'embed', 'edit' ),
					'properties'  => array(
						'id'   => array(
							'description' => __( 'Unique identifier for the user.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'embed', 'edit' ),
							'readonly'    => true,
							'required'    => true,
							'arg_options' => array(
								'sanitize_callback' => 'intval',
							),
						),
						'name' => array(
							'description' => __( 'Display name for the user.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'embed', 'edit' ),
						),
					),
				),
				'uuid'            => array(
					'description' => __( 'Unique identifier for the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
				),
				'updated_at'      => array(
					'description' => __( "The date the transaction was last modified, in the site's timezone.", 'wp-ever-accounting' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
				),
				'created_at'      => array(
					'description' => __( "The date the transaction was created, in the site's timezone.", 'wp-ever-accounting' ),
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
