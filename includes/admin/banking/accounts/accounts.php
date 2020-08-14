<?php
/**
 * Admin Accounts Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking/Accounts
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_banking_tab_accounts() {
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

	if ( in_array( $action, [ 'edit', 'add' ] ) ) {
		require_once EACCOUNTING_ABSPATH . '/includes/admin/banking/accounts/edit-account.php';
	} else {
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-accounts.php';
		$list_table = new \EverAccounting\Admin\Banking\Accounts\List_Table_Accounts();
		$list_table->prepare_items();
		?>
		<h1>
			<?php _e( 'Accounts', 'wp-ever-accounting' ); ?>
			<a href="<?php echo esc_url( eaccounting_admin_url( array(
					'action' => 'add',
					'tab'    => 'accounts'
			) ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php

		/**
		 * Fires at the top of the admin accounts page.
		 *
		 * Use this hook to add content to this section of accounts.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_accounts_page_top' );

		?>
		<form id="ea-accounts-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounts' ), 'eaccounting-accounts' ); ?>

			<input type="hidden" name="page" value="ea-banking"/>
			<input type="hidden" name="tab" value="accounts"/>

			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
		</form>
		<?php
		/**
		 * Fires at the bottom of the admin accounts page.
		 *
		 * Use this hook to add content to this section of accounts Tab.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_accounts_page_bottom' );
		?>

		<?php
	}
}

add_action( 'eaccounting_banking_tab_accounts', 'eaccounting_banking_tab_accounts' );
