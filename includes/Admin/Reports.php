<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Reports
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 */
class Reports {

	/**
	 * Reports constructor.
	 */
	public function __construct() {
		add_filter( 'eac_reports_page_tabs', array( __CLASS__, 'register_tabs' ) );
	}

	/**
	 * Register tabs.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['payments'] = __( 'Payments', 'ever-accounting' );
		$tabs['expenses'] = __( 'Expenses', 'ever-accounting' );
		$tabs['profits']  = __( 'Profits', 'ever-accounting' );
		$tabs['taxes']    = __( 'Taxes', 'ever-accounting' );

		return $tabs;
	}
}
