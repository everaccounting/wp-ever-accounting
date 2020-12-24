<?php
/**
 * Render Accounts list table
 * Page: Banking
 * Tab: Accounts
 */

defined( 'ABSPATH' ) || exit;
$add_url    = add_query_arg(
	array(
		'page'   => 'ea-banking',
		'tab'    => 'accounts',
		'action' => 'add',
	),
	admin_url( 'admin.php' )
);
$import_url = add_query_arg(
	array(
		'page' => 'ea-tools',
		'tab'  => 'import',
	),
	admin_url( 'admin.php' )
);
include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-account-list-table.php' );
$accounts_table = new EAccounting_Account_List_Table();
$accounts_table->prepare_items();
?>
	<h1>
		<?php esc_html_e( 'Accounts', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
		<a class="page-title-action" href=" <?php echo esc_url( $import_url ); ?>"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
	</h1>
	<?php do_action( 'eaccounting_accounts_table_top' ); ?>
	<form method="get" id="ea-accounts-table" action="<?php echo admin_url( 'admin.php' ); ?>">
		<?php $accounts_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-accounts' ); ?>
		<?php $accounts_table->display(); ?>

		<input type="hidden" name="page" value="ea-banking"/>
		<input type="hidden" name="tab" value="accounts"/>
	</form>
	<?php do_action( 'eaccounting_accounts_table_bottom' ); ?>

<?php
