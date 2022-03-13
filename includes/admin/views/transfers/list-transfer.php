<?php
/**
 * Render Transfer list table
 *
 * @since       1.0.2
 * @subpackage  Admin/Banking/Transfers
 * @package     Ever_Accounting
 */

defined( 'ABSPATH' ) || exit();

include( dirname( EVER_ACCOUNTING_FILE ) . '/includes/admin/list-tables/class-transfer-list-table.php' );
$transfers_table = new Ever_Accounting_Transfer_List_Table();
$transfers_table->prepare_items();
$add_url = ever_accounting_admin_url(
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
<?php do_action( 'ever_accounting_transfers_table_top' ); ?>
<form id="ea-transfers-table" method="get" action="<?php echo admin_url( 'admin.php' ); ?>">
	<?php  $transfers_table->display(); ?>
	<input type="hidden" name="page" value="ea-banking"/>
	<input type="hidden" name="tab" value="transfers"/>
</form>
<?php do_action( 'ever_accounting_transfers_table_bottom' ); ?>
<?php
ever_accounting_enqueue_js("
	jQuery('.del').on('click',function(e){
		if(confirm('Are you sure you want to delete?')){
			return true;
		} else {
			return false;
		}
	});
");
