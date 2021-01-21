<?php
/**
 * Admin Revenues Page
 *
 * Functions used for displaying revenue related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.0
 */

use EverAccounting\Models\Revenue;

defined( 'ABSPATH' ) || exit();

class EverAccounting_Admin_Revenues {
	/**
	 * EverAccounting_Admin_Revenues constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_sales_page_tab_revenues', array( $this, 'render_tab' ) );
	}

	/**
	 * Render revenues tab.
	 *
	 * @since 1.1.0
	 */
	public function render_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$invoice_id = isset( $_GET['revenue_id'] ) ? absint( $_GET['revenue_id'] ) : null;
			include dirname( __FILE__ ) . '/views/revenues/edit-revenue.php';
		} else {
			include dirname( __FILE__ ) . '/views/revenues/list-revenue.php';
		}
	}
}

return new \EverAccounting_Admin_Revenues();
