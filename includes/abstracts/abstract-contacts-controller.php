<?php
/**
 * Contacts Rest Controller Class.
 *
 * @since       1.1.2
 * @subpackage  Abstracts
 * @package     EverAccounting
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || die();

abstract class Contacts_Controller extends Entities_Controller {
	/**
	 * Retrieves the items's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 * @since 1.1.2
	 *
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'Contact', 'wp-ever-accounting' ),
			'type'       => 'object',
			'properties' => array(
				'id'           => array(
					'description' => __( 'Unique identifier for the contact.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed', 'edit' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
				),
				'user_id'      => array(
					'description' => __( 'WP user ID.', 'wp-ever-accounting' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
					'arg_options' => array(
						'sanitize_callback' => 'intval',
					),
					'required'    => true,
				),
				'name'         => array(
					'description' => __( 'Name for the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'default'     => '',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => true,
				),
				'company'      => array(
					'description' => __( 'Company for the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'default'     => '',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'required'    => false,
				),
				'email'        => array(
					'description' => __( 'The email address for the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_email',
					),
				),
				'phone'        => array(
					'description' => __( 'Phone number for the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'website'      => array(
					'description' => __( 'website of the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'esc_url_raw',
					),
				),
				'birth_date'   => array(
					'description' => __( 'Birth date', 'wp-ever-accounting' ),
					'type'        => 'string',
					'format'      => 'date',
					'context'     => array( 'embed', 'view' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'vat_number'   => array(
					'description' => __( 'Vat number of the contact', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'street'       => array(
					'description' => __( 'Street Address of the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'state'        => array(
					'description' => __( 'State Address of the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'postcode'     => array(
					'description' => __( 'Postcode of the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
				'country'      => array(
					'description' => __( 'Country of the contact.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'context'     => array( 'embed', 'view', 'edit' ),
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_key',
					),
				),
				'currency'     => array(
					'description' => __( 'Currency code for customer.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view', 'edit' ),
					'required'    => true,
					'properties'  => array(
						'code' => array(
							'description' => __( 'Currency code', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'embed', 'view', 'edit' ),
							'enum'        => array_keys( eaccounting_get_global_currencies() ),
							'arg_options' => array(
								'sanitize_callback' => 'sanitize_text_field',
							),
						),
					),
				),
				'thumbnail'    => array(
					'description' => __( 'Thumbnail of the contact', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view', 'edit' ),
					'properties'  => array(
						'id'  => array(
							'description' => __( 'Thumbnail ID.', 'wp-ever-accounting' ),
							'type'        => 'integer',
							'context'     => array( 'embed', 'view', 'edit' ),
							'arg_options' => array(
								'sanitize_callback' => 'absint',
							),
						),
						'src' => array(
							'description' => __( 'Thumbnail src.', 'wp-ever-accounting' ),
							'type'        => 'string',
							'context'     => array( 'embed', 'view' ),
							'arg_options' => array(
								'sanitize_callback' => 'esc_url_raw',
							),
						),
					),
				),
				'enabled'      => array(
					'description' => __( 'Status of the contact.', 'wp-ever-accounting' ),
					'type'        => 'boolean',
					'context'     => array( 'embed', 'view', 'edit' ),
				),
				'creator'      => array(
					'description' => __( 'Creator of the contact.', 'wp-ever-accounting' ),
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
					'description' => __( 'Created date of the contact.', 'wp-ever-accounting' ),
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
	 * @since 1.1.2
	 *
	 */
	public function get_collection_params() {
		$query_params                       = parent::get_collection_params();
		$query_params['context']['default'] = 'view';
		$params['orderby']                  = array(
			'description'       => __( 'Sort collection by object attribute.', 'wp-ever-accounting' ),
			'type'              => 'string',
			'default'           => 'id',
			'enum'              => array(
				'name',
				'email',
				'phone',
				'type',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $query_params;
	}
}
