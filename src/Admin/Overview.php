<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Tools.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Overview extends \EverAccounting\Singleton {

	/**
	 * Tools constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'ever_accounting_overview_overview_content', array( __CLASS__, 'output_overview_overview_content' ) );
	}

	/**
	 * Output the tools page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_overview_tabs();
		$tab          = eac_get_input_var( 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_get_input_var( 'page' );
		$page_name    = 'overview';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Output the overview tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_overview_overview_content() {
		include dirname( __FILE__ ) . '/views/admin-overview.php';
	}
}
