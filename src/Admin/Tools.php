<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Tools.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Tools extends \EverAccounting\Singleton {

	/**
	 * Tools constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'ever_accounting_tools_tab_export', array( __CLASS__, 'output_transactions_export_form' ) );
	}

	/**
	 * Output the tools page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_tools_tabs();
		$tab          = eac_filter_input( INPUT_GET, 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_filter_input( INPUT_GET, 'page' );
		$page_name    = 'tools';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Output the transactions export form.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_transactions_export_form() {
		include dirname( __FILE__ ) . '/views/export/transactions-export.php';
	}
}
