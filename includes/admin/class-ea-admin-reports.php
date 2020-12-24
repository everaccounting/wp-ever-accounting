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
		add_action( 'eaccounting_render_report_sales', array( $this, 'render_sales_report' ) );
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
			'taxes'    => __( 'Taxes', 'wp-ever-accounting' ),
		);

		return apply_filters( 'eaccounting_reports_tabs', $tabs );
	}

	/**
	 * Displays the reports page.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function display_reports_page() {
		$tabs        = $this->get_tabs();
		$first_tab   = current( array_keys( $tabs ) );
		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		include dirname( __FILE__ ) . '/views/html-admin-page-reports.php';
	}


	public function render_sales_report() {
//		$sections = array(
//			'by_date'     => __( 'By Date', 'wp-ever-accounting' ),
//			'by_category' => __( 'By Category', 'wp-ever-accounting' ),
//		);
//		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $sections ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
//
//		include dirname( __FILE__ ) . '/views/html-admin-page-reports.php';
	}

}

new EAccounting_Admin_Reports();
