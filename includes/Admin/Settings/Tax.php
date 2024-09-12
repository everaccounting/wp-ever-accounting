<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class ExpensesTab.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class Tax extends Page {

	/**
	 * ExpensesTab constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id    = 'taxes';
		$this->label = __( 'Taxes', 'wp-ever-accounting' );

		parent::__construct();
	}


	/**
	 * Get own sections for this page.
	 * Derived classes should override this method if they define sections.
	 * There should always be one default section with an empty string as identifier.
	 *
	 * Example:
	 * return array(
	 *   ''        => __( 'General', 'wp-ever-accounting' ),
	 *   'foobars' => __( 'Foos & Bars', 'wp-ever-accounting' ),
	 * );
	 *
	 * @return array An associative array where keys are section identifiers and the values are translated section names.
	 */
	protected function get_own_sections() {
		return array();
	}

	/**
	 * Get settings or the default section.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_settings_for_default_section() {
		return array(
			array(
				'title' => __( 'Tax options', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'tax_options',
			),
			array(
				'title'   => __( 'Enable Taxes', 'wp-ever-accounting' ),
				'desc'    => __( 'Enable tax rates and calculations.', 'wp-ever-accounting' ),
				'id'      => 'eac_tax_enabled',
				'type'    => 'checkbox',
				'default' => 'no',
			),
			array(
				'title'   => __( 'Rounding', 'wp-ever-accounting' ),
				'desc'    => __( 'Round tax at subtotal level, instead of rounding per tax rate.', 'wp-ever-accounting' ),
				'id'      => 'eac_tax_subtotal_rounding',
				'type'    => 'checkbox',
				'default' => 'no',
			),
			// tax_display_totals as single or not.
			array(
				'title'    => __( 'Display tax totals', 'wp-ever-accounting' ),
				'id'       => 'eac_tax_display_totals',
				'type'     => 'select',
				'default'  => 'single',
				'desc_tip' => true,
				'options'  => array(
					'single'   => __( 'As a single total', 'wp-ever-accounting' ),
					'itemized' => __( 'Itemized', 'wp-ever-accounting' ),
				),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'tax_options',
			),
		);
	}
}
