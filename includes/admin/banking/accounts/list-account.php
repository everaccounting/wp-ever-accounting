<?php
/**
 * Render Account list table
 *
 * @since       1.0.2
 * @subpackage  Admin/Banking/Accounts
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-account-list-table.php' );
$account_table = new EAccounting_Account_List_Table();
$account_table->prepare_items();
$add_url = eaccounting_admin_url(
	array(
		'page'   => 'ea-banking',
		'tab'    => 'accounts',
		'action' => 'add',
	)
);
?>
<h1 class="wp-heading-inline"><?php _e( 'Accounts', 'wp-ever-accounting' ); ?></h1>
<a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">
	<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
</a>
<?php do_action( 'eaccounting_accounts_table_top' ); ?>
<form id="ea-accounts-table" method="get" action="<?php echo admin_url(); ?>">
	<?php
	$account_table->views();
	$account_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-accounts' );
	$account_table->display();
	?>
	<input type="hidden" name="page" value="ea-banking"/>
	<input type="hidden" name="tab" value="accounts"/>
</form>
<?php do_action( 'eaccounting_accounts_table_bottom' ); ?>
