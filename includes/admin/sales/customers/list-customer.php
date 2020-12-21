<?php
/**
 * Render Customer list table
 *
 * @since       1.0.2
 * @subpackage  Admin/Sales/Customers
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-customer-list-table.php' );
$customers_table = new EAccounting_Customer_List_Table();
$customers_table->prepare_items();
$add_url = eaccounting_admin_url(
	array(
		'page'   => 'ea-sales',
		'tab'    => 'customers',
		'action' => 'add',
	)
);
?>
<h1 class="wp-heading-inline"><?php _e( 'Customers', 'wp-ever-accounting' ); ?></h1>
<a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">
	<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
</a>
<?php do_action( 'eaccounting_customers_table_top' ); ?>
<form id="ea-customers-table" method="get" action="<?php echo admin_url(); ?>">
	<?php
	$customers_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-customers' );
	$customers_table->display();
	?>
	<input type="hidden" name="page" value="ea-sales"/>
	<input type="hidden" name="tab" value="customers"/>
</form>
<?php do_action( 'eaccounting_customers_table_bottom' ); ?>
