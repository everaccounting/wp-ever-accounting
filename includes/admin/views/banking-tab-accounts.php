<?php
/**
 * Page : Banking
 * Tab: Accounts
 *
 * @var array  $tabs
 * @var string $current_tab
 */
defined( 'ABSPATH' ) || exit;

$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['account_id'] ) ) {
	$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : null;
	include dirname( __FILE__ ) . '/accounts/view-account.php';
} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
	$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : null;
	include dirname( __FILE__ ) . '/accounts/edit-account.php';
} else {
	include dirname( __FILE__ ) . '/accounts/list-account.php';
}
