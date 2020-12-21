<?php
/**
 * Admin Accounts Page
 *
 * @since       1.0.2
 * @subpackage  Admin/Banking/Accounts
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_banking_tab_accounts() {
	if ( ! current_user_can( 'ea_manage_account' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

	if ( in_array( $action, array( 'edit', 'add' ), true ) ) {
		require_once EACCOUNTING_ABSPATH . '/includes/admin/banking/accounts/edit-account.php';
	} else {
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-account-list-table.php';
		$list_table = new EAccounting_Account_List_Table ();
		$list_table->prepare_items();
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
		?>
		<h1>
			<?php _e( 'Accounts', 'wp-ever-accounting' ); ?>
			<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
			<a class="page-title-action" href=" <?php echo esc_url( $import_url ); ?>"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
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
		<form id="ea-accounts-table" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-accounts' ); ?>

			<input type="hidden" name="page" value="ea-banking"/>
			<input type="hidden" name="tab" value="accounts"/>

			<?php $list_table->views(); ?>
			<?php $list_table->display(); ?>
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
	}
}

add_action( 'eaccounting_banking_tab_accounts', 'eaccounting_banking_tab_accounts' );
