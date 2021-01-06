<?php
/**
 * Admin Payments Page
 *
 * Functions used for displaying payments related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.0
 */

use EverAccounting\Models\Payment;

defined( 'ABSPATH' ) || exit();


class EverAccounting_Admin_Payments {
	/**
	 * EverAccounting_Admin_Payments constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_expenses_page_tab_payments', array( $this, 'render_tab' ) );
	}

	/**
	 * Render payments tab.
	 *
	 * @since 1.1.0
	 */
	public function render_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$payment_id = isset( $_GET['payment_id'] ) ? absint( $_GET['payment_id'] ) : null;
			include dirname( __FILE__ ) . '/views/payments/edit-payment.php';
		} else {
			include dirname( __FILE__ ) . '/views/payments/list-payment.php';
		}
	}
}

return new \EverAccounting_Admin_Payments();
