<?php
/**
 * Admin Accounts Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Tools
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

require_once __DIR__ .'/class-list-table.php';

/**
 * Add per page screen option to the Accounts list table
 *
 * @since 1.0.2
 */
function eaccounting_baking_screen_options_tab_accounts() {

	$tab = eaccounting_get_current_tab();

	if ( $tab !== 'accounts'|| empty($tab) ) {
		return;
	}

	add_screen_option(
		'per_page',
		array(
			'label'   => __( 'Number of accounts per page:', 'wp-ever-accounting' ),
			'option'  => 'eaccounting_edit_accounts_per_page',
			'default' => 20,
		)
	);

	/*
	 * Instantiate the list table to make the columns array available to screen options.
	 *
	 * If the 'view_accounts' action is set, don't instantiate. Instantiating in sub-views
	 * creates conflicts in the screen option column controls if another list table is being
	 * displayed.
	 */
	if ( empty( $_REQUEST['action'] ) || ( ! empty( $_REQUEST['action'] ) && 'view_accounts' !== $_REQUEST['action'] ) ) {
		new EAccounting_Accounts_Table();
	}


	do_action('eaccounting_accounts_screen_options');
}

add_action('eaccounting_baking_screen_options_tab_accounts', 'eaccounting_baking_screen_options_tab_accounts');



function eaccounting_banking_tab_accounts() {
	$accounts_table = new EAccounting_Accounts_Table();
	$accounts_table->prepare_items();
	?>
	<div class="wrap">
		<h1>
			<?php _e( 'Accounts', 'wp-ever-accounting' ); ?>
			<a href="<?php echo esc_url( eaccounting_admin_url( array( 'action' => 'add_account', 'tab'=>'accounts' ) ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
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
			<?php $accounts_table->search_box( __( 'Search', 'wp-ever-accounts' ), 'eaccounting-accounts' ); ?>

			<input type="hidden" name="page" value="ea-banking" />
			<input type="hidden" name="tab" value="accounts" />

			<?php $accounts_table->views() ?>
			<?php $accounts_table->display() ?>
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
	</div>
	<?php
}

add_action( 'eaccounting_banking_tab_accounts', 'eaccounting_banking_tab_accounts' );
