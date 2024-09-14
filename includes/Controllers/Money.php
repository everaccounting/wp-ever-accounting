<?php

namespace EverAccounting\Controllers;

defined( 'ABSPATH' ) || exit;

/**
 * Money controller.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Controllers
 */
class Money {

	/**
	 * Format price with currency code & number format
	 *
	 * @param string $amount Amount.
	 *
	 * @param string $code If not passed will be used default currency.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function format( $amount, $code = '' ) {
		if ( is_null( $code ) ) {
			$code = eac_get_base_currency();
		}
		if ( ! is_numeric( $amount ) ) {
			$amount = eac_sanitize_amount( $amount, $code );
		}
		$currency     = eac_get_currency( $code );
		$decimals    = $currency ? $currency->decimals : 2;
		$thousand_sep = $currency ? $currency->thousand_separator : '';
		$decimal_sep  = $currency ? $currency->decimal_separator : '.';
		$position     = $currency ? $currency->position : 'before';
		$symbol       = $currency ? $currency->symbol : '';
		$prefix       = 'before' === $position ? $symbol : '';
		$suffix       = 'after' === $position ? $symbol : '';
		$is_negative  = $amount < 0;
		$amount       = $is_negative ? - $amount : $amount;

		$value = number_format( $amount, $decimals, $decimal_sep, $thousand_sep );

		return ( $is_negative ? '-' : '' ) . $prefix . $value . $suffix;
	}

	/**
	 * Sanitize price for inserting into database.
	 *
	 * When converting to default currency, the amount will convert to default currency
	 * with the exchange rate of the currency at the time of the transaction.
	 *
	 * @param string $amount Amount.
	 * @param string $from_code If not passed will be used default currency.
	 *
	 * @since 1.0.2
	 * @return float|int
	 */
	public function sanitize( $amount, $from_code = '' ) {
		$base_currency = eac_get_base_currency();
		// Get the base currency if not passed.
		if ( is_null( $from_code ) ) {
			$from_code = $base_currency;
		}

		if ( ! is_numeric( $amount ) ) {
			$currency = eac_get_currency( $from_code );
			// Retrieve the thousand and decimal separator from currency object.
			$thousand_separator = $currency ? $currency->thousand_separator : '';
			$decimal_separator  = $currency ? $currency->decimal_separator : '.';
			$symbol             = $currency ? $currency->symbol : '';
			// Remove currency symbol from amount.
			$amount = str_replace( $symbol, '', $amount );
			// Remove any non-numeric characters except a thousand and decimal separators.
			$amount = preg_replace( '/[^0-9\\' . $thousand_separator . '\\' . $decimal_separator . '\-\+]/', '', $amount );
			// Replace a thousand and decimal separators with empty string and dot respectively.
			$amount = str_replace( array( $thousand_separator, $decimal_separator ), array( '', '.' ), $amount );
			// Convert to int if amount is a whole number, otherwise convert to float.
			if ( preg_match( '/^([\-\+])?\d+$/', $amount ) ) {
				$amount = (int) $amount;
			} elseif ( preg_match( '/^([\-\+])?\d+\.\d+$/', $amount ) ) {
				$amount = (float) $amount;
			} else {
				$amount = 0;
			}
		}

		return $amount;
	}
}
