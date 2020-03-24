<?php
defined( 'ABSPATH' ) || exit();

function eaccounting_register_setting( $option_group, $option_name, $args = array() ) {
	global $eaccounting_settings;

	$defaults = array(
		'type'              => 'string',
		'group'             => $option_group,
		'description'       => '',
		'sanitize_callback' => null,
		'show_in_rest'      => false,
	);

	$args = apply_filters( 'eaccounting_register_setting_args', $args, $defaults, $option_group, $option_name );
	$args = wp_parse_args( $args, $defaults );

	if ( ! is_array( $eaccounting_settings ) ) {
		$eaccounting_settings = array();
	}

	if ( ! empty( $args['sanitize_callback'] ) ) {
		add_filter( "eaccounting_sanitize_option_{$option_name}", $args['sanitize_callback'] );
	}
	if ( array_key_exists( 'default', $args ) ) {
		add_filter( "eaccounting_default_option_{$option_name}", 'filter_default_option', 10, 3 );
	}

	$eaccounting_settings[ $option_name ] = $args;
}

/**
 * Register default settings available in plugin.
 *
 * since 1.0.0
 */
function eaccounting_register_initial_settings() {
	eaccounting_register_setting(
		'general',
		'ea_company_name',
		array(
			'show_in_rest' => array(
				'name' => 'company_name',
			),
			'type'         => 'string',
			'description'  => __( 'Company name.' ),
			'default'      => site_url(),
		)
	);

	eaccounting_register_setting(
		'general',
		'ea_company_phone',
		array(
			'show_in_rest' => array(
				'name' => 'company_phone',
			),
			'type'         => 'string',
			'description'  => __( 'Company phone.' ),
			'default'      => '',
		)
	);

	eaccounting_register_setting(
		'general',
		'ea_company_email',
		array(
			'show_in_rest' => array(
				'name'   => 'company_email',
				'schema' => array(
					'format' => 'email',
				),
			),
			'type'         => 'string',
			'description'  => __( 'Company email.' ),
			'default'      => get_option( 'admin_email' ),
		)
	);

	eaccounting_register_setting(
		'general',
		'ea_date_format',
		array(
			'show_in_rest' => array(
				'name'   => 'date_format',
				'schema' => array(
					'format' => 'date',
				),
			),
			'type'         => 'string',
			'default'      => get_option( 'date_format' ),
			'description'  => __( 'A date format for all date strings.' ),
		)
	);

	eaccounting_register_setting(
		'general',
		'ea_time_format',
		array(
			'show_in_rest' => array(
				'name'   => 'time_format',
				'schema' => array(
					'format' => 'time',
				),
			),
			'type'         => 'string',
			'default'      => get_option( 'time_format' ),
			'description'  => __( 'A time format for all time strings.' ),
		)
	);

	eaccounting_register_setting(
		'general',
		'ea_start_of_week',
		array(
			'show_in_rest' => array(
				'name'   => 'start_of_week',
				'schema' => array(
					'format' => 'string',
				),
			),
			'type'         => 'integer',
			'default'      => get_option( 'start_of_week' ),
			'description'  => __( 'A day number of the week that the week should start on.' ),
		)
	);

	eaccounting_register_setting(
		'general',
		'ea_company_logo',
		array(
			'show_in_rest' => array(
				'name'   => 'logo',
				'schema' => array(
					'format' => 'uri',
				),
			),
			'type'         => 'string',
			'default'      => '',
			'description'  => __( 'Logo URL.' ),
		)
	);

}


/**
 * Retrieves an array of registered settings.
 *
 * since 1.0.0
 * @return array
 */
function eaccounting_get_registered_settings() {
	global $eaccounting_settings;

	if ( ! is_array( $eaccounting_settings ) ) {
		return array();
	}

	return $eaccounting_settings;
}

/**
 * Retrieve plugin settings
 * since 1.0.0
 *
 * @param $name
 *
 * @return bool|mixed|void
 */
function eaccounting_get_setting( $name ) {
	$settings = eaccounting_get_registered_settings();
	$name     = ltrim( $name, 'ea_' );
	$name     = 'ea_' . $name;
	if ( ! array_key_exists( $name, $settings ) ) {
		return false;
	}

	$setting = $settings[ $name ];
	$default = isset( $setting['default'] ) ? $setting['default'] : false;

	return get_option( $name, $default );
}
