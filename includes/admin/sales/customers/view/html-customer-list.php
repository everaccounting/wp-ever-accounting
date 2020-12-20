<?php
/**
 * Customer list
 */

include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-customers.php' );
$customers_table = new EverAccounting\Admin\ListTables\List_Table_Customers();
$customers_table->prepare_items();
?>
	<h1 class="wp-heading-inline"><?php _e( 'Customers', 'wp-ever-accounting' ); ?></h1>
	<a class="page-title-action" href="<?php echo esc_url( admin_url( 'admin.php?page=ea-sales&tab=customers&action=add' ) ); ?>">
		<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
	</a>
<?php do_action( 'eaccounting_customers_table_top' ); ?>
	<form id="ea-customers-table" method="get" action="<?php echo admin_url( 'edit.php?post_type=download&page=edd-customers' ); ?>">
		<?php
		$customers_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-customers' );
		$customers_table->display();
		?>
		<input type="hidden" name="page" value="edd-customers"/>
		<input type="hidden" name="view" value="customers"/>
	</form>
<?php do_action( 'eaccounting_customers_table_bottom' ); ?>
