<?php

namespace EverAccounting\Admin\Misc;

defined( 'ABSPATH' ) || exit;


/**
 * TaxRates class.
 *
 * @since 3.0.0
 * @package EverAccounting\Admin
 */
class TaxRates {

	/**
	 * TaxRates constructor.
	 */
	public function __construct() {
		add_filter( 'eac_misc_page_tabs', array( __CLASS__, 'register_tabs' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['tax-rates'] = __( 'Tax Rates', 'wp-ever-accounting' );

		return $tabs;
	}
}
