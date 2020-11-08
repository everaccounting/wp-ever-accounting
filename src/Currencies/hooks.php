<?php
/**
 * Delete default currency from settings
 *
 * @since 1.0.2
 *
 * @param int $id ID of the default currency.
 *
 */

defined( 'ABSPATH' ) || exit();

function eaccounting_delete_default_currency( $id ) {
	$default_account = eaccounting()->settings->get( 'default_currency' );
	if ( absint( $default_account ) === absint( $id ) ) {
		eaccounting()->settings->set( array( array( 'default_currency' => '' ) ), true );
	}
}

add_action( 'eaccounting_delete_currency', 'eaccounting_delete_default_currency' );
