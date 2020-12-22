<?php
/**
 * Admin Items Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Items/Item
 * @since       1.1.0
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_render_items_tab() {
	$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
	if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
		$item_id = isset( $_GET['item_id'] ) ? absint( $_GET['item_id'] ) : null;
		include dirname( __FILE__ ) . '/edit-item.php';
	} else {
		include dirname( __FILE__ ) . '/list-item.php';
	}
}

add_action( 'eaccounting_items_tab_items', 'eaccounting_render_items_tab' );
