<?php
/**
 * Admin Bills Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Expenses/Bills
 * @since       1.1.0
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_expenses_tab_bills() {
	if ( ! current_user_can( 'ea_manage_payment' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	if ( in_array( $action, [ 'add', 'edit' ] ) ) {
		include_once dirname( __FILE__ ) . '/edit-bill.php';

	} else {
		?>
		<h1>
			<?php _e( 'Bills', 'wp-ever-accounting' ); ?>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'tab' => 'bills', 'action' => 'add' ) ); ?>"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'page' => 'ea-tools', 'tab' => 'import' ) ); ?>"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-bills.php';
		$list_table = new \EverAccounting\Admin\ListTables\List_Table_Bills();
		$list_table->prepare_items();

		/**
		 * Fires at the top of the admin bills page.
		 *
		 * Use this hook to add content to this section of bills.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eaccounting_bills_page_top' );
		?>
		<form id="ea-bills-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-bills' ); ?>

			<input type="hidden" name="page" value="ea-expenses"/>
			<input type="hidden" name="tab" value="bills"/>

			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
		</form>
		<?php
		/**
		 * Fires at the bottom of the admin bills page.
		 *
		 * Use this hook to add content to this section of bills Tab.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eaccounting_bills_page_bottom' );
	}
}

add_action( 'eaccounting_expenses_tab_bills', 'eaccounting_expenses_tab_bills' );

