<?php

namespace EverAccounting\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Class Users.
 *
 * Responsible for providing functionality related to users.
 *
 * @package EverAccounting\Services
 */
class Users extends Service {

	/**
	 * Users constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_user_data' ) );
	}

	/**
	 * Registers specific user data to the WordPress user API.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function register_user_data() {
		register_rest_field(
			'user',
			'ever_accounting_meta',
			array(
				'get_callback'    => array( __CLASS__, 'get_user_data_values' ),
				'update_callback' => array( __CLASS__, 'update_user_data_values' ),
				'schema'          => null,
			)
		);
	}

	/**
	 * Get user data values.
	 *
	 * @param array            $user_data User data.
	 * @param string           $field_name Field name.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.1.6
	 * @return array
	 */
	public static function get_user_data_values( $user_data, $field_name, $request ) {
		$user_id = $user_data['id'];
		$meta    = get_user_meta( $user_id, 'ever_accounting_meta', true );
		if ( ! $meta ) {
			$meta = array();
		}

		return $meta;
	}

	/**
	 * Update user data values.
	 *
	 * @param array            $user_data User data.
	 * @param string           $field_name Field name.
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @since 1.1.6
	 * @return array
	 */
	public static function update_user_data_values( $user_data, $field_name, $request ) {
		$user_id = $user_data['id'];
		$meta    = get_user_meta( $user_id, 'ever_accounting_meta', true );
		if ( ! $meta ) {
			$meta = array();
		}
		$meta_value = $request->get_param( $field_name );
		// if the value is empty, delete the meta. Otherwise, update it.
		if ( empty( $meta_value ) && isset( $meta[ $field_name ] ) ) {
			unset( $meta[ $field_name ] );
		} else {
			$meta[ $field_name ] = $meta_value;
		}

		update_user_meta( $user_id, 'ever_accounting_meta', $meta );

		return $meta;
	}
}
