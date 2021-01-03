<?php
/**
 * Admin Bill Page
 *
 * Functions used for displaying bill related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.10
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Bills {
	/**
	 * EAccounting_Admin_Bill constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_expenses_page_tab_bills', array( $this, 'render_tab' ), 20 );
	}

	/**
	 *
	 * @since 1.1.0
	 */
	public function render_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['bill_id'] ) ) {
			$bill_id = isset( $_GET['bill_id'] ) ? absint( $_GET['bill_id'] ) : null;
			include dirname( __FILE__ ) . '/views/bills/view-bill.php';
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$bill_id = isset( $_GET['bill_id'] ) ? absint( $_GET['bill_id'] ) : null;
			include dirname( __FILE__ ) . '/views/bills/edit-bill.php';
		} else {
			include dirname( __FILE__ ) . '/views/bills/list-bill.php';
		}
	}
}

return new EAccounting_Admin_Bills();
