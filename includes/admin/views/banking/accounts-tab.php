<?php
defined( 'ABSPATH' ) || exit();
if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_account' ) {
	eaccounting_get_views( 'banking/edit-account.php' );
} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_account' ) {
	eaccounting_get_views( 'banking/edit-account.php' );
} else {
	require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-ea-accounts-list-table.php';
	$list_table = new EAccounting_Accounts_List_Table();
	$list_table->prepare_items();
	$base_url   = admin_url( 'admin.php?page=eaccounting-banking&tab=accounts' );
	$insert_url = add_query_arg(
		array( 'action' => 'eaccounting_edit_account' ),
		admin_url( 'admin-ajax.php' ) )
	?>

	<h1 class="wp-heading-inline"><?php _e( 'Accounts', 'wp-ever-accounting' ); ?></h1>
	<a href="#"
	   class="page-title-action open-eaccounting-modal" data-title="Edit Account">
		<?php _e( 'Add New', 'wp-ever-accounting' ); ?>
	</a>


	<?php do_action( 'eaccounting_accounts_page_top' ); ?>
	<form method="get" action="<?php echo esc_url( $base_url ); ?>">
		<div class="ea-list-table">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-contacts' ); ?>
			<input type="hidden" name="page" value="eaccounting-accounts"/>
			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
		</div>
	</form>
	<?php
	do_action( 'eaccounting_accounts_page_bottom' );
}
