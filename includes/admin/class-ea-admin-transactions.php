<?php
/**
 * Admin Transactions Page
 *
 * Functions used for displaying transactions related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.0
 */

defined( 'ABSPATH' ) || exit();

class EverAccounting_Admin_Transactions {
	/**
	 * EverAccounting_Admin_Transactions constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_banking_page_tab_transactions', array( $this, 'render_tab' ) );
	}

	/**
	 * Render customers tab.
	 *
	 * @since 1.1.0
	 */
	public function render_tab(){
		include dirname( __FILE__ ) . '/views/transactions/list-transactions.php';
	}
}

return new \EverAccounting_Admin_Transactions();
