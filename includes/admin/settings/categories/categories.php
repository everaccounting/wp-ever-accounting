<?php
/**
 * Admin categories Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Settings/Categories
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_render_categories_tab() {
	$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
	if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
		$category_id = isset( $_GET['category_id'] ) ? absint( $_GET['category_id'] ) : null;
		include dirname( __FILE__ ) . '/edit-category.php';
	} else {
		include dirname( __FILE__ ) . '/list-category.php';
	}
}

add_action( 'eaccounting_settings_tab_categories', 'eaccounting_render_categories_tab' );
