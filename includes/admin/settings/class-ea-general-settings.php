<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_General_Settings {

	/**
	 * EAccounting_General_Settings constructor.
	 */
	public function __construct() {
		add_filter( 'eaccounting_settings_sections', array( $this, 'add_section' ) );
		add_filter( 'eaccounting_settings_fields', array( $this, 'add_fields' ) );
	}

	/**
	 * Add sections
	 *
	 * @param $sections
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function add_section( $sections ) {
		$section = array(
			array(
				'id'    => 'eaccounting_general',
				'title' => __( 'General', 'wp-ever-accounting' )
			)
		);

		return array_merge( $sections, $section );
	}

	/**
	 * Add fields
	 *
	 * @param $fields
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function add_fields( $fields ) {
		$general_fields = array(
			'eaccounting_general' => array(
				array(
					'name'    => 'logo',
					'label'   => __( 'Logo', 'wp-ever-accounting' ),
					'default' => '',
					'type'    => 'file',
				),
				array(
					'name'        => 'name',
					'label'       => __( 'Name', 'wp-ever-accounting' ),
					'placeholder' => __( 'XYZ Company', 'wp-ever-accounting' ),
					'default'     => '',
					'type'        => 'text',
				),
				array(
					'name'        => 'email',
					'label'       => __( 'Email', 'wp-ever-accounting' ),
					'placeholder' => __( 'email@domain.com', 'wp-ever-accounting' ),
					'default'     => get_option( 'admin_email' ),
					'type'        => 'text',
				),
				array(
					'name'        => 'tax_number',
					'label'       => __( 'Tax Number', 'wp-ever-accounting' ),
					'placeholder' => __( 'xxxxxxx', 'wp-ever-accounting' ),
					'default'     => '',
					'type'        => 'text',
				),
				array(
					'name'        => 'phone',
					'label'       => __( 'Phone Number', 'wp-ever-accounting' ),
					'placeholder' => __( '0987654321', 'wp-ever-accounting' ),
					'default'     => '',
					'type'        => 'text',
				),
				array(
					'name'        => 'address',
					'label'       => __( 'Address', 'wp-ever-accounting' ),
					'placeholder' => __( '', 'wp-ever-accounting' ),
					'default'     => '',
					'type'        => 'text',
				),
				array(
					'name'        => 'city',
					'label'       => __( 'City', 'wp-ever-accounting' ),
					'placeholder' => __( '', 'wp-ever-accounting' ),
					'default'     => '',
					'type'        => 'text',
				),
				array(
					'name'        => 'state',
					'label'       => __( 'State', 'wp-ever-accounting' ),
					'placeholder' => __( '', 'wp-ever-accounting' ),
					'default'     => '',
					'type'        => 'text',
				),
				array(
					'name'        => 'postcode',
					'label'       => __( 'Postcode', 'wp-ever-accounting' ),
					'placeholder' => __( '', 'wp-ever-accounting' ),
					'default'     => '',
					'type'        => 'text',
				),
				array(
					'name'        => 'country',
					'label'       => __( 'Country', 'wp-ever-accounting' ),
					'placeholder' => __( '', 'wp-ever-accounting' ),
					'default'     => 'US',
					'type'        => 'select',
					'options'     => eaccounting_get_countries(),
				),
			)
		);

		return array_merge( $fields, $general_fields );
	}

}

new EAccounting_General_Settings();
