<?php

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/Functions/Accounts.php';
require_once __DIR__ . '/Functions/Contacts.php';
require_once __DIR__ . '/Functions/Currencies.php';
require_once __DIR__ . '/Functions/Deprecated.php';
require_once __DIR__ . '/Functions/Documents.php';
require_once __DIR__ . '/Functions/Formatters.php';
require_once __DIR__ . '/Functions/Media.php';
require_once __DIR__ . '/Functions/Misc.php';
require_once __DIR__ . '/Functions/Notes.php';
require_once __DIR__ . '/Functions/Products.php';
require_once __DIR__ . '/Functions/Reports.php';
require_once __DIR__ . '/Functions/Taxes.php';
require_once __DIR__ . '/Functions/Templates.php';
require_once __DIR__ . '/Functions/Terms.php';
require_once __DIR__ . '/Functions/Transactions.php';

/**
 * Get base currency code
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_base_currency() {
	$currency = get_option( 'eac_base_currency', 'USD' );

	return apply_filters( 'ever_accounting_base_currency', $currency );
}

/**
 * Get base country.
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_base_country() {
	$country = get_option( 'eac_company_country', 'US' );

	return apply_filters( 'ever_accounting_base_country', $country );
}

/**
 * Get only numbers from the string.
 *
 * @param string   $number Number to get only numbers from.
 *
 * @param bool|int $decimals Allow decimal. If true, then allow decimal. If false, then only allow integers. If a number, then allow that many decimal places.
 *
 * @since 1.0.2
 *
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
 * @param int   $precision The optional number of decimal digits to round to.
 * @param int   $mode A constant to specify the mode in which rounding occurs.
 *
 * @return float The value rounded to the given precision as a float, or the supplied default value.
 */
function eac_round_number( $val, $precision = 6, $mode = PHP_ROUND_HALF_UP ) {
	$val = eac_sanitize_number( $val, $precision );

	return round( $val, $precision, $mode );
}

/**
 * Get only numbers from the string.
 *
 * @param string $number Number to get only numbers from.
 *
 * @param int    $decimals Number of decimals.
 * @param bool   $trim_zeros Trim zeros.
 *
 * @since 1.0.2
 *
 * @return int|float|null
 */
function eac_format_decimal( $number, $decimals = 4, $trim_zeros = false ) {

	// Convert multiple dots to just one.
	$number = preg_replace( '/\.(?![^.]+$)|[^0-9.-]/', '', eac_clean( $number ) );

	if ( is_numeric( $decimals ) ) {
		$number = number_format( floatval( $number ), $decimals, '.', '' );
	}

	if ( $trim_zeros && strstr( $number, '.' ) ) {
		$number = rtrim( rtrim( $number, '0' ), '.' );
	}

	return $number;
}

/**
 * Sanitize price for inserting into database.
 *
 * When converting to default currency, the amount will convert to default currency
 * with the exchange rate of the currency at the time of the transaction.
 *
 * @param string $amount Amount.
 * @param string $from_code If not passed will be used default currency.
 * @param bool   $to_base Convert to base currency.
 *
 * @since 1.0.2
 *
 * @return float|int
 */
function eac_sanitize_money( $amount, $from_code = null, $to_base = false ) {
	$currencies    = eac_get_currencies();
	$base_currency = eac_get_base_currency();
	// Get the base currency if not passed.
	if ( is_null( $from_code ) ) {
		$from_code = $base_currency;
	}

	if ( ! is_numeric( $amount ) ) {
		// Retrieve the thousand and decimal separator from currency object.
		$thousand_sep = $currencies[ $from_code ]['thousand_sep'] || ','; // Default to comma.
		$decimal_sep  = $currencies[ $from_code ]['decimal_sep'] || '.'; // Default to dot.
		$symbol       = $currencies[ $from_code ]['symbol'] || ''; // Default to empty string.
		// Remove currency symbol from amount.
		$amount = str_replace( $symbol, '', $amount );
		// Remove any non-numeric characters except a thousand and decimal separators.
		$amount = preg_replace( '/[^0-9\\' . $thousand_sep . '\\' . $decimal_sep . '\-\+]/', '', $amount );
		// Replace a thousand and decimal separators with empty string and dot respectively.
		$amount = str_replace( array( $thousand_sep, $decimal_sep ), array( '', '.' ), $amount );
		// Convert to int if amount is a whole number, otherwise convert to float.
		if ( preg_match( '/^([\-\+])?\d+$/', $amount ) ) {
			$amount = (int) $amount;
		} elseif ( preg_match( '/^([\-\+])?\d+\.\d+$/', $amount ) ) {
			$amount = (float) $amount;
		} else {
			$amount = 0;
		}
	}

	// Convert to base currency if needed.
	if ( $to_base && $from_code !== $base_currency && 0 !== $amount ) {
		$from_rate = eac_get_currency_rate( $from_code );
		$precision = eac_get_currency_precision( $from_code );
		// Divide by rate.
		$amount = round( $amount / $from_rate, $precision, PHP_ROUND_HALF_UP );
	}

	return $amount;
}

