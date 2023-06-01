<?php
/**
 * View: Transaction List
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Transactions
 */

defined( 'ABSPATH' ) || exit();


$list_table = new \EverAccounting\Admin\ListTables\Transactions();
$action     = $list_table->current_action();
$page       = eac_get_input_var( 'page' );
$tab        = eac_get_input_var( 'tab' );
$list_table->process_bulk_action( $action );
$list_table->prepare_items();
?>
	<div class="eac-section-header">
		<div>
			<h2><?php esc_html_e( 'Transactions', 'ever-accounting' ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments&action=add' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add Payment', 'ever-accounting' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-purchase&tab=expenses&action=add' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add Expense', 'ever-accounting' ); ?></a>
			<a class="page-title-action" href=" <?php echo esc_url( admin_url( 'admin.php?page=eac-tools&tab=import' ) ); ?>"><?php esc_html_e( 'Import', 'ever-accounting' ); ?></a>
		</div>
	</div>
<?php do_action( 'ever_accounting_transactions_table_top' ); ?>
	<form method="get" id="eac-transactions-table">
		<?php
		$list_table->views();
		$list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' );
		$list_table->display();
		?>
		<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>"/>
		<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>"/>
	</form>
<?php
do_action( 'ever_accounting_transactions_table_bottom' );
