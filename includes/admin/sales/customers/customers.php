<?php
/**
 * Admin Revenues Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Sales/Revenues
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_sales_tab_customers() {
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	if ( in_array( $action, [ 'add', 'edit' ] ) ) {
        include_once dirname( __FILE__ ) .'/edit-customers.php';
	} else {
        ?>
        <h1>
            <?php _e( 'Customers', 'wp-ever-accounting' ); ?>
            <a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'tab' => 'customers', 'action' => 'add' ) ); ?>"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
            <a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'page' => 'ea-tools', 'tab' => 'import' ) ); ?>"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
        </h1>
        <?php
		require_once dirname( __FILE__ ) . '/list-table-customers.php';
		$list_table = new \EverAccounting\Admin\Sales\List_Table_Customers();
		$list_table->prepare_items();
		/**
		 * Fires at the top of the admin customers page.
		 *
		 * Use this hook to add content to this section of customers.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_customers_page_top' );

		?>
		<form id="ea-accounts-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounts' ), 'eaccounting-customers' ); ?>

			<input type="hidden" name="page" value="ea-sales"/>
			<input type="hidden" name="tab" value="customers"/>

			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
		</form>
		<?php
		/**
		 * Fires at the bottom of the admin customers page.
		 *
		 * Use this hook to add content to this section of customers Tab.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_customers_page_bottom' );
	}
}

add_action( 'eaccounting_sales_tab_customers', 'eaccounting_sales_tab_customers' );
