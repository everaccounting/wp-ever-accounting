<?php
/**
 * EverAccounting Tax functions.
 *
 * Functions related to taxes.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is tax enabled.
 *
 * @since 1.1.0
 * @return bool
 */
function eac_tax_enabled() {
	return apply_filters( 'ever_accounting_tax_enabled', get_option( 'eac_tax_enabled', 'no' ) === 'yes' );
}

/**
 * Are prices inclusive of tax?
 *
 * @return bool
 */
function eac_price_include_tax() {
	return eac_tax_enabled() && apply_filters( 'ever_accounting_price_include_tax', get_option( 'eac_prices_include_tax' ) === 'yes' );
}

/**
 * Get calculated tax.
 *
 * @since 1.1.0
 *
 * @param string $amount  Amount to calculate tax for.
 * @param float  $rate   Tax rate.
 * @param bool   $inclusive Whether the amount is inclusive of tax.
 *
 * @return float|int
 */
function eac_calculate_tax( $amount, $rate, $inclusive = false ) {
	$tax    = 0.00;
	$amount = eac_sanitize_number( $amount );
	$rate   = eac_sanitize_number( $rate );

	if ( $amount > 0 ) {

		if ( $inclusive ) {
			$pre_tax = ( $amount / ( 1 + floatval( $rate ) ) );
			$tax     = $amount - $pre_tax;
		} else {
			$tax = $amount * $rate;
		}
	}

	return $tax;
}

