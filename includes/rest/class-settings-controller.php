<?php
/**
 * Settings Rest Controller Class.
 *
 * @since       1.0.2
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || die();

/**
 * Class SettingsController
 *
 * @package EverAccounting\REST
 */
class Settings_Controller extends REST_Controller {
	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'settings';

	/**
	 * Register routes.
	 *
	 * @since 1.5.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_options' ),
					'permission_callback' => array( $this, 'get_options_permissions_check' ),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_options' ),
					'permission_callback' => array( $this, 'update_options_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\w-]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for settings option.', 'wp-ever-accounting' ),
						'type'        => 'string',
					),
				),
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_option' ),
					'permission_callback' => array( $this, 'get_options_permissions_check' ),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_option' ),
					'permission_callback' => array( $this, 'update_options_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Makes sure the current user has access to READ the settings APIs.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|boolean
	 * @since  1.5.0
	 */
	public function get_options_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_eaccounting' ) ) {
			return new \WP_Error( 'eaccounting_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get all settings groups items.
	 *
	 * @param \WP_REST_Request $request Request data.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 * @since  1.5.0
	 */
	public function get_options( $request ) {
		$settings          = $this->get_settings();
		$filtered_settings = array();
		foreach ( $settings as $setting ) {
			$rest_field = $this->prepare_item_for_response( $setting, $request );
			$rest_field = $this->prepare_response_for_collection( $rest_field );
			if ( $this->is_setting_type_valid( $rest_field['type'] ) ) {
				$filtered_settings[] = $rest_field;
			}
		}

		return rest_ensure_response( $filtered_settings );
	}

	/**
	 * Return a single setting.
	 *
	 * @param \WP_REST_Request $request Request data.
	 * @return\WP_Error|\WP_REST_Response
	 *
	 * @since  1.5.0
	 */
	public function get_option( $request ) {
		$settings = $this->get_settings();
		if ( ! array_key_exists( $request['id'], $settings ) ) {
			return new \WP_Error( 'rest_setting_field_invalid', __( 'Invalid setting field.', 'wp-ever-accounting' ), array( 'status' => 404 ) );
		}

		$setting = $settings[ $request['id'] ];

		$response = $this->prepare_item_for_response( $setting, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Makes sure the current user has access to WRITE the settings APIs.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|boolean
	 * @since  1.5.0
	 */
	public function update_options_permissions_check( $request ) {
		if ( ! current_user_can( 'ea_manage_options' ) ) {
			return new \WP_Error( 'rest_cannot_edit', __( 'Sorry, you cannot edit this resource.', 'wp-ever-accounting' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}


	/**
	 * Update a single setting in a group.
	 *
	 * @param \WP_REST_Request $request Request data.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 * @since  1.5.0
	 */
	public function update_option( $request ) {
		$settings = $this->get_settings();
		if ( ! array_key_exists( $request['id'], $settings ) ) {
			return new \WP_Error( 'rest_setting_field_invalid', __( 'Invalid setting field.', 'wp-ever-accounting' ), array( 'status' => 404 ) );
		}

		$setting = $settings[ $request['id'] ];
		if ( is_callable( array( $this, 'validate_setting_' . $setting['type'] . '_field' ) ) ) {
			$value = $this->{'validate_setting_' . $setting['type'] . '_field'}( $request['value'], $setting );
		} else {
			$value = eaccounting_clean( $request['value'] );
		}

		if ( is_wp_error( $value ) ) {
			return $value;
		}

		eaccounting_update_option( $request['id'], $value );
		$response = $this->prepare_item_for_response( $setting, $request );

		return rest_ensure_response( $response );
	}


	/**
	 * Update options.
	 *
	 * @param \WP_REST_Request $request Request body
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function update_options( $request ) {
		$options  = $request->get_body_params();
		$settings = $this->get_settings();
		foreach ( $options as $option => $value ) {
			if ( ! array_key_exists( $option, $settings ) ) {
				return new \WP_Error( 'rest_setting_field_invalid', __( 'Invalid setting field.', 'wp-ever-accounting' ), array( 'status' => 404 ) );
			}

			$setting = $settings[ $option ];
			if ( is_callable( array( $this, 'validate_setting_' . $setting['type'] . '_field' ) ) ) {
				$value = $this->{'validate_setting_' . $setting['type'] . '_field'}( $value, $setting );
			} else {
				$value = eaccounting_clean( $value );
			}

			if ( is_wp_error( $value ) ) {
				return $value;
			}
			eaccounting_update_option( $option, $value );
		}

		$settings = $this->get_settings();
		$response = $this->prepare_item_for_response( (object) $settings, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Get all settings options.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings          = Settings::instance()->get_settings();
		$filtered_settings = array();
		$whitelisted       = array();

		foreach ( $settings as $tab => $setting ) {
			if ( ! empty( $setting['fields'] ) ) {
				$setting['sections']['']['fields'] = $setting['fields'];
			}
			// Bail if no sections.
			if ( empty( $setting['sections'] ) ) {
				continue;
			}

			foreach ( $setting['sections'] as $section_id => $section ) {
				// Bail if no fields.
				if ( empty( $section['fields'] ) ) {
					continue;
				}

				foreach ( $section['fields'] as $field ) {
					// Bail if no fields.
					if ( empty( $field['id'] ) ) {
						continue;
					}
					// Restrict duplicate.
					if ( in_array( $field['id'], $whitelisted, true ) ) {
						continue;
					}
					$rest_field            = $field;
					$rest_field['tab']     = $tab;
					$rest_field['section'] = $section_id;
					$rest_field['value']   = eaccounting_get_option( $field['id'] );
					if ( $this->is_setting_type_valid( $rest_field['type'] ) ) {
						$filtered_settings[ $field['id'] ] = $rest_field;
					}
				}
			}
		}

		return $filtered_settings;
	}

	/**
	 * Boolean for if a setting type is a valid supported setting type.
	 *
	 * @param string $type Type.
	 *
	 * @return bool
	 * @since  1.5.0
	 */
	public function is_setting_type_valid( $type ) {
		return in_array(
			$type,
			array(
				'text',
				'email',
				'number',
				'color',
				'password',
				'textarea',
				'select',
				'multiselect',
				'radio',
				'checkbox',
			),
			true
		);
	}

	/**
	 * Prepare a single setting object for response.
	 *
	 * @param object           $item Setting object.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response $response Response data.
	 * @since  1.5.0
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data     = $this->filter_setting( (array) $item );
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, empty( $request['context'] ) ? 'view' : $request['context'] );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $data['id'] ) );

		return $response;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param string $setting_id Setting ID.
	 *
	 * @return array Links for the given setting.
	 * @since  1.5.0
	 */
	protected function prepare_links( $setting_id ) {
		$base  = str_replace( '(?P<group_id>[\w-]+)', $setting_id, $this->rest_base );
		$links = array(
			'self' => array(
				'href' => rest_url( sprintf( '/%s/%s/%s', $this->namespace, $base, $setting_id ) ),
			),
		);

		return $links;
	}


	/**
	 * Callback for allowed keys for each setting response.
	 *
	 * @param string $key Key to check.
	 *
	 * @return boolean
	 * @since  1.5.0
	 */
	public function allowed_setting_keys( $key ) {
		return in_array(
			$key,
			array(
				'id',
				'label',
				'description',
				'default',
				'tip',
				'placeholder',
				'type',
				'options',
				'value',
				'tab',
				'section',
			),
			true
		);
	}

	/**
	 * Filters out bad values from the settings array/filter so we
	 * only return known values via the API.
	 *
	 * @param array $setting Settings.
	 *
	 * @return array
	 * @since 1.5.0
	 */
	public function filter_setting( $setting ) {
		$setting = array_intersect_key(
			$setting,
			array_flip( array_filter( array_keys( $setting ), array( $this, 'allowed_setting_keys' ) ) )
		);

		return $setting;
	}

	/**
	 * Get the settings schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'setting',
			'type'       => 'object',
			'properties' => array(
				'id'          => array(
					'description' => __( 'A unique identifier for the setting.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_title',
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'tab'         => array(
					'description' => __( 'An identifier for the field this setting belongs to tab.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_title',
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'section'     => array(
					'description' => __( 'An identifier for the feild this setting belongs to section.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_title',
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'label'       => array(
					'description' => __( 'A human readable label for the setting used in interfaces.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'description' => array(
					'description' => __( 'A human readable description for the setting used in interfaces.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'value'       => array(
					'description' => __( 'Setting value.', 'wp-ever-accounting' ),
					'type'        => 'mixed',
					'context'     => array( 'view', 'edit' ),
				),
				'default'     => array(
					'description' => __( 'Default value for the setting.', 'wp-ever-accounting' ),
					'type'        => 'mixed',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'tip'         => array(
					'description' => __( 'Additional help text shown to the user about the setting.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'placeholder' => array(
					'description' => __( 'Placeholder text to be displayed in text inputs.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'type'        => array(
					'description' => __( 'Type of setting.', 'wp-ever-accounting' ),
					'type'        => 'string',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					'context'     => array( 'view', 'edit' ),
					'enum'        => array(
						'text',
						'email',
						'number',
						'color',
						'password',
						'textarea',
						'select',
						'multiselect',
						'radio',
						'image_width',
						'checkbox',
					),
					'readonly'    => true,
				),
				'options'     => array(
					'description' => __( 'Array of options (key value pairs) for inputs such as select, multiselect, and radio buttons.', 'wp-ever-accounting' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}
