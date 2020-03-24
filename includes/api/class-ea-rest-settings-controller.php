<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Settings_Controller extends EAccounting_REST_Controller {
	/**
	 * @var string
	 */
	protected $namespace = 'ea/v1';

	/**
	 * @var string
	 */
	protected $rest_base = 'settings';

	/**
	 * @since 1.0.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'args'                => array(),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Retrieves the settings.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return array|WP_Error Array on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		$options  = $this->get_registered_options();
		$response = array();

		foreach ( $options as $name => $args ) {
			$response[ $name ] = apply_filters( 'eaccounting_rest_pre_get_setting', null, $name, $args );

			if ( is_null( $response[ $name ] ) ) {
				// Default to a null value as "null" in the response means "not set".
				$response[ $name ] = get_option( $args['option_name'], $args['schema']['default'] );
			}
			$response[ $name ] = $this->prepare_value( $response[ $name ], $args['schema'] );
		}

		return $response;
	}

	/**
	 * Prepares a value for output based off a schema array.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value  Value to prepare.
	 * @param array $schema Schema to match.
	 * @return mixed The prepared value.
	 */
	protected function prepare_value( $value, $schema ) {
		/*
		 * If the value is not valid by the schema, set the value to null.
		 * Null values are specifically non-destructive, so this will not cause
		 * overwriting the current invalid value to null.
		 */
		if ( is_wp_error( rest_validate_value_from_schema( $value, $schema ) ) ) {
			return null;
		}
		return rest_sanitize_value_from_schema( $value, $schema );
	}


	/**
	 * Updates settings for the settings object.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return array|WP_Error Array on success, or error object on failure.
	 */
	public function update_item( $request ) {
		$options = $this->get_registered_options();

		$params = $request->get_params();

		foreach ( $options as $name => $args ) {
			if ( ! array_key_exists( $name, $params ) ) {
				continue;
			}

			$updated = apply_filters( 'eaccounting_rest_pre_update_setting', false, $name, $request[ $name ], $args );

			if ( $updated ) {
				continue;
			}

			if ( is_null( $request[ $name ] ) ) {
				if ( is_wp_error( rest_validate_value_from_schema( get_option( $args['option_name'], false ), $args['schema'] ) ) ) {
					return new WP_Error(
						'rest_invalid_stored_value',
						sprintf( __( 'The %s property has an invalid stored value, and cannot be updated to null.' ), $name ),
						array( 'status' => 500 )
					);
				}
				delete_option( $args['option_name'] );
			} else {
				update_option( $args['option_name'], $request[ $name ] );
			}
		}

		return $this->get_item( $request );
	}

	/**
	 * Retrieves all of the registered options for the Settings API.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of registered options.
	 */
	protected function get_registered_options() {
		$rest_options = array();

		foreach ( eaccounting_get_registered_settings() as $name => $args ) {
			if ( empty( $args['show_in_rest'] ) ) {
				continue;
			}

			$rest_args = array();

			if ( is_array( $args['show_in_rest'] ) ) {
				$rest_args = $args['show_in_rest'];
			}

			$defaults = array(
				'name'   => ! empty( $rest_args['name'] ) ? $rest_args['name'] : $name,
				'schema' => array(),
			);

			$rest_args = array_merge( $defaults, $rest_args );

			$default_schema = array(
				'type'        => empty( $args['type'] ) ? null : $args['type'],
				'description' => empty( $args['description'] ) ? '' : $args['description'],
				'default'     => isset( $args['default'] ) ? $args['default'] : null,
			);

			$rest_args['schema']      = array_merge( $default_schema, $rest_args['schema'] );
			$rest_args['option_name'] = $name;

			// Skip over settings that don't have a defined type in the schema.
			if ( empty( $rest_args['schema']['type'] ) ) {
				continue;
			}

			/*
			 * Whitelist the supported types for settings, as we don't want invalid types
			 * to be updated with arbitrary values that we can't do decent sanitizing for.
			 */
			if ( ! in_array( $rest_args['schema']['type'], array( 'number', 'integer', 'string', 'boolean', 'array', 'object' ), true ) ) {
				continue;
			}

			$rest_args['schema'] = $this->set_additional_properties_to_false( $rest_args['schema'] );

			$rest_options[ $rest_args['name'] ] = $rest_args;
		}

		return $rest_options;
	}

	/**
	 * Retrieves the site setting schema, conforming to JSON Schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$options = $this->get_registered_options();

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'settings',
			'type'       => 'object',
			'properties' => array(),
		);

		foreach ( $options as $option_name => $option ) {
			$schema['properties'][ $option_name ]                = $option['schema'];
			$schema['properties'][ $option_name ]['arg_options'] = array(
				'sanitize_callback' => array( $this, 'sanitize_callback' ),
			);
		}

		$this->schema = $schema;
		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Custom sanitize callback used for all options to allow the use of 'null'.
	 *
	 * By default, the schema of settings will throw an error if a value is set to
	 * `null` as it's not a valid value for something like "type => string". We
	 * provide a wrapper sanitizer to whitelist the use of `null`.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed           $value   The value for the setting.
	 * @param WP_REST_Request $request The request object.
	 * @param string          $param   The parameter name.
	 * @return mixed|WP_Error
	 */
	public function sanitize_callback( $value, $request, $param ) {
		if ( is_null( $value ) ) {
			return $value;
		}
		return rest_parse_request_arg( $value, $request, $param );
	}

	/**
	 * Recursively add additionalProperties = false to all objects in a schema.
	 *
	 * This is need to restrict properties of objects in settings values to only
	 * registered items, as the REST API will allow additional properties by
	 * default.
	 *
	 * @since 4.9.0
	 *
	 * @param array $schema The schema array.
	 * @return array
	 */
	protected function set_additional_properties_to_false( $schema ) {
		switch ( $schema['type'] ) {
			case 'object':
				foreach ( $schema['properties'] as $key => $child_schema ) {
					$schema['properties'][ $key ] = $this->set_additional_properties_to_false( $child_schema );
				}
				$schema['additionalProperties'] = false;
				break;
			case 'array':
				$schema['items'] = $this->set_additional_properties_to_false( $schema['items'] );
				break;
		}

		return $schema;
	}
}
