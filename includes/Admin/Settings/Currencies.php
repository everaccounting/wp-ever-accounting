<?php

namespace EverAccounting\Admin\Settings;

/**
 * Class Currencies.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\Settings
 */
class Currencies extends Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'currencies', __( 'Currencies', 'wp-ever-accounting' ) );
	}

	/**
	 * Get settings tab sections.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_sections() {
		return array(
			''           => __( 'Options', 'wp-ever-accounting' ),
			'currencies' => __( 'Currencies', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Get settings.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_default_section_settings() {
		return array(
			// currency options.
			array(
				'title' => __( 'Currency Options', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'currency_options',
			),
			// currency.
			array(
				'title'    => __( 'Base Currency', 'wp-ever-accounting' ),
				'desc'     => __( 'The base currency of your business.Currency can not be changed once you have recorded any transaction.', 'wp-ever-accounting' ),
				'id'       => 'eac_base_currency',
				'type'     => 'select',
				'default'  => 'USD',
				'class'    => 'eac_select2',
				'options'  => wp_list_pluck( eac_get_currencies(), 'formatted_name', 'code' ),
				'value'    => get_option( 'eac_base_currency', 'USD' ),
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Currency Position', 'wp-ever-accounting' ),
				'desc'     => __( 'The position of the currency symbol.', 'wp-ever-accounting' ),
				'id'       => 'eac_currency_position',
				'type'     => 'select',
				'default'  => 'before',
				'options'  => array(
					'before' => __( 'Before', 'wp-ever-accounting' ),
					'after'  => __( 'After', 'wp-ever-accounting' ),
				),
				'desc_tip' => true,
			),
			array(
				'title'       => __( 'Thousand Separator', 'wp-ever-accounting' ),
				'desc'        => __( 'The character used to separate thousands.', 'wp-ever-accounting' ),
				'id'          => 'eac_thousand_separator',
				'type'        => 'text',
				'placeholder' => ',',
				'default'     => ',',
				'desc_tip'    => true,
				'css'         => 'width: 80px;',
			),
			array(
				'title'       => __( 'Decimal Separator', 'wp-ever-accounting' ),
				'desc'        => __( 'The character used to separate decimals.', 'wp-ever-accounting' ),
				'id'          => 'eac_decimal_separator',
				'type'        => 'text',
				'placeholder' => '.',
				'default'     => '.',
				'desc_tip'    => true,
				'css'         => 'width: 80px;',
			),
			array(
				'title'       => __( 'Currency Precision', 'wp-ever-accounting' ),
				'desc'        => __( 'The number of decimal places to display.', 'wp-ever-accounting' ),
				'id'          => 'eac_currency_precision',
				'type'        => 'number',
				'placeholder' => '2',
				'default'     => 2,
				'desc_tip'    => true,
				'css'         => 'width: 80px;',
			),

			// end currency options.
			array(
				'type' => 'sectionend',
				'id'   => 'currency_options',
			),
		);
	}
}
