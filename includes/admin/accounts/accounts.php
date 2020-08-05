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

require_once __DIR__ . '/class-list-table.php';

/**
 * Add per page screen option to the Accounts list table
 *
 * @since 1.0.2
 */
function eaccounting_baking_screen_options_tab_accounts() {

	$tab = eaccounting_get_current_tab();

	if ( $tab !== 'accounts' || empty( $tab ) ) {
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


	do_action( 'eaccounting_accounts_screen_options' );
}

add_action( 'eaccounting_baking_screen_options_tab_accounts', 'eaccounting_baking_screen_options_tab_accounts' );