/**
 * Format price with currency code & number format
 *
 * @param string $amount Amount.
 *
 * @param string $code If not passed will be used default currency.
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_format_money( $amount, $code = null ) {
	if ( ! is_numeric( $amount ) ) {
		$amount = eac_sanitize_money( $amount, $code );
	}

	if ( is_null( $code ) ) {
		$code = eac_get_base_currency();
	}

	$precision    = eac_get_currency_precision( $code );
	$thousand_sep = eac_get_currency_thousand_separator( $code );
	$decimal_sep  = eac_get_currency_decimal_separator( $code );
	$position     = eac_get_currency_position( $code );
	$symbol       = eac_get_currency_symbol( $code );
	$prefix       = 'before' === $position ? $symbol : '';
	$suffix       = 'after' === $position ? $symbol : '';
	$is_negative  = $amount < 0;
	$amount       = $is_negative ? - $amount : $amount;

	$value = number_format( $amount, $precision, $decimal_sep, $thousand_sep );

	return ( $is_negative ? '-' : '' ) . $prefix . $value . $suffix;
}

/**
 * Convert price from default to any other currency.
 *
 * @param string $amount Amount.
 * @param string $to Convert to currency code.
 * @param string $to_rate Convert to currency rate.
 *
 * @since 1.0.2
 *
 * @return float|int|string
 */
function eac_money_from_base( $amount, $to, $to_rate = null ) {
	$default = eac_get_base_currency();
	$amount  = eac_sanitize_money( $amount, $to );
	// No need to convert same currency.
	if ( $default === $to ) {
		return $amount;
	}

	if ( is_null( $to_rate ) ) {
		$to_rate = eac_get_currency_rate( $to );
	}

	$precision = eac_get_currency_precision( $to );

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
 * @since 1.0.2
 *
 * @return float|int|string
 */
function eac_money_to_base( $amount, $from, $from_rate = null, $formatted = false ) {
	$default = eac_get_base_currency();
	$amount  = eac_sanitize_money( $amount, $from );
	// No need to convert same currency.
	if ( $default === $from ) {
		return $amount;
	}

	if ( is_null( $from_rate ) ) {
		$from_rate = eac_get_currency_rate( $from );
	}
	$precision = eac_get_currency_precision( $default );

	// First check if mathematically possible.
	if ( 0 === $from_rate ) {
		$amount = 0;
	} else {
		// Divide by rate.
		$amount = round( $amount / $from_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $formatted ) {
		$amount = eac_format_money( $amount, $default );
	}

	return $amount;
}

/**
 * Convert price from one currency to another.
 *
 * @param string      $amount Amount.
 * @param string      $from Convert from currency code.
 * @param string|null $to Convert to currency code.
 * @param string|null $to_rate Convert to currency rate.
 * @param string|null $from_rate Convert from currency rate.
 * @param bool        $formatted Whether to format the price or not.
 *
 * @since 1.0.2
 *
 * @return float|int|string
 */
function eac_convert_money( $amount, $from, $to = null, $to_rate = null, $from_rate = null, $formatted = false ) {
	if ( is_null( $to ) ) {
		$to = eac_get_base_currency();
	}
	if ( is_null( $from_rate ) ) {
		$from_rate = eac_get_currency_rate( $from );
	}
	if ( is_null( $to_rate ) ) {
		$to_rate = eac_get_currency_rate( $to );
	}
	if ( ! is_numeric( $amount ) ) {
		$amount = eac_sanitize_money( $amount, $from );
	}

	// No need to convert same currency.
	if ( $from !== $to && $amount > 0 && $from_rate > 0 ) {
		$precision = eac_get_currency_precision( $to );
		$amount    = round( $amount / $from_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $amount > 0 && $to_rate > 0 ) {
		$precision = eac_get_currency_precision( $to );
		$amount    = round( $amount * $to_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $formatted ) {
		$amount = eac_format_money( $amount, $to );
	}

	return $amount;
}

/**
 * Add notice.
 *
 * @param string $message Message.
 * @param string $type Type.
 * @param array  $args Args.
 *
 * @since 1.1.6
 *
 * @return void
 */
function eac_add_notice( $message, $type = 'success', $args = array() ) {
	\EverAccounting\Notices::add_notice( $message, $type, $args );
}

/**
 * Add message.
 *
 * @param string $message Message.
 * @param string $type Type.
 * @param array  $args Args.
 *
 * @since 1.1.6
 *
 * @return void
 */
function eac_add_message( $message, $type = 'success', $args = array() ) {
	\EverAccounting\Notices::add_message( $message, $type, $args );
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

/**
 * Get time zone.
 *
 * @since 1.1.6
 * @return string
 */
function eac_get_timezone() {
	// Default return value.
	$retval = 'UTC';

	// Get some useful values.
	$timezone   = get_option( 'timezone_string' );
	$gmt_offset = get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS;

	// Use timezone string if it's available.
	if ( ! empty( $timezone ) ) {
		$retval = $timezone;

		// Use GMT offset to calculate.
	} elseif ( is_numeric( $gmt_offset ) ) {
		$hours   = abs( floor( $gmt_offset / HOUR_IN_SECONDS ) );
		$minutes = abs( floor( ( $gmt_offset / MINUTE_IN_SECONDS ) % MINUTE_IN_SECONDS ) );
		$math    = ( $gmt_offset >= 0 ) ? '+' : '-';
		$value   = ! empty( $minutes ) ? "{$hours}:{$minutes}" : $hours;
		$retval  = "GMT{$math}{$value}";
	}

	return $retval;
}

