<?php
/**
 * Updates functions.
 *
 * @since 1.0.0
 * @package EverAccounting\Functions
 */

use EverAccounting\Utilities\I18n;

defined( 'ABSPATH' ) || exit;

function eac_update_121_currency() {
	$options = get_option( 'eaccounting_currencies', array() );
	if ( $options ) {
		foreach ( $options as $option ) {
			eac_insert_currency( $option );
		}
		delete_option( 'eaccounting_currencies' );
	}
	$currencies = eac_get_currencies( [ 'limit' => - 1 ] );
	$codes      = wp_list_pluck( $currencies, 'code' );
	foreach ( I18n::get_currencies() as $code => $currency ) {
		if ( ! in_array( $code, $codes, true ) ) {
			$currency['enabled'] = 0;
			eac_insert_currency( $currency );
		}
	}
}

function eac_update_1212() {
	error_log( 'Run only once' );
}

function eac_update_1213() {
	error_log( 'This will run 5 times' );
	$times = get_option( 'eac_update_1213', 1 );
	error_log( 'Times: ' . $times );
	update_option( 'eac_update_1213', $times + 1 );
	// if times 5 or less
	if ( $times <= 5 ) {
		return true;
	}
}
