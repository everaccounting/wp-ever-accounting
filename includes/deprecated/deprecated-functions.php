<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 * @version  1.1.0
 */

use Ever_Accounting\Currency;

defined( 'ABSPATH' ) || exit;


/**
 * Create new currency programmatically.
 *
 * @param array $args
 *
 * @since 1.1.0
 * @return Currency|\WP_Error
 * @deprecated 1.1.4
 */
function eaccounting_insert_currency( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Currencies::insert()' );

	return \Ever_Accounting\Currencies::insert( $args );
}

/**
 * Delete a currency.
 *
 * @param $currency_code
 *
 * @return bool
 * @since 1.1.0
 * @deprecated 1.1.4
 */
function eaccounting_delete_currency( $currency_code ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Currencies::delete()' );

	return \Ever_Accounting\Currencies::delete( $currency_code );
}

