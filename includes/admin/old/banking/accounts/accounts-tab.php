<?php
defined('ABSPATH') || exit();


/**
 * Renders the Accounts Pages Admin Page
 * since 1.0.0
 */
function eaccounting_accounts_tab() {
	//wp_enqueue_script('eaccounting-accounts');

	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_account' ) {
		require_once dirname( __FILE__ ) . '/edit-account.php';
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_account' ) {
		require_once dirname( __FILE__ ) . '/edit-account.php';
	} else {
		require_once dirname( __FILE__ ) . '/class-accounts-table.php';
		$accounts_table = new EAccounting_Accounts_Table();
		$accounts_table->prepare_items();
		$base_url = admin_url('admin.php?page=eaccounting-banking&tab=accounts');
		?>

		<h2 class="wp-heading-inline"><?php _e( 'Accounts', 'wp-ever-accounting' ); ?></h2>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_account' ), $base_url ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
		<hr class="wp-header-end">

		<?php do_action( 'eaccounting_accounts_page_top' ); ?>
		<form id="eaccounting-accounts-filter" method="get" action="<?php echo admin_url( 'admin.php?page=eaccounting-accounts' ); ?>">
			<?php $accounts_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-accounts' ); ?>

			<input type="hidden" name="page" value="eaccounting-banking" />
			<input type="hidden" name="tab" value="accounts" />

			<?php $accounts_table->views() ?>
			<?php $accounts_table->display() ?>
		</form>
		<?php
		do_action( 'eaccounting_accounts_page_bottom' );
	}
}

add_action('eaccounting_banking_tab_accounts', 'eaccounting_accounts_tab');
