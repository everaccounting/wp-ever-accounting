<?php
defined('ABSPATH') || exit();


/**
 * Renders the Accounts Pages Admin Page
 * since 1.0.0
 */
function eaccount_accounts_page() {
	eaccounting_page_wrapper_open('accounts-page');
	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_account' ) {
		require_once EVER_ACCOUNTING_ABSPATH . '/includes/admin/accounts/edit-account.php';
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_account' ) {
		require_once EVER_ACCOUNTING_ABSPATH . '/includes/admin/accounts/add-account.php';
	} else {
		require_once EVER_ACCOUNTING_ABSPATH . '/includes/admin/accounts/class-accounts-table.php';
		$accounts_table = new EAccounting_Accounts_Table();
		$accounts_table->prepare_items();
		?>

		<h1><?php _e( 'Accounts', 'wp-ever-accounting' ); ?><a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_account' ), admin_url('admin.php?page=eaccounting-accounts') ) ); ?>" class="add-new-h2"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a></h1>
		<?php do_action( 'eaccounting_accounts_page_top' ); ?>
		<form id="eaccounting-accounts-filter" method="get" action="<?php echo admin_url( 'admin.php?page=eaccounting-accounts' ); ?>">
			<?php $accounts_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-accounts' ); ?>

			<input type="hidden" name="page" value="eaccounting-accounts" />

			<?php $accounts_table->views() ?>
			<?php $accounts_table->display() ?>
		</form>
		<?php
		do_action( 'eaccounting_accounts_page_bottom' );
	}
	eaccounting_page_wrapper_close();
}
