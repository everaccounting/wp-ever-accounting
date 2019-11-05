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
				'id'    => 'eaccounting_localisation',
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
			'eaccounting_localisation' => array(
				array(
					'name'        => 'financial_start',
					'label'       => __( 'Financial Year Start', 'wp-ever-accounting' ),
					'placeholder' => __( 'XYZ Company', 'wp-ever-accounting' ),
					'default'     => '01-01',
					'type'        => 'text',
				),
				array(
					'name'        => 'percent_position',
					'label'       => __( 'Percent (%) Position', 'wp-ever-accounting' ),
					'default'     => 'after',
					'type'        => 'select',
					'options' => array(
						'before' => __('Before Number', 'wp-ever-accounting'),
						'after' => __('After Number', 'wp-ever-accounting'),
					)
				),
				array(
					'name'    => 'currency_position',
					'label'   => __( 'Currency Position', 'wp-ever-hrm' ),
					'type'    => 'select',
					'options' => array(
						'before' => __( 'Before - $10', 'wp-ever-hrm' ),
						'after'  => __( 'After - 10$', 'wp-ever-hrm' ),
					),
				),
				array(
					'name'    => 'thousand_separator',
					'label'   => __( 'Thousands Separator', 'wp-ever-hrm' ),
					'type'    => 'text',
					'default' => ',',
				),
				array(
					'name'    => 'decimal_separator',
					'label'   => __( 'Decimal Separator', 'wp-ever-hrm' ),
					'type'    => 'text',
					'default' => '.',
				),
			)
		);

		return array_merge($fields, $localisation_fields);
	}

}

new EAccounting_Localisation_Settings();
