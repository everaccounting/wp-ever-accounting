<?php
/**
 * Render Transfer list table
 *
 * @since       1.0.2
 * @subpackage  Admin/Banking/Transfers
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-transfer-list-table.php' );
$transfers_table = new EAccounting_Transfer_List_Table();
$transfers_table->prepare_items();
$add_url = eaccounting_admin_url(
	array(
		'page'   => 'ea-banking',
		'tab'    => 'transfers',
		'action' => 'add',
	)
);
?>
<h1 class="wp-heading-inline"><?php _e( 'Transfers', 'wp-ever-accounting' ); ?></h1>
<a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">
	<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
</a>
<?php do_action( 'eaccounting_transfers_table_top' ); ?>
<form id="ea-transfers-table" method="get" action="<?php echo admin_url(); ?>">
	<?php
	$transfers_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-transfers' );
	$transfers_table->display();
	?>
	<input type="hidden" name="page" value="ea-banking"/>
	<input type="hidden" name="tab" value="transfers"/>
</form>
<?php do_action( 'eaccounting_transfers_table_bottom' ); ?>
