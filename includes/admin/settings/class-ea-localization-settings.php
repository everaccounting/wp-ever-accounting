<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Localisation_Settings {

	/**
	 * EAccounting_Localisation_Settings constructor.
	 */
	public function __construct() {
		add_filter( 'ever_accounting_settings_sections', array( $this, 'add_section' ) );
		add_filter( 'ever_accounting_settings_fields', array( $this, 'add_fields' ) );
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
				'id'    => 'localisation',
				'title' => __( 'Localisation', 'wp-ever-accounting' )

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
		$localisation_fields = array(
			'localisation' => array(
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
					'default'     => get_option('admin_email'),
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
					'default'     => '',
					'type'        => 'text',
				),
			)
		);

		return array_merge($fields, $localisation_fields);
	}

}

new EAccounting_Localisation_Settings();
