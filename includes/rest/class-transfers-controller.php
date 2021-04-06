<?php
/**
 * Transfers Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Abstracts\Entities_Controller;
use EverAccounting\Models\Transfer;

defined( 'ABSPATH' ) || die();

/**
 * Class Transfers_Controller
 *
 * @package EverAccounting\REST
 */
class Transfers_Controller extends Entities_Controller {
	/**
	 * Entity type.
	 *
	 * @since 1.1.4
	 *
	 * @var string
	 */
	protected $entity_type = 'transfer';

	/**
	 * Route base.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $rest_base = 'transfers';
	/**
	 * Entity model class.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $entity_model = Transfer::class;

	/**
	 * Get objects.
	 *
	 * @param array            $query_args Query args.
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return array|int|\WP_Error
	 *
	 * @since  1.1.0
	 */
	protected function get_objects( $query_args, $request ) {
		return eaccounting_get_transfers( $query_args );
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
			'title'      => __( 'Transfer', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'             => array(
					'description' => __( 'Unique identifier for the transfer.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'absint',
					),
				),
				'from_account'   => array(
					'description' => __( 'From Account of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => true,
					'properties'  => array(
						'id'   => array(
							'description' => __( 'From Account ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'embed', 'view', 'edit' ),
							'arg_options' => array(
								'sanitize_callback' => 'absint',
							),
						),
						'name' => array(
							'description' => __( 'From Account name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'embed', 'view', 'edit' ),
							'arg_options' => array(
								'sanitize_callback' => 'sanitize_text_field',
							),
						),
					),
				),
				'to_account'     => array(
					'description' => __( 'To Account ID of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => true,
					'properties'  => array(
						'id'   => array(
							'description' => __( 'To Account ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'embed', 'view', 'edit' ),
							'arg_options' => array(
								'sanitize_callback' => 'absint',
							),
						),
						'name' => array(
							'description' => __( 'To Account name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'embed', 'view', 'edit' ),
							'arg_options' => array(
								'sanitize_callback' => 'sanitize_text_field',
							),
						),
					),
				),
				'amount'         => array(
					'description' => __( 'Amount of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'date'           => array(
					'description' => __( 'Date of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => true,
				),
				'payment_method' => array(
					'description' => __( 'Payment method of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'income_id'      => array(
					'description' => __( 'Income ID of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => false,
				),
				'expense_id'     => array(
					'description' => __( 'Expense ID of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => false,
				),
				// 'creator'      => array(
				// 'description' => __( 'Creator of the transfer', 'wp-ever-accounting' ),
				// 'type'        => 'object',
				// 'context'     => array( 'view', 'edit' ),
				// 'properties'  => array(
				// 'id'    => array(
				// 'description' => __( 'Creator ID.', 'wp-ever-accounting' ),
				// 'type'        => 'integer',
				// 'context'     => array( 'view', 'edit' ),
				// 'readonly'    => true,
				// ),
				// 'name'  => array(
				// 'description' => __( 'Creator name.', 'wp-ever-accounting' ),
				// 'type'        => 'string',
				// 'context'     => array( 'view', 'edit' ),
				// ),
				// 'email' => array(
				// 'description' => __( 'Creator Email.', 'wp-ever-accounting' ),
				// 'type'        => 'string',
				// 'context'     => array( 'view', 'edit' ),
				// ),
				// ),
				// ),
				'date_created'   => array(
					'description' => __( 'Created date of the transaction.', 'wp-ever-accounting' ),
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
	 * @since 1.1.0
	 */
	public function get_collection_params() {
		$query_params = array_merge(
			parent::get_collection_params(),
			array(
				'orderby'         => array(
					'description' => __( 'Sort collection by transaction attribute.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'default'     => 'id',
					'enum'        => array(
						'id',
						'income_id',
						'expense_id',
						'date_created',
					),
				),
			)
		);

		return $query_params;
	}
}
