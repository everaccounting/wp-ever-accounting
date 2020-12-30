<?php
/**
 * Admin Reports
 *
 * Functions used for displaying banking related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.10
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Reports {
	/**
	 * Class constructor.
	 *
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_reports_page' ), 80 );
		add_action( 'eaccounting_reports_tab_sales', array( $this, 'render_sales_report' ) );
	}

	/**
	 * Registers the reports page.
	 *
	 */
	public function register_reports_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Reports', 'wp-ever-accounting' ),
			__( 'Reports', 'wp-ever-accounting' ),
			'ea_manage_report',
			'ea-reports',
			array( $this, 'display_reports_page' )
		);
	}

	/**
	 * Get report tabs.
	 *
	 * @since 1.1.0
	 * @return mixed|void
	 */
	public function get_tabs() {
		$tabs = array(
			'sales'    => __( 'Sales', 'wp-ever-accounting' ),
			'expenses' => __( 'Expenses', 'wp-ever-accounting' ),
			'profits'  => __( 'Profits', 'wp-ever-accounting' ),
		);

		return apply_filters( 'eaccounting_reports_tabs', $tabs );
	}

	public function get_tab_sections( $tab ) {
		$sections = array();
		switch ( $tab ) {
			case 'sales':
				$sections = array(
					'by_date'     => __( 'Sales by date', 'wp-ever-accounting' ),
					'by_category' => __( 'Sales by category', 'wp-ever-accounting' ),
					'by_items'    => __( 'Sales by item', 'wp-ever-accounting' ),
					'by_accounts' => __( 'Sales by accounts', 'wp-ever-accounting' ),
				);
				break;
		}

		return apply_filters( 'eaccounting_reports_tab_' . $tab . '_sections', $sections );
	}

	/**
	 * Displays the reports page.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function display_reports_page() {
		$tabs            = $this->get_tabs();
		$first_tab       = current( array_keys( $tabs ) );
		$current_tab     = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		$sections        = $this->get_tab_sections( $current_tab );
		$first_section   = current( array_keys( $sections ) );
		$current_section = ! empty( $_GET['section'] ) && array_key_exists( $_GET['section'], $sections ) ? sanitize_title( $_GET['section'] ) : $first_section;
		var_dump( $current_section );
		include dirname( __FILE__ ) . '/views/admin-page-reports.php';
	}


	public function render_sales_report() {
		require_once dirname( __FILE__ ) . '/reports/class-ea-admin-report.php';
		$report = new EAccounting_Admin_Report( 'report', '' );
		$report->output();
	}

}

new EAccounting_Admin_Reports();
