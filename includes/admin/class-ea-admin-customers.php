<?php
/**
 * Admin Customers Page
 *
 * Functions used for displaying customer related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.0
 */

use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit();


class EverAccounting_Admin_Customers {
	/**
	 * EverAccounting_Admin_Customers constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_sales_page_tab_customers', array( $this, 'render_tab' ) );
	}

	/**
	 * Render customers tab.
	 *
	 * @since 1.1.0
	 */
	public function render_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['customer_id'] ) ) {
			$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : null;
			include dirname( __FILE__ ) . '/views/customers/view-customer.php';
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : null;
			include dirname( __FILE__ ) . '/views/customers/edit-customer.php';
		} else {
			include dirname( __FILE__ ) . '/views/customers/list-customer.php';
		}
	}
}

return new \EverAccounting_Admin_Customers();
