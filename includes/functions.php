<?php
/**
 * Core functions
 *
 * @version  1.1.0
 * @category Functions
 * @package  EverAccounting\Functions
 */

use EverAccounting\Utilities\I18n;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/Functions/formatters.php';
require_once __DIR__ . '/Functions/misc.php';
require_once __DIR__ . '/Functions/reports.php';
require_once __DIR__ . '/Functions/taxes.php';
require_once __DIR__ . '/Functions/templates.php';
require_once __DIR__ . '/Functions/updates.php';

/**
 * Get the base currency.
 *
 * @since 1.0.0
 * @return string
 */
function eac_base_currency() {
	$currency = get_option( 'eac_base_currency', 'USD' );

	return apply_filters( 'eac_base_currency', strtoupper( $currency ) );
}

/**
 * Get the currency symbol.
 *
 * @param string $currency The currency.
 *
 * @since 1.0.0
 * @return string
 */
function eac_currency_symbol( $currency = null ) {
	$currency = empty( $currency ) ? eac_base_currency() : strtoupper( $currency );

	$currencies = eac_get_currencies();

	return isset( $currencies[ $currency ] ) ? $currencies[ $currency ]['symbol'] : $currency;
}

/**
 * Get the currency precision.
 *
 * @param string $currency The currency.
 *
 * @since 1.0.0
 * @return int The currency precision.
 */
function eac_currency_precision( $currency = null ) {
	$currency = empty( $currency ) ? eac_base_currency() : strtoupper( $currency );

	$currencies = eac_get_currencies();

	return isset( $currencies[ $currency ] ) ? $currencies[ $currency ]['precision'] : 2;
}

/**
 * Get the currency position.
 *
 * @param string $currency The currency.
 *
 * @since 1.0.0
 * @return string The currency position.
 */
function eac_currency_position( $currency = null ) {
	$currency = empty( $currency ) ? eac_base_currency() : strtoupper( $currency );

	$currencies = eac_get_currencies();

	return isset( $currencies[ $currency ] ) ? $currencies[ $currency ]['position'] : 'before';
}

/**
 * Get the currency thousands separator.
 *
 * @param string $currency The currency.
 *
 * @since 1.0.0
 * @return string The currency thousand separator.
 */
function eac_thousand_separator( $currency = null ) {
	$currency = empty( $currency ) ? eac_base_currency() : strtoupper( $currency );

	$currencies = eac_get_currencies();

	return isset( $currencies[ $currency ] ) ? $currencies[ $currency ]['thousand_separator'] : ',';
}

/**
 * Get the currency decimal separator.
 *
 * @param string $currency The currency.
 *
 * @since 1.0.0
 * @return string The currency decimal separator.
 */
function eac_decimal_separator( $currency = null ) {
	$currency = empty( $currency ) ? eac_base_currency() : strtoupper( $currency );

	$currencies = eac_get_currencies();

	return isset( $currencies[ $currency ] ) ? $currencies[ $currency ]['decimal_separator'] : '.';
}

/**
 * Get currencies.
 *
 * @since 1.0.0
 * @return array
 */
function eac_get_currencies() {
	$base_currency = eac_base_currency();
	$currencies    = I18n::get_currencies();

	$currencies[ $base_currency ] = array_merge(
		$currencies[ $base_currency ],
		array(
			'code'               => $base_currency,
			'rate'               => 1,
			'precision'          => get_option( 'eac_currency_precision', 2 ),
			'position'           => get_option( 'eac_currency_position', 'before' ),
			'thousand_separator' => get_option( 'eac_thousand_separator', ',' ),
			'decimal_separator'  => get_option( 'eac_decimal_separator', '.' ),
		)
	);

	return apply_filters( 'eac_currencies', $currencies );
}

/**
 * Get exchange rate for a currency.
 *
 * @param string $currency The currency.
 *
 * @since 1.0.0
 * @return float
 */
function eac_get_exchange_rate( $currency = null ) {
	$currency = strtoupper( $currency );
	if ( empty( $currency ) || $currency === eac_base_currency() ) {
		return 1;
	}

	$currencies = eac_get_currencies();

	return isset( $currencies[ $currency ] ) ? $currencies[ $currency ]['exchange_rate'] : 1;
}

/**
 * Format price with currency code & number format
 *
 * @param string $amount Amount.
 * @param string $currency Currency code.
 *
 * @since 1.0.2
 * @return string
 */
function eac_format_amount( $amount, $currency = null ) {
	$currency_symbol    = eac_currency_symbol( $currency );
	$currency_precision = eac_currency_precision( $currency );
	$currency_position  = eac_currency_position( $currency );
	$thousand_separator = eac_thousand_separator( $currency );
	$decimal_separator  = eac_decimal_separator( $currency );

	if ( ! is_numeric( $amount ) ) {
		$amount = eac_sanitize_amount( $amount, $currency );
	}
	$negative = $amount < 0;
	$prefix   = 'before' === $currency_position ? $currency_symbol : '';
	$suffix   = 'after' === $currency_position ? $currency_symbol : '';

	$amount = $negative ? - $amount : $amount;
	$amount = number_format( $amount, $currency_precision, $decimal_separator, $thousand_separator );

	return $negative ? sprintf( '-%s%s%s', $prefix, $amount, $suffix ) : sprintf( '%s%s%s', $prefix, $amount, $suffix );
}

