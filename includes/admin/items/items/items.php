<?php
/**
 * Admin Items Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Items/Item
 * @since       1.1.0
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_items_items_tab() {
	if ( ! current_user_can( 'ea_manage_currency' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

	if ( in_array( $action, array( 'edit', 'add' ), true ) ) {
		require_once dirname( __FILE__ ) . '/edit-item.php';
	} else {
		require_once dirname( __FILE__ ) . '/list-item.php';
	}
}

add_action( 'eaccounting_items_tab_items', 'eaccounting_items_items_tab' );
