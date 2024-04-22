<?php
/**
 * Core functions
 *
 * @version  1.1.0
 * @category Functions
 * @package  EverAccounting\Functions
 */

defined( 'ABSPATH' ) || exit;

require_once dirname( __FILE__ ) . '/Functions/categories.php';
require_once dirname( __FILE__ ) . '/Functions/currencies.php';
require_once dirname( __FILE__ ) . '/Functions/items.php';
require_once dirname( __FILE__ ) . '/Functions/updates.php';

/**
 * Get base currency code
 *
 * @since 1.0.2
 * @return string
 */
function eac_get_base_currency() {
	$settings = get_option( 'eaccounting_settings', array() );
	$currency = get_option( 'eac_base_currency', isset( $settings['default_currency'] ) ? $settings['default_currency'] : 'usd' );

	return apply_filters( 'ever_accounting_base_currency', strtoupper( $currency ) );
}

/**
 * Format price with currency code & number format
 *
 * @param string $amount Amount.
 *
 * @param string $code If not passed will be used default currency.
 *
 * @return string
 * @since 1.0.2
 */
function eac_format_money( $amount, $code = null ) {
	if ( is_null( $code ) ) {
		$code = eac_get_base_currency();
	}
	if ( ! is_numeric( $amount ) ) {
		$amount = eac_sanitize_money( $amount, $code );
	}
	$currency     = eac_get_currency( $code );
	$precision    = $currency ? $currency->precision : 2;
	$thousand_sep = $currency ? $currency->thousand_separator : '';
	$decimal_sep  = $currency ? $currency->decimal_separator : '.';
	$position     = $currency ? $currency->position : 'before';
	$symbol       = $currency ? $currency->symbol : '';
	$prefix       = 'before' === $position ? $symbol : '';
	$suffix       = 'after' === $position ? $symbol : '';
	$is_negative  = $amount < 0;
	$amount       = $is_negative ? - $amount : $amount;

	$value = number_format( $amount, $precision, $decimal_sep, $thousand_sep );

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
 * @return float|int
 * @since 1.0.2
 */
function eac_sanitize_money( $amount, $from_code = null ) {
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

/**
 * Convert price from default to any other currency.
 *
 * @param string $amount Amount.
 * @param string $to Convert to currency code.
 * @param string $to_rate Convert to currency rate.
 *
 * @return float|int|string
 * @since 1.0.2
 */
function eac_convert_money_from_base( $amount, $to, $to_rate = null ) {
	$default = eac_get_base_currency();
	$amount  = eac_sanitize_money( $amount, $to );
	// No need to convert same currency.
	if ( $default === $to ) {
		return $amount;
	}
	$currency  = eac_get_currency( $to );
	$precision = $currency ? $currency->precision : 2;
	if ( is_null( $to_rate ) ) {
		$to_rate = $currency ? $currency->exchange_rate : 1;
	}

	// First check if mathematically possible.
	if ( 0 === $to_rate ) {
		return 0;
	}

	// Multiply by rate.
	return round( $amount * $to_rate, $precision, PHP_ROUND_HALF_UP );
}

/**
 * Convert price from any currency to default.
 *
 * @param string $amount Amount.
 * @param string $from Convert from currency code.
 * @param string $from_rate Convert from currency rate.
 * @param bool   $formatted Whether to format the price or not.
 *
 * @return float|int|string
 * @since 1.0.2
 */
function eac_convert_money_to_base( $amount, $from, $from_rate = null, $formatted = false ) {
	$base          = eac_get_base_currency();
	$from_currency = eac_get_currency( $from );
	$amount        = eac_sanitize_money( $amount, $from );
	// No need to convert same currency.
	if ( $base === $from ) {
		return $amount;
	}

	if ( empty( $from_rate ) ) {
		$from_rate = $from_currency ? $from_currency->exchange_rate : 1;
	}

	// First check if mathematically possible.
	if ( 0 === $from_rate ) {
		$amount = 0;
	} else {
		// Divide by rate.
		$precision = $from_currency ? $from_currency->precision : 2;
		$amount    = round( $amount / $from_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $formatted ) {
		$amount = eac_format_money( $amount, $base );
	}

	return $amount;
}

/**
 * Convert price from one currency to another.
 *
 * @param string      $amount Amount.
 * @param string      $from Convert from currency code.
 * @param string|null $to Convert to currency code.
 * @param string|null $from_rate Convert from currency rate.
 * @param string|null $to_rate Convert to currency rate.
 * @param bool        $formatted Whether to format the price or not.
 *
 * @return float|int|string
 * @since 1.0.2
 */
function eac_convert_money( $amount, $from, $to, $from_rate = null, $to_rate = null, $formatted = false ) {

	if ( ! is_numeric( $amount ) ) {
		$amount = eac_sanitize_money( $amount, $from );
	}

	if ( empty( $from_rate ) ) {
		$from_currency = eac_get_currency( $from );
		$from_rate     = $from_currency ? $from_currency->exchange_rate : 1;
	}

	if ( empty( $to_rate ) ) {
		$to_currency = eac_get_currency( $to );
		$to_rate     = $to_currency ? $to_currency->exchange_rate : 1;
	}

	// No need to convert same currency.
	if ( $from !== $to && $amount > 0 && $from_rate > 0 ) {
		$currency  = eac_get_currency( $to );
		$precision = $currency ? $currency->precision : 2;
		$amount    = round( $amount / $from_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $amount > 0 && $to_rate > 0 ) {
		$currency  = eac_get_currency( $to );
		$precision = $currency ? $currency->precision : 2;
		$amount    = round( $amount * $to_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $formatted ) {
		$amount = eac_format_money( $amount, $to );
	}

	return $amount;
}
