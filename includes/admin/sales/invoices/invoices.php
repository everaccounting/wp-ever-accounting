<?php
/**
 * Admin Invoices Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Sales/Invoices
 * @since       1.1.0
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_invoices_tab_revenues() {
	if ( ! current_user_can( 'ea_manage_revenue' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}

	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	if ( in_array( $action, [ 'add', 'edit' ] ) ) {
		include_once dirname( __FILE__ ) . '/edit-invoice.php';
	} else {
		?>
		<h1>
			<?php _e( 'Invoices', 'wp-ever-accounting' ); ?>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'tab' => 'invoices', 'action' => 'add' ) ); ?>"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'page' => 'ea-tools', 'tab' => 'import' ) ); ?>"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-invoices.php';
		$list_table = new EverAccounting\Admin\ListTables\List_Table_Invoices();
		$list_table->prepare_items();

		/**
		 * Fires at the top of the admin invoices page.
		 *
		 * Use this hook to add content to this section of revenues.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eaccounting_invoices_page_top' );
		?>
		<form id="ea-invoices-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-revenues' ); ?>

			<input type="hidden" name="page" value="ea-sales"/>
			<input type="hidden" name="tab" value="invoices"/>

			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
		</form>
		<?php
		/**
		 * Fires at the bottom of the admin invoices page.
		 *
		 * Use this hook to add content to this section of invoices Tab.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_invoices_page_bottom' );
	}
}

add_action( 'eaccounting_sales_tab_invoices', 'eaccounting_invoices_tab_revenues' );
