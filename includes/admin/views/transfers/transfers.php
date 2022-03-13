<?php
/**
 * Admin Transfers Page.
 *
 * @package     Ever_Accounting
 * @subpackage  Admin/Banking/Transfers
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();


function ever_accounting_render_transfers_tab() {
	$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
	if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
		$transfer_id = isset( $_GET['transfer_id'] ) ? absint( $_GET['transfer_id'] ) : null;
		include dirname( __FILE__ ) . '/edit-transfer.php';
	} else {
		include dirname( __FILE__ ) . '/list-transfer.php';
	}
}

add_action( 'ever_accounting_banking_tab_transfers', 'ever_accounting_render_transfers_tab' );
