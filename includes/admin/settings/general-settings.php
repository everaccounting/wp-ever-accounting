<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_General_Settings extends EAccounting_Settings_Page {

	/**
	 * @since 1.0.2
	 * EAccounting_General_Settings constructor.
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', 'wp-ever-accounting' );
		parent::__construct();
	}

	public function get_settings( $section = null ) {
		$settings = array(
			array(
				'title' => __( 'Company Settings', 'wp-ever-accounting' ),
				'type'  => 'title',
				'desc'  => __( 'This is where your business is located. Input your business details.', 'wp-ever-accounting' ),
				'id'    => 'company_section',
			),
			array(
				'id'          => 'company_name',
				'title'       => __( 'Company Name', 'wp-ever-accounting' ),
				'tooltip'     => __( 'Your company name', 'wp-ever-accounting' ),
				'placeholder' => __( 'XYZ Company', 'wp-ever-accounting' ),
				'type'        => 'text',
			),
			array(
				'id'          => 'company_address',
				'title'       => __( 'Address', 'wp-ever-accounting' ),
				'tooltip'     => __( 'Your company address', 'wp-ever-accounting' ),
				'placeholder' => __( 'xyz street, golden suite', 'wp-ever-accounting' ),
				'type'        => 'text',
			),
			array(
				'id'          => 'company_state',
				'title'       => __( 'State', 'wp-ever-accounting' ),
				'tooltip'     => __( 'Your company state', 'wp-ever-accounting' ),
				'placeholder' => __( 'Newyork', 'wp-ever-accounting' ),
				'type'        => 'text',
			),
			array(
				'id'          => 'company_postcode',
				'title'       => __( 'Postcode', 'wp-ever-accounting' ),
				'tooltip'     => __( 'Your company postcode', 'wp-ever-accounting' ),
				'placeholder' => __( '654321', 'wp-ever-accounting' ),
				'type'        => 'text',
			),
			array(
				'id'      => 'company_country',
				'title'   => __( 'Country', 'wp-ever-accounting' ),
				'tooltip' => __( 'Your company country', 'wp-ever-accounting' ),
				'options' => eaccounting_get_countries(),
				'class'   => 'ea-select2-control',
				'type'    => 'select',
			),
			array(
				'id'      => 'file',
				'title'   => __( 'File', 'wp-ever-accounting' ),
				'desc'    => __( 'File description', 'wp-ever-accounting' ),
				'type'    => 'file',
				'default' => '',
				'options' => array(
					'button_label' => 'Choose Image'
				)
			),
			array(
				'type' => 'sectionend',
				'id'   => 'company_section',
			),
			array(
				'title' => __( 'Currency options', 'wp-ever-accounting' ),
				'type'  => 'title',
				'desc'  => __( 'The following options affect how prices are displayed.', 'wp-ever-accounting' ),
				'id'    => 'currency_section',
			),
			array(
				'id'      => 'currency',
				'title'   => __( 'Currency', 'wp-ever-accounting' ),
				//'tooltip' => __( 'Your company country', 'wp-ever-accounting' ),
				'options' => wp_list_pluck( eaccounting_get_global_currencies(), 'name'),
				'class'   => 'ea-select2-control',
				'type'    => 'select',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'currency_section',
			),

		);

		return apply_filters( 'wpcp_get_settings_' . $this->id, $settings );
	}

}

return new EAccounting_General_Settings();
