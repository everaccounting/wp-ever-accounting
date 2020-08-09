<?php
/**
 * Admin Vendors Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Expenses/Vendors
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_expenses_tab_vendors() {
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	if ( in_array( $action, [ 'add', 'edit' ] ) ) {
		require_once dirname( __FILE__ ) . '/edit-vendor.php';
	} else {
		require_once dirname( __FILE__ ) . '/list-table-vendors.php';
		$list_table = new \EverAccounting\Admin\Sales\List_Table_Vendors();
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
		<form id="ea-vendors-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounts' ), 'eaccounting-vendors' ); ?>

			<input type="hidden" name="page" value="ea-expenses"/>
			<input type="hidden" name="tab" value="vendors"/>

			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
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
