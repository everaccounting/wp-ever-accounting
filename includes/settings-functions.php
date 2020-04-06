<?php
defined( 'ABSPATH' ) || exit();

function eaccounting_get_setting_options( $section = null ) {
	$setting_options = apply_filters( 'eaccounting_setting_options', array(
		'company_name'           => [
			'type'    => 'string',
			'default' => wp_parse_url( site_url(), PHP_URL_HOST ),
		],
		'company_phone'          => [],
		'company_email'          => [
			'default' => get_option( 'admin_email' ),
		],
		'logo_id'                => [
			'type' => 'integer',
		],
		'company_tax_number'     => [],
		'company_address'        => [],
		'company_city'           => [],
		'company_state'          => [],
		'company_postcode'       => [],
		'company_country'        => [
			'default' => 'US',
		],
		'default_account_id'     => [
			'type'    => 'integer',
			'section' => 'defaults',
		],
		'default_currency_code'  => [
			'default' => 'USD',
			'section' => 'defaults',
		],
		'default_payment_method' => [
			'default' => 'check',
			'section' => 'defaults',
		],
		'default_tax_rate_id'    => [
			'type'    => 'integer',
			'section' => 'defaults',
		],
		'invoice_number_prefix'  => [
			'default' => 'INV-',
			'section' => 'invoice',
		],
		'invoice_number_digit'  => [
			'default' => '5',
			'section' => 'invoice',
		],
	) );

	$defaults = array(
		'type'              => 'string',
		'section'           => 'general',
		'description'       => '',
		'sanitize_callback' => null,
		'show_in_rest'      => true,
	);

	$settings = array();

	foreach ( $setting_options as $name => $args ) {
		if ( empty( $name ) || array_key_exists( $name, $settings ) ) {
			continue;
		}
		$args = apply_filters( 'eaccounting_setting_option_args', $args, $defaults, $name );
		if ( ! empty( $args['sanitize_callback'] ) ) {
			add_filter( "eaccounting_setting_option_sanitize_{$name}", $args['sanitize_callback'] );
		}

		$settings[ $name ] = wp_parse_args( $args, $defaults );
	}

	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	if ( $section !== null ) {
		return wp_list_filter( $settings, [ 'section' => sanitize_key( $section ) ] );
	}

	return $settings;
}

/**
 * A central function for getting settings option of the plugin
 * it saves data in the table with ea_ prefix but can be called without ea_
 * since 1.0.0
 *
 * @param $name
 *
 * @return bool|mixed|void
 */
function eaccounting_get_option( $name ) {
	$options  = eaccounting_get_setting_options();
	$raw_name = preg_replace( '/^ea_/', '', $name, 1 );
	if ( ! array_key_exists( $raw_name, $options ) ) {
		return false;
	}

	$option = $options[ $raw_name ];
	$name   = 'ea_' . $raw_name;

	$default = isset( $option['default'] ) ? $option['default'] : false;

	return apply_filters( "eaccounting_settings_option_$raw_name", get_option( $name, $default ) );
}

/**
 * A central function for setting settings option of the plugin
 * it saves data in the table with ea_ prefix but can be called without ea_
 * since 1.0.0
 *
 * @param $name
 * @param $value
 *
 * @return bool
 */
function eaccounting_set_option( $name, $value ) {
	$options  = eaccounting_get_setting_options();
	$raw_name = preg_replace( '/^ea_/', '', $name, 1 );
	if ( ! array_key_exists( $raw_name, $options ) ) {
		return false;
	}
	$option = $options[ $name ];
	$name   = 'ea_' . $raw_name;
	if ( ! empty( $option['sanitize_callback'] ) ) {
		$value = call_user_func( $option['sanitize_callback'], $value );
	}

	return update_option( $name, $value );
}



//
///**
// * Retrieve plugin settings
// * since 1.0.0
// *
// * @param $name
// *
// * @return bool|mixed|void
// */
//function eaccounting_get_settings( $name ) {
//	$settings = eaccounting_get_registered_settings();
//	$raw_name = ltrim( $name, 'ea_' );
//	$name     = 'ea_' . $raw_name;
//	if ( ! array_key_exists( $name, $settings ) ) {
//		return false;
//	}
//	$setting = $settings[ $name ];
//	$default = isset( $setting['default'] ) ? $setting['default'] : false;
//
//	return apply_filters( "eaccounting_settings_field_$raw_name", get_option( $name, $default ) );
//}
//
///**
// * since 1.0.0
// *
// * @param $currency
// *
// * @return array
// */
//function eaccounting_get_settings_field_currency( $currency ) {
//	if ( is_numeric( $currency ) ) {
//		return (array) eaccounting_get_currency( $currency );
//	}
//
//	return $currency;
//}
//
//add_filter( 'eaccounting_settings_field_currency', 'eaccounting_get_settings_field_currency' );
//
//
///**
// * @return array|object|void|null
// * @since 1.0.2
// */
//function eaccounting_get_default_currency() {
//	$default_currency = (string) eaccounting_get_settings( 'default_currency' );
//	$currency         = (object) eaccounting_get_currency_config( $default_currency );
//
//	return $currency;
//}
//
///**
// * @return array|object|void|null
// * @since 1.0.2
// */
//function eaccounting_get_default_account() {
//	$default_account = eaccounting_get_settings( 'default_account' );
//	if ( empty( $default_account ) ) {
//		global $wpdb;
//
//		return $wpdb->get_row( "SELECT * FROM $wpdb->ea_accounts order by id ASC limit 1" );
//	}
//
//	return eaccounting_get_account( $default_account );
//}
