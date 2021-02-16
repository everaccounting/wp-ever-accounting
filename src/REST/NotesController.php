<?php
/**
 * Notes Rest Controller Class.
 *
 * @since       1.1.1
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Models\Note;

defined( 'ABSPATH' ) || die();

class NotesController extends EntitiesController {
	/**
	 * Route base.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 *
	 */
	protected $rest_base = 'notes';

	/**
	 * Entity type.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 */
	protected $entity_type = "invoice";

	/**
	 * Entity model class.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 */
	protected $entity_model = Note::class;

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
		return eaccounting_get_notes( $query_args );
	}

	/**
	 * Retrieves the items schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 *
	 * @since 1.1.1
	 *
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Note', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'           => array(
					'description' => __( 'Unique identifier for the note.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'parent_id'    => array(
					'description' => __( 'Parent id of the account.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'type'         => array(
					'description' => __( 'Type of the account.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'note'         => array(
					'description' => __( 'Note of the account', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'required'    => true,
				),
				'extra'        => array(
					'description' => __( 'Extra content of the account', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view' ),
					'default'     => '0',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'required'    => false,
				),
				'creator_id'   => array(
					'description' => __( 'Creator of the account', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
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
				'parent_id',
				'id',
				'type',
			),
			'validate_callback' => 'rest_validate_request_arg'
		);

		return $query_params;
	}
}
