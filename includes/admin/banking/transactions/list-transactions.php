<?php
/**
 * Render Transaction list table
 *
 * @since       1.0.2
 * @subpackage  Admin/Banking/Transactions
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-transaction-list-table.php' );
$transactions_table = new EAccounting_Transaction_List_Table();
$transactions_table->prepare_items();
?>
<h1 class="wp-heading-inline"><?php _e( 'Transactions', 'wp-ever-accounting' ); ?></h1>
<?php do_action( 'eaccounting_transactions_table_top' ); ?>
<form id="ea-transactions-table" method="get" action="<?php echo admin_url(); ?>">
	<?php
	$transactions_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-transactions' );
	$transactions_table->display();
	?>
	<input type="hidden" name="page" value="ea-banking"/>
	<input type="hidden" name="tab" value="transactions"/>
</form>
<?php do_action( 'eaccounting_transactions_table_bottom' ); ?>