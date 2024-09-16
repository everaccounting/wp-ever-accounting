<?php
/**
 * Core functions
 *
 * @version  1.1.0
 * @category Functions
 * @package  EverAccounting\Functions
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/Functions/accounts.php';
require_once __DIR__ . '/Functions/categories.php';
require_once __DIR__ . '/Functions/contacts.php';
require_once __DIR__ . '/Functions/currencies.php';
require_once __DIR__ . '/Functions/documents.php';
require_once __DIR__ . '/Functions/formatters.php';
require_once __DIR__ . '/Functions/items.php';
require_once __DIR__ . '/Functions/notes.php';
require_once __DIR__ . '/Functions/misc.php';
require_once __DIR__ . '/Functions/reports.php';
require_once __DIR__ . '/Functions/taxes.php';
require_once __DIR__ . '/Functions/templates.php';
require_once __DIR__ . '/Functions/transactions.php';
require_once __DIR__ . '/Functions/updates.php';

/**
 * Get base currency code
 *
 * @since 1.0.2
 * @return string
 */
function eac_get_base_currency() {
	$currency = get_option( 'eac_base_currency', 'USD' );

	return apply_filters( 'ever_accounting_base_currency', strtoupper( $currency ) );
}

/**
 * Get the default currency code.
 *
 * @since 1.0.2
 * @return string
 */
function eac_currency_code() {
	return get_option( 'eac_base_currency', 'USD' );
}


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
function eac_format_amount( $amount, $code = null ) {
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
function eac_sanitize_amount( $amount, $from_code = null ) {
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
 * @since 1.0.2
 * @return float|int|string
 */
function eac_convert_currency_from_base( $amount, $to, $to_rate = null ) {
	$default = eac_get_base_currency();
	$amount  = eac_sanitize_amount( $amount, $to );
	// No need to convert same currency.
	if ( $default === $to ) {
		return $amount;
	}
	$currency  = eac_get_currency( $to );
	$decimals = $currency ? $currency->decimals : 2;
	if ( is_null( $to_rate ) ) {
		$to_rate = $currency ? $currency->exchange_rate : 1;
	}

	// First check if mathematically possible.
	if ( 0 === $to_rate ) {
		return 0;
	}

	// Multiply by rate.
	return round( $amount * $to_rate, $decimals, PHP_ROUND_HALF_UP );
}

/**
 * Convert price from any currency to default.
 *
 * @param string $amount Amount.
 * @param string $from Convert from currency code.
 * @param string $from_rate Convert from currency rate.
 * @param bool   $formatted Whether to format the price or not.
 *
 * @since 1.0.2
 * @return float|int|string
 */
function eac_convert_currency_to_base( $amount, $from, $from_rate = null, $formatted = false ) {
	$base          = eac_get_base_currency();
	$from_currency = eac_get_currency( $from );
	$amount        = eac_sanitize_amount( $amount, $from );
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
		$decimals = $from_currency ? $from_currency->decimals : 2;
		$amount    = round( $amount / $from_rate, $decimals, PHP_ROUND_HALF_UP );
	}

	if ( $formatted ) {
		$amount = eac_format_amount( $amount, $base );
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
 * @since 1.0.2
 * @return float|int|string
 */
function eac_convert_currency( $amount, $from, $to, $from_rate = null, $to_rate = null, $formatted = false ) {

	if ( ! is_numeric( $amount ) ) {
		$amount = eac_sanitize_amount( $amount, $from );
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
		$decimals = $currency ? $currency->decimals : 2;
		$amount    = round( $amount / $from_rate, $decimals, PHP_ROUND_HALF_UP );
	}

	if ( $amount > 0 && $to_rate > 0 ) {
		$currency  = eac_get_currency( $to );
		$decimals = $currency ? $currency->decimals : 2;
		$amount    = round( $amount * $to_rate, $decimals, PHP_ROUND_HALF_UP );
	}

	if ( $formatted ) {
		$amount = eac_format_amount( $amount, $to );
	}

	return $amount;
}

/**
 * Get only numbers from the string.
 *
 * @param string   $number Number to get only numbers from.
 *
 * @param bool|int $decimals Allow decimal. If true, then allow decimal. If false, then only allow integers. If a number, then allow that many decimal places.
 *
 * @return int|float|null
 * @since 1.0.2
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
 * @return array
 * @since 1.0.2
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
/**
 * Get formatted company address.
 *
 * @return string
 * @since 1.1.6
 */
function eac_get_formatted_company_address() {
	return eac_get_formatted_address(
		array(
			'name'      => get_option( 'eac_company_name', get_bloginfo( 'name' ) ),
			'address_1' => get_option( 'eac_company_address_1' ),
			'address_2' => get_option( 'eac_company_address_2' ),
			'city'      => get_option( 'eac_company_city' ),
			'state'     => get_option( 'eac_company_state' ),
			'postcode'  => get_option( 'eac_company_postcode' ),
			'country'   => get_option( 'eac_company_country' ),
			'phone'     => get_option( 'eac_company_phone' ),
			'email'     => get_option( 'eac_company_email' ),
		)
	);
}
