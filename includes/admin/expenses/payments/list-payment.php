<?php
/**
 * Render Payment list table
 *
 * @since       1.0.2
 * @subpackage  Admin/Expenses/Payments
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-payment-list-table.php' );
$payments_table = new EAccounting_Payment_List_Table();
$payments_table->prepare_items();
$add_url = eaccounting_admin_url(
	array(
		'page'   => 'ea-expenses',
		'tab'    => 'payments',
		'action' => 'add',
	)
);
?>
<h1 class="wp-heading-inline"><?php _e( 'Payments', 'wp-ever-accounting' ); ?></h1>
<a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">
	<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
</a>
<?php do_action( 'eaccounting_payments_table_top' ); ?>
<form id="ea-payments-table" method="get" action="<?php echo admin_url(); ?>">
	<?php
	$payments_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-payments' );
	$payments_table->display();
	?>
	<input type="hidden" name="page" value="ea-expenses"/>
	<input type="hidden" name="tab" value="payments"/>
</form>
<?php do_action( 'eaccounting_payments_table_bottom' ); ?>
