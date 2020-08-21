<?php
/**
 * Admin Transactions Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Transactions
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();
require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-transactions.php';

/**
 * render transactions page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_transactions_page() {
	$list_table = new EverAccounting\Admin\ListTables\List_Table_Transactions();
	$list_table->prepare_items();
	?>
	<div class="wrap">
		<h1>
			<?php _e( 'Transactions', 'wp-ever-accounting' ); ?>
		</h1>
		<?php

		/**
		 * Fires at the top of the admin transactions page.
		 *
		 * Use this hook to add content to this section of transactions.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_transactions_page_top' );

		?>
		<form id="ea-transactions-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-currencies' ), 'eaccounting-transactions' ); ?>

			<input type="hidden" name="page" value="ea-transactions"/>

			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
		</form>
		<?php
		/**
		 * Fires at the bottom of the admin transactions page.
		 *
		 * Use this hook to add content to this section of transactions Tab.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_transactions_page_bottom' );
		?>
	</div>
	<?php
}
