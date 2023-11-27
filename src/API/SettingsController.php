<?php

namespace EverAccounting\API;

use EverAccounting\Services\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * REST API Settings controller
 *
 * Handles requests to the /settings endpoints.
 *
 * @since   0.0.1
 * @package EverAccounting\API
 */
class SettingsController extends Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'settings';

	/**
	 * Registers the routes for the settings.
	 *
	 * @since 1.1.6
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'args'                => array(),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'args'                => $this->get_endpoint_args_for_item_schema( \WP_REST_Server::EDITABLE ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to read and manage settings.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.6
	 *
	 * @return bool True if the request has read access for the item, otherwise false.
	 */
	public function get_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Retrieves the settings.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.6
	 *
	 * @return array|\WP_Error Array on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$settings = $this->get_registered_settings();
		$response = array();
		foreach ( $settings as $key => $setting ) {
			$item             = $this->prepare_item_for_response( $setting, $request );
			$item             = $this->prepare_response_for_collection( $item );
			$response[ $key ] = $item;
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Updates settings for the settings object.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.1.6
	 *
	 * @return array|\WP_Error Array on success, or error object on failure.
	 */
	public function update_item( $request ) {
		$settings = $this->get_registered_settings();
		$params   = $request->get_params();
		error_log(print_r($params, true));
		foreach ( $settings as $setting ) {
			if ( ! array_key_exists( $setting['name'], $params ) ) {
				continue;
			}

			if ( is_null( $params[ $setting['name'] ] ) ) {
				return new \WP_Error(
					'rest_invalid_stored_value',
					/* translators: %s: Property name. */
					sprintf( __( 'The %s property has an invalid stored value, and cannot be updated to null.' ), $setting['name'] ),
					array( 'status' => 500 )
				);
				delete_option( $setting['option_key'] );
			} else {
				// Update the value.
				update_option( $setting['option_key'], $params[ $setting['name'] ] );
			}
		}

		return $this->get_items( $request );
	}

	/**
	 * Retrieves all the registered settings.
	 *
	 * @since 1.1.6
	 *
	 * @return array Array of registered options.
	 */
	protected function get_registered_settings() {
		$settings = array();
		$options  = Settings::instance()->get_settings();
		foreach ( $options as $option ) {
			if ( empty( $option['name'] ) ) {
				continue;
			}

			// Set defaults.
			$option['option_key'] = $option['option_key'] ?? 'eac_' . $option['name'];
			$option['type']       = $option['type'] ?? 'string';
			$option['default']    = $option['default'] ?? null;
			$option['schema']     = $option['schema'] ?? array();

			// Set schema defaults.
			$option['schema'] = wp_parse_args( $option['schema'], array(
				'type'        => null,
				'description' => $option['description'] ?? '',
				'default'     => $option['default'],
			) );

			if ( empty( $option['schema']['type'] ) ) {
				// Based on the input type, set the schema type.
				switch ( $option['type'] ) {
					case 'checkbox':
						$option['schema']['type'] = 'boolean';
						break;
					case 'number':
						$option['schema']['type'] = 'number';
						break;
					case 'multiselect':
						$option['schema']['type'] = array( 'array', 'object', null );
						break;
					default:
						$option['schema']['type'] = 'string';
						break;
				}
			}

			// Allow only certain types.
			if ( ! in_array( $option['schema']['type'], array( 'number', 'integer', 'string', 'boolean', 'array', 'object', 'mixed' ), true ) ) {
				continue;
			}

			// if option is an array, set schema enum to the array keys based on assoc or numeric array.
			if ( isset($option['options']) && is_array( $option['options'] ) && ! empty( $option['options'] ) ) {
				// check if the array is associative or numeric.
				$is_assoc                 = array_keys( $option['options'] ) !== range( 0, count( $option['options'] ) - 1 );
				$option['schema']['enum'] = $is_assoc ? array_keys( $option['options'] ) : array_values( $option['options'] );
			}

			$option['schema'] = rest_default_additional_properties_to_false( $option['schema'] );

			// Allow only certain keys.
			$allowed_keys = array( 'name', 'label', 'description', 'default', 'tip', 'placeholder', 'type', 'options', 'value', 'option_key', 'schema' );
			$settings[]   = array_intersect_key( $option, array_flip( $allowed_keys ) );
		}

		return $settings;
	}

	/**
	 * Prepare a single setting object for response.
	 *
	 * @param array            $item Setting object.
	 * @param \WP_REST_Request $request Request data.
	 *
	 * @since  1.1.6
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function prepare_item_for_response( $item, $request ) {
		$item['value'] = get_option( $item['option_key'], $item['default'] );
		// unset the option_key as it is not needed in the response.
		unset( $item['option_key'] );
		unset( $item['schema'] );
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $item, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		return rest_ensure_response( $data );
	}

	/**
	 * Custom sanitize callback used for all options to allow the use of 'null'.
	 *
	 * @param mixed            $value The value for the setting.
	 * @param \WP_REST_Request $request The request object.
	 * @param string           $param The parameter name.
	 *
	 * @since 1.1.6
	 *
	 * @return mixed|\WP_Error
	 */
	public function sanitize_callback( $value, $request, $param ) {
		if ( is_null( $value ) ) {
			return $value;
		}

		return rest_parse_request_arg( $value, $request, $param );
	}

	/**
	 * Retrieves the site setting schema, conforming to JSON Schema.
	 *
	 * @since 1.1.6
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$settings = $this->get_registered_settings();
		$schema   = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'settings',
			'type'       => 'object',
			'properties' => array(),
		);

		foreach ( $settings as $setting ) {
			$properties                               = wp_parse_args( $setting['schema'], array(
				'arg_options' => array(
					'sanitize_callback' => array( $this, 'sanitize_callback' ),
				),
			) );
			$schema['properties'][ $setting['name'] ] = $properties;
		}

		return $this->add_additional_fields_schema( $schema );
	}
}
