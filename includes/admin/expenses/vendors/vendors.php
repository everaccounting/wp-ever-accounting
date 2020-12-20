<?php
/**
 * Admin Vendors Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Expenses/Vendors
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_expenses_tab_vendors() {
	if ( ! current_user_can( 'ea_manage_vendor' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
		require_once dirname( __FILE__ ) . '/edit-vendor.php';
	} else {
		$add_url    = add_query_arg(
			array(
				'page'   => 'ea-expenses',
				'tab'    => 'vendors',
				'action' => 'add',
			),
			admin_url( 'admin.php' )
		);
		$import_url = add_query_arg(
			array(
				'page' => 'ea-tools',
				'tab'  => 'import',
			),
			admin_url( 'admin.php' )
		);
		?>
		<h1>
			<?php _e( 'Vendors', 'wp-ever-accounting' ); ?>
			<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
			<a class="page-title-action" href=" <?php echo esc_url( $import_url ); ?>"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-vendors.php';
		$list_table = new \EverAccounting\Admin\ListTables\List_Table_Vendors();
		$list_table->prepare_items();
		/**
		 * Fires at the top of the admin vendors page.
		 *
		 * Use this hook to add content to this section of vendors.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_vendors_page_top' );

		?>
		<form id="ea-vendors-table" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-vendors' ); ?>

			<input type="hidden" name="page" value="ea-expenses"/>
			<input type="hidden" name="tab" value="vendors"/>

			<?php $list_table->views(); ?>
			<?php $list_table->display(); ?>
		</form>
		<?php
		/**
		 * Fires at the bottom of the admin vendors page.
		 *
		 * Use this hook to add content to this section of vendors Tab.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_vendors_page_bottom' );
	}
}

add_action( 'eaccounting_expenses_tab_vendors', 'eaccounting_expenses_tab_vendors' );
