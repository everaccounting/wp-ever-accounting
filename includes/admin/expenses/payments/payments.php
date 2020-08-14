<?php
/**
 * Admin Payments Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Expenses/Payments
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_expenses_tab_payments() {
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	if ( in_array( $action, [ 'add', 'edit' ] ) ) {
		include_once dirname( __FILE__ ) . '/edit-payment.php';

	} else {
		?>
		<h1>
			<?php _e( 'Payments', 'wp-ever-accounting' ); ?>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'tab' => 'payments', 'action' => 'add' ) ); ?>"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'page' => 'ea-tools', 'tab' => 'import' ) ); ?>"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-payments.php';
		$list_table = new \EverAccounting\Admin\ListTables\List_Table_Payments();
		$list_table->prepare_items();

		/**
		 * Fires at the top of the admin payments page.
		 *
		 * Use this hook to add content to this section of payments.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_payments_page_top' );

		?>
		<form id="ea-accounts-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounts' ), 'eaccounting-payments' ); ?>

			<input type="hidden" name="page" value="ea-expenses"/>
			<input type="hidden" name="tab" value="payments"/>

			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
		</form>
		<?php
		/**
		 * Fires at the bottom of the admin payments page.
		 *
		 * Use this hook to add content to this section of payments Tab.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_payments_page_bottom' );
	}
}

add_action( 'eaccounting_expenses_tab_payments', 'eaccounting_expenses_tab_payments' );
