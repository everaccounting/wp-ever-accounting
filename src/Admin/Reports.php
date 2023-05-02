<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Reports.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Reports extends \EverAccounting\Singleton {

	/**
	 * Reports constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {}

	/**
	 * Output the sales page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_reports_tabs();
		$tab          = eac_filter_input( INPUT_GET, 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_filter_input( INPUT_GET, 'page' );
		$page_name    = 'reports';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}
}
