<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 *
 * @author   EverAccounting
 * @category Core
 * @package  EverAccounting\Functions
 * @version  1.1.0
 */

defined( 'ABSPATH' ) || exit;

function eaccounting_get_global_currencies() {
	return eaccounting_get_currency_codes();
}

function eaccounting_get_currency_codes() {
	return eaccounting_get_currency_iso_codes();
}

function eaccounting_get_customers( $args = [] ) {
	return eaccounting_get_contacts( array_merge( $args, [ 'type' => 'customer' ] ) );
}

function eaccounting_get_vendors( $args = [] ) {
	return eaccounting_get_contacts( array_merge( $args, [ 'type' => 'vendor' ] ) );
}
