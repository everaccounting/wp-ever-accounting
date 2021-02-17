<?php
/**
 * Transfers Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Models\Transfer;

defined( 'ABSPATH' ) || die();

class TransfersController extends EntitiesController {
	/**
	 * Route base.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 *
	 */
	protected $rest_base = 'transfers';

	/**
	 * Entity type.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 *
	 */
	protected $entity_type = 'transfer';

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
	 * @param array $query_args Query args.
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return array|int|\WP_Error
	 * @since  1.1.0
	 *
	 */
	protected function get_objects( $query_args, $request ) {
		return eaccounting_get_transfers( $query_args );
	}

	/**
	 * Retrieves the item's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 *
	 * @since 1.1.0
	 *
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Transfer', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'              => array(
					'description' => __( 'Unique identifier for the transfer.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'from_account' => array(
					'description' => __( 'From Account of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
					'readonly'    => true,
					'properties'  => array(
						'id'   => array(
							'description' => __( 'From Account ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'name' => array(
							'description' => __( 'From Account name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
				'to_account'   => array(
					'description' => __( 'To Account ID of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
					'readonly'    => true,
					'properties'  => array(
						'id'   => array(
							'description' => __( 'To Account ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'name' => array(
							'description' => __( 'To Account name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
				'amount'          => array(
					'description' => __( 'Amount of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'date'            => array(
					'description' => __( 'Date of the transaction', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => true,
				),
				'payment_method'  => array(
					'description' => __( 'Payment method of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'income_id'       => array(
					'description' => __( 'Income ID of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly' => false,
					'required'    => false,
				),
				'expense_id'      => array(
					'description' => __( 'Expense ID of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'readonly' => false,
					'required'    => false,
				),
				'reference'       => array(
					'description' => __( 'Reference of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'required'    => false,
				),
				'description'     => array(
					'description' => __( 'Reference of the transaction.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'required'    => false,
				),
				'creator'         => array(
					'description' => __( 'Creator of the transfer', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'readonly' => true,
					'properties'  => array(
						'id'    => array(
							'description' => __( 'Creator ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'name'  => array(
							'description' => __( 'Creator name.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'email' => array(
							'description' => __( 'Creator Email.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
				'date_created'    => array(
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
	 *
	 * @since 1.1.0
	 *
	 */
	public function get_collection_params() {
		$query_params                       = parent::get_collection_params();
		$query_params['context']['default'] = 'view';

		$query_params['orderby'] = array(
			'description'       => __( 'Sort collection by object attribute.', 'wp-ever-accounting' ),
			'type'              => 'string',
			'default'           => 'id',
			'enum'              => array(
				'id',
				'income_id',
				'expense_id',
				'date_created'
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $query_params;
	}

	/**
	 * Prepare a single object for create or update.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return ResourceModel|\WP_Error Data object or WP_Error.
	 * @since 1.1.0
	 *
	 */
	public function prepare_object_for_database( &$object, $request ) {
		$object->set_date( $request['date'] );
		$object->set_from_account_id( $request['from_account']['id'] );
		$object->set_amount( $request['amount'] );
		$object->set_to_account_id( $request['to_account']['id'] );
		$object->set_income_id( $request['income_id'] );
		$object->set_expense_id( $request['expense_id'] );
		$object->set_payment_method( $request['payment_method'] );
		$object->set_reference( $request['reference'] );
		$object->set_description( $request['description'] );
		$object->set_creator_id( $request['creator']['id'] );
		$object->set_date_created( $request['date_created'] );

		return $object;
	}
}
