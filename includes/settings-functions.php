<?php
defined( 'ABSPATH' ) || exit();

function eaccounting_get_settings( $section ) {
	$site     = site_url();
	$host     = parse_url( $site )['host'];
	$settings = array(
		'ea_general_settings' => [
			[
				'name'    => 'company_name',
				'default' => $host,
			],
			[
				'name'    => 'company_email',
				'default' => get_option( 'admin_email' ),
			],
			[
				'name'    => 'company_address',
				'default' => ''
			],
			[
				'name'    => 'company_logo',
				'default' => ''
			]
		]
	);

	$section_settings = get_option( $section, [] );
	$section_defaults = array_key_exists( $section, $settings ) ? $settings[ $section ] : [];
	foreach ( $section_defaults as $field ) {
		if(array_key_exists($field['name'], $section_settings)){

		}
	}

}
