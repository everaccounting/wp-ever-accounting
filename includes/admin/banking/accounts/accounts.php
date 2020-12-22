<?php
/**
 * Admin Accounts Page
 *
 * @since       1.0.2
 * @subpackage  Admin/Banking/Accounts
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_render_accounts_tab() {
	$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
	if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['account_id'] ) ) {
		$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : null;
		include dirname( __FILE__ ) . '/view-account.php';
	} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
		$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : null;
		include dirname( __FILE__ ) . '/edit-account.php';
	} else {
		include dirname( __FILE__ ) . '/list-account.php';
	}
}

add_action( 'eaccounting_banking_tab_accounts', 'eaccounting_render_accounts_tab' );

function eaccounting_account_profile_top($account) {
	include dirname( __FILE__ ) . '/account-profile-top.php';
}
add_action('eaccounting_account_profile_top','eaccounting_account_profile_top');

function eaccounting_account_profile_aside($account) {
	include dirname( __FILE__ ) . '/account-profile-aside.php';
}
add_action('eaccounting_account_profile_aside','eaccounting_account_profile_aside');

function eaccounting_account_profile_content_transactions($account) {
	include dirname( __FILE__ ) . '/account-profile-transactions.php';
}
add_action('eaccounting_account_profile_content_transactions','eaccounting_account_profile_content_transactions');

function eaccounting_account_profile_content_transfers($account) {
	include dirname( __FILE__ ) . '/account-profile-transfers.php';
}
add_action('eaccounting_account_profile_content_transfers','eaccounting_account_profile_content_transfers');
