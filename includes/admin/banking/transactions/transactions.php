<?php
/**
 * Admin Transactions Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Transactions
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

/**
 * render transactions page.
 *
 * @since 1.0.2
 */
function eaccounting_banking_tab_transactions() {
	require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-transaction-list-table.php';
	$list_table = new EAccounting_Transaction_List_Table();
	$list_table->prepare_items();
	?>
	<h1>
		<?php _e( 'Transactions', 'wp-ever-accounting' ); ?>
	</h1>
	<?php

	/**
	 * Fires at the top of the admin accounts page.
	 *
	 * Use this hook to add content to this section of accounts.
	 *
	 * @since 1.0.2
	 */
	do_action( 'eaccounting_transactions_page_top' );

	?>
	<form id="ea-transactions-table" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
		<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-accounts' ); ?>

		<input type="hidden" name="page" value="ea-banking"/>
		<input type="hidden" name="tab" value="transactions"/>

		<?php $list_table->views(); ?>
		<?php $list_table->display(); ?>
	</form>
	<?php
	/**
	 * Fires at the bottom of the admin accounts page.
	 *
	 * Use this hook to add content to this section of accounts Tab.
	 *
	 * @since 1.0.2
	 */
	do_action( 'eaccounting_transactions_page_bottom' );
}


add_action( 'eaccounting_banking_tab_transactions', 'eaccounting_banking_tab_transactions' );
