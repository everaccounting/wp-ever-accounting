<?php
/**
 * Admin Transactions Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Banking/Transactions
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

/**
 * render transactions page.
 *
 * @since 1.0.2
 */
function eaccounting_render_transactions_tab() {
	include dirname( __FILE__ ) . '/list-transactions.php';
}


add_action( 'eaccounting_banking_tab_transactions', 'eaccounting_render_transactions_tab' );