/**
 * Sanitize price before inserting into database.
 *
 * @param string $amount Amount.
 * @param string $currency Currency.
 *
 * @since 1.0.2
 * @return float|int
 */
function eac_sanitize_amount( $amount, $currency = null ) {
	if ( ! is_numeric( $amount ) ) {
		$currency_symbol    = eac_currency_symbol( $currency );
		$thousand_separator = eac_thousand_separator( $currency );
		$decimal_separator  = eac_decimal_separator( $currency );

		// Remove currency symbol.
		$amount = str_replace( $currency_symbol, '', $amount );
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
 * Convert price from one currency to another.
 *
 * @param string      $amount Amount.
 * @param string      $from_currency Convert from currency.
 * @param string|null $to_currency Convert to currency.
 * @param string|null $from_rate Convert from currency rate.
 * @param string|null $to_rate Convert to currency rate.
 * @param bool        $formatted Whether to format the price or not.
 *
 * @since 1.0.2
 * @return float|int|string
 */
function eac_convert_currency( $amount, $from_currency, $to_currency = null, $from_rate = null, $to_rate = null, $formatted = false ) {
	if ( ! is_numeric( $amount ) ) {
		$amount = eac_sanitize_amount( $amount, $from_currency );
	}

	$from_rate = empty( $from_rate ) ? eac_get_exchange_rate( $from_currency ) : $from_rate;
	$to_rate   = empty( $to_rate ) ? eac_get_exchange_rate( $to_currency ) : $to_rate;

	// No need to convert same currency.
	if ( $from_currency !== $to_currency && $amount > 0 && $from_rate > 0 ) {
		$amount = round( $amount / $from_rate, eac_currency_precision( $to_currency ) ) * $to_rate;
	}

	if ( $amount > 0 && $to_rate > 0 ) {
		$amount = round( $amount * $to_rate, eac_currency_precision( $to_currency ) );
	}

	return $formatted ? eac_format_amount( $amount, $to_currency ) : $amount;
}

/**
 * Get only numbers from the string.
 *
 * @param string   $number Number to get only numbers from.
 *
 * @param bool|int $decimals Allow decimal. If true, then allow decimal. If false, then only allow integers. If a number, then allow that many decimal places.
 *
 * @since 1.0.2
 * @return int|float|null
 */
function eac_sanitize_number( $number, $decimals = 2 ) {
	// Convert multiple dots to just one.
	$number = preg_replace( '/\.(?![^.]+$)|[^0-9.-]/', '', eac_clean( $number ) );

	if ( $decimals ) {
		$number = (float) preg_replace( '/[^0-9.-]/', '', $number );
		// if allow decimal is a number, then use that as the number of decimals.
		if ( is_numeric( $decimals ) ) {
			$number = number_format( floatval( $number ), $decimals, '.', '' );
		}

		return $number;
	}

	return (int) preg_replace( '/[^0-9]/', '', $number );
}

/**
 * Round a number using the built-in `round` function, but unless the value to round is numeric
 * (a number or a string that can be parsed as a number), apply 'floatval' first to it
 * (so it will convert it to 0 in most cases).
 *
 * @param mixed $val The value to round.
 * @param int   $decimals The optional number of decimal digits to round to.
 * @param int   $mode A constant to specify the mode in which rounding occurs.
 *
 * @return float The value rounded to the given decimals as a float, or the supplied default value.
 */
function eac_round_number( $val, $decimals = 6, $mode = PHP_ROUND_HALF_UP ) {
	$val = eac_sanitize_number( $val, $decimals );

	return round( $val, $decimals, $mode );
}

/**
 * Get payment methods.
 *
 * @since 1.0.2
 * @return array
 */
function eac_get_payment_methods() {
	return apply_filters(
		'ever_accounting_payment_methods',
		array(
			'cash'          => esc_html__( 'Cash', 'wp-ever-accounting' ),
			'check'         => esc_html__( 'Cheque', 'wp-ever-accounting' ),
			'credit_card'   => esc_html__( 'Credit Card', 'wp-ever-accounting' ),
			'debit_card'    => esc_html__( 'Debit Card', 'wp-ever-accounting' ),
			'bank_transfer' => esc_html__( 'Bank Transfer', 'wp-ever-accounting' ),
			'paypal'        => esc_html__( 'PayPal', 'wp-ever-accounting' ),
			'other'         => esc_html__( 'Other', 'wp-ever-accounting' ),
		)
	);
}
