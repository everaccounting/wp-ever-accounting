<?php
/**
 * Admin Items Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Items/Items
 * @since       1.1.0
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_items_tab_items() {
	if ( ! current_user_can( 'manage_eaccounting' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	if ( in_array( $action, [ 'add', 'edit' ] ) ) {
		require_once dirname( __FILE__ ) . '/edit-item.php';
	} else {
		?>
		<h1>
			<?php _e( 'Items', 'wp-ever-accounting' ); ?>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'tab' => 'items', 'action' => 'add' ) ); ?>"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'page' => 'ea-tools', 'tab' => 'import' ) ); ?>"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-items.php';
		$list_table = new \EverAccounting\Admin\ListTables\List_Table_Items();
		$list_table->prepare_items();

		/**
		 * Fires at the top of the admin items page.
		 *
		 * Use this hook to add content to this section of items.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eaccounting_items_page_top' );
		?>
		<form id="ea-items-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-items' ); ?>

			<input type="hidden" name="page" value="ea-items"/>
			<input type="hidden" name="tab" value="items"/>

			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
		</form>
		<?php
		/**
		 * Fires at the bottom of the admin items page.
		 *
		 * Use this hook to add content to this section of items Tab.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_items_page_bottom' );
	}
}

add_action( 'eaccounting_items_tab_items', 'eaccounting_items_tab_items' );
