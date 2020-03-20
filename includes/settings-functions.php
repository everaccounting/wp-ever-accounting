<?php
defined( 'ABSPATH' ) || exit();

/**
 * Get settings from a section
 * since 1.0.0
 *
 * @param $section
 *
 * @return array
 */
function eaccounting_get_section_settings( $section ) {
	switch ( $section ) {
		case 'ea_general_settings':
			$site    = site_url();
			$host    = parse_url( $site )['host'];
			$default = [
				'company_name'       => $host,
				'company_phone'      => '',
				'company_email'      => get_option( 'admin_email' ),
				'company_address'    => '',
				'company_tax_number' => '',
				'company_logo'       => '',
			];
			break;
		default:
			$default = [];
	}

	$settings = wp_parse_args( get_option( $section, [] ), $default );

	return apply_filters( 'eaccounting_section_settings', $settings, $section );
}

/**
 * since 1.0.0
 *
 * @param $settings
 * @param $section
 *
 * @return array
 */
function eaccounting_update_section_settings( $settings, $section ) {
	$saved_settings  = eaccounting_get_section_settings( $section );
	$merged_settings = wp_parse_args( $settings, $saved_settings );

	update_option( $section, $merged_settings );

	return $merged_settings;
}

/**
 * since 1.0.0
 *
 * @param $field
 * @param bool $default
 * @param string $section
 *
 * @return string
 */
function eaccounting_get_settings( $field, $default = false, $section = 'ea_general_settings' ) {
	$section_settings = eaccounting_get_section_settings( $section );

	return array_key_exists( $field, $section_settings ) && isset( $section_settings[ $field ] ) ? $section_settings[ $field ] : $default;
}

/**
 * since 1.0.0
 *
 * @param $field
 * @param $value
 * @param string $section
 *
 * @return array
 */
function eaccounting_update_settings( $field, $value, $section = 'ea_general_settings' ) {
	$section_settings           = eaccounting_get_section_settings( $section );
	$section_settings[ $field ] = $value;

	return eaccounting_update_section_settings( $section_settings, $section );
}


