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
	if ( ! current_user_can( 'ea_manage_customer' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	if ( in_array( $action, [ 'add', 'edit' ] ) ) {
		include_once dirname( __FILE__ ) . '/edit-customer.php';
	} else {
		?>
		<h1>
			<?php _e( 'Customers', 'wp-ever-accounting' ); ?>
			<a class="page-title-action" href="
			<?php
			echo eaccounting_admin_url(
				array(
					'tab'    => 'customers',
					'action' => 'add',
				)
			);
			?>
												"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
			<a class="page-title-action" href="
			<?php
			echo eaccounting_admin_url(
				array(
					'page' => 'ea-tools',
					'tab'  => 'import',
				)
			);
			?>
												"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-customers.php';
		$list_table = new \EverAccounting\Admin\ListTables\List_Table_Customers();
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
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-customers' ); ?>

			<input type="hidden" name="page" value="ea-sales"/>
			<input type="hidden" name="tab" value="customers"/>

			<?php $list_table->views(); ?>
			<?php $list_table->display(); ?>
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
