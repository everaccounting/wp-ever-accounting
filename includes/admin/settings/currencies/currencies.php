<?php
/**
 * Admin Currencies Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Settings/Currency
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_render_currencies_tab() {
	$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
	if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['currency_id'] ) ) {
		$currency_id = isset( $_GET['currency_id'] ) ? absint( $_GET['currency_id'] ) : null;
		include dirname( __FILE__ ) . '/view-currency.php';
	} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
		$currency_id = isset( $_GET['currency_id'] ) ? absint( $_GET['currency_id'] ) : null;
		include dirname( __FILE__ ) . '/edit-currency.php';
	} else {
		include dirname( __FILE__ ) . '/list-currency.php';
	}
}

add_action( 'eaccounting_settings_tab_currencies', 'eaccounting_render_currencies_tab' );
