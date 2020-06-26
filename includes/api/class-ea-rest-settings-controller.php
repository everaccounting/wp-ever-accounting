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
					'callback'            => array( $this, 'get_items' ),
					'args'                => array(),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_items' ),
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
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return array|WP_Error Array on success, or WP_Error object on failure.
	 * @since 1.0.0
	 *
	 */
	public function get_items( $request ) {
		$section  = isset( $request['section'] ) ? $request['section'] : null;
		$options  = $this->get_registered_options( $section );
		$response = array();

		foreach ( $options as $name => $args ) {

			switch ( $name ) {
				case 'default_account_id':
					$response['default_account'] = eaccounting_get_default_account();
					break;
				case 'logo_id':
					$logo_id          = (int) eaccounting_get_option( 'logo_id' );
					$response['logo'] = empty( $logo_id ) ? [] : self::get_rest_object( 'files', $logo_id );
					break;
				default:
					$response[ $name ] = apply_filters( 'eaccounting_rest_pre_get_setting', null, $name, $args );
					if ( is_null( $response[ $name ] ) ) {
						// Default to a null value as "null" in the response means "not set".
						$response[ $name ] = eaccounting_get_option( $args['option_name'] );
						$response[ $name ] = $this->prepare_value( $response[ $name ], $args['schema'] );
					}
			}

		}

		return $response;
	}

	/**
	 * Prepares a value for output based off a schema array.
	 *
	 * @param mixed $value Value to prepare.
	 * @param array $schema Schema to match.
	 *
	 * @return mixed The prepared value.
	 * @since 1.0.0
	 *
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
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return array|WP_Error Array on success, or error object on failure.
	 * @since 1.0.0
	 *
	 */
	public function update_items( $request ) {
		$section = isset( $request['section'] ) ? $request['section'] : null;
		$options = $this->get_registered_options( $section );

		$params = $request->get_params();

		foreach ( $options as $name => $args ) {
			if ( ! array_key_exists( $name, $params ) ) {
				continue;
			}

			if($name ='default_currency'){
				eaccounting_set_default_currency( $request[ $name ] );
			}

			$updated = apply_filters( 'eaccounting_rest_pre_update_setting', false, $name, $request[ $name ], $args );

			if ( $updated ) {
				break;
			}

			if ( is_null( $request[ $name ] ) ) {
				delete_option( $args['option_name'] );
			} else {
				update_option( $args['option_name'], $request[ $name ] );
			}
			break;

		}

		return $this->get_items( $request );
	}

	/**
	 * Retrieves all of the registered options for the Settings API.
	 *
	 * @return array Array of registered options.
	 * @since 1.0.0
	 *
	 */
	protected function get_registered_options( $section = null ) {
		$rest_options = array();

		foreach ( eaccounting_get_setting_options() as $name => $args ) {
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
			$rest_args['option_name'] = "ea_{$name}";
			$rest_args['section']     = $args['section'];
			// Skip over settings that don't have a defined type in the schema.
			if ( empty( $rest_args['schema']['type'] ) ) {
				continue;
			}

			/*
			 * Whitelist the supported types for settings, as we don't want invalid types
			 * to be updated with arbitrary values that we can't do decent sanitizing for.
			 */
			if ( ! in_array( $rest_args['schema']['type'], array(
				'number',
				'integer',
				'string',
				'boolean',
				'array',
				'object'
			), true ) ) {
				continue;
			}

			$rest_args['schema'] = $this->set_additional_properties_to_false( $rest_args['schema'] );

			$rest_options[ $rest_args['name'] ] = $rest_args;

		}


		if ( $section !== null ) {
			return wp_list_filter( $rest_options, [ 'section' => sanitize_key( $section ) ] );
		}

		return $rest_options;
	}

	/**
	 * Retrieves the site setting schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 * @since 1.0.0
	 *
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
	 * @param mixed $value The value for the setting.
	 * @param WP_REST_Request $request The request object.
	 * @param string $param The parameter name.
	 *
	 * @return mixed|WP_Error
	 * @since 1.0.0
	 *
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
	 * @param array $schema The schema array.
	 *
	 * @return array
	 * @since 1.0.0
	 *
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
