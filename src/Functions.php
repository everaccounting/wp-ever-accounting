<?php

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/Functions/Accounts.php';
require_once __DIR__ . '/Functions/Categories.php';
require_once __DIR__ . '/Functions/Contacts.php';
require_once __DIR__ . '/Functions/Currencies.php';
require_once __DIR__ . '/Functions/Deprecated.php';
require_once __DIR__ . '/Functions/Documents.php';
require_once __DIR__ . '/Functions/Formatters.php';
require_once __DIR__ . '/Functions/Items.php';
require_once __DIR__ . '/Functions/Media.php';
require_once __DIR__ . '/Functions/Misc.php';
require_once __DIR__ . '/Functions/Notes.php';
require_once __DIR__ . '/Functions/Taxes.php';
require_once __DIR__ . '/Functions/Transactions.php';

/**
 * Get default currency code
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_default_currency() {
	$currency = get_option( 'eac_default_currency', 'USD' );

	return apply_filters( 'ever_accounting_default_currency', $currency );
}

/**
 * Get company currency code
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_base_currency() {
	$currency = get_option( 'eac_currency_code', 'USD' );

	return apply_filters( 'ever_accounting_base_currency', $currency );
}

/**
 * Get default account id.
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_account_id() {
	$account_id = get_option( 'eac_account_id', '1' );

	return apply_filters( 'ever_accounting_default_account_id', $account_id );
}

/**
 * Get default payment method.
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_payment_method() {
	$payment_method = get_option( 'eac_payment_method', 'cash' );

	return apply_filters( 'ever_accounting_default_payment_method', $payment_method );
}

/**
 * Get only numbers from the string.
 *
 * @param string $number Number to get only numbers from.
 *
 * @param bool   $allow_decimal Allow decimal.
 *
 * @since 1.0.2
 *
 * @return int|float|null
 */
function eac_sanitize_number( $number, $allow_decimal = true ) {
	// Convert multiple dots to just one.
	$number = preg_replace( '/\.(?![^.]+$)|[^0-9.-]/', '', eac_clean( $number ) );

	if ( $allow_decimal ) {
		return (float) preg_replace( '/[^0-9.-]/', '', $number );
	}

	return (int) preg_replace( '/[^0-9]/', '', $number );
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
 * @param bool   $convert Convert to default currency.
 *
 * @since 1.0.2
 *
 * @return float|int
 */
function eac_sanitize_price( $amount, $from_code = null, $convert = false ) {
	$default_currency = eac_get_default_currency();
	// Get the default currency if not passed.
	if ( is_null( $from_code ) ) {
		$from_code = $default_currency;
	}

	// Retrieve currency object.
	$currency = eac_get_currency( $from_code );

	if ( ! is_numeric( $amount ) ) {
		// Retrieve the thousand and decimal separator from currency object.
		$thousand_mark = $currency ? $currency->get_thousand_sep() : ',';
		$decimal_mark  = $currency ? $currency->get_decimal_sep() : '.';
		// Remove currency symbol from amount.
		$amount = str_replace( $currency->get_symbol(), '', $amount );
		// Remove any non-numeric characters except a thousand and decimal separators.
		$amount = preg_replace( '/[^0-9\\' . $thousand_mark . '\\' . $decimal_mark . '\-\+]/', '', $amount );
		// Replace a thousand and decimal separators with empty string and dot respectively.
		$amount = str_replace( array( $thousand_mark, $decimal_mark ), array( '', '.' ), $amount );
		// Convert to int if amount is a whole number, otherwise convert to float.
		if ( preg_match( '/^([\-\+])?\d+$/', $amount ) ) {
			$amount = (int) $amount;
		} elseif ( preg_match( '/^([\-\+])?\d+\.\d+$/', $amount ) ) {
			$amount = (float) $amount;
		} else {
			$amount = 0;
		}
	}

	// Convert to default currency if needed.
	if ( $convert && $from_code !== $default_currency && 0 !== $amount ) {
		$from_rate = $currency && $currency->get_rate() ? $currency->get_rate() : 1;
		$precision = $currency ? $currency->get_precision() : 0;
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
function eac_format_price( $amount, $code = null ) {
	if ( ! is_numeric( $amount ) ) {
		$amount = eac_sanitize_price( $amount, $code );
	}

	if ( is_null( $code ) ) {
		$code = eac_get_default_currency();
	}

	$currency      = eac_get_currency( $code );
	$precision     = $currency ? $currency->get_precision() : 0;
	$thousand_mark = $currency ? $currency->get_thousand_sep() : ',';
	$decimal_mark  = $currency ? $currency->get_decimal_sep() : '.';
	$position      = $currency ? $currency->get_position() : 'before';
	$symbol        = $currency ? $currency->get_symbol() : '$';
	$prefix        = 'before' === $position ? $symbol : '';
	$suffix        = 'after' === $position ? $symbol : '';
	$is_negative   = $amount < 0;
	$amount        = $is_negative ? - $amount : $amount;

	$value = number_format( $amount, $precision, $decimal_mark, $thousand_mark );

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
function eac_price_from_default( $amount, $to, $to_rate = null ) {
	$default = eac_get_default_currency();
	$amount  = eac_sanitize_price( $amount, $to );
	// No need to convert same currency.
	if ( $default === $to ) {
		return $amount;
	}
	$to_currency = eac_get_currency_rate( $to );
	if ( is_null( $to_rate ) ) {
		$to_rate = $to_currency ? $to_currency->get_rate() : 1;
	}
	$precision = $to_currency ? $to_currency->get_precision() : 0;

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
function eac_price_to_default( $amount, $from, $from_rate = null, $formatted = false ) {
	$default = eac_get_default_currency();
	$amount  = eac_sanitize_price( $amount, $from );
	// No need to convert same currency.
	if ( $default === $from ) {
		return $amount;
	}

	if ( is_null( $from_rate ) ) {
		$from_currency = eac_get_currency( $from );
		$from_rate     = $from_currency ? $from_currency->get_rate() : 1;
	}
	$default_currency = eac_get_currency( $default );
	$precision        = $default_currency ? $default_currency->get_precision() : 0;

	// First check if mathematically possible.
	if ( 0 === $from_rate ) {
		$amount = 0;
	} else {
		// Divide by rate.
		$amount = round( $amount / $from_rate, $precision, PHP_ROUND_HALF_UP );
	}

	if ( $formatted ) {
		$amount = eac_format_price( $amount, $default );
	}

	return $amount;
}

/**
 * Convert price from one currency to another.
 *
 * @param string $amount Amount.
 * @param string $from Convert from currency code.
 * @param string $to Convert to currency code.
 * @param string $from_rate Convert from currency rate.
 * @param string $to_rate Convert to currency rate.
 *
 * @since 1.0.2
 *
 * @return float|int|string
 */
function eac_convert_price( $amount, $from, $to = null, $from_rate = null, $to_rate = null ) {
	if ( is_null( $to ) ) {
		$to = eac_get_default_currency();
	}

	if ( is_null( $from_rate ) ) {
		$from_currency = eac_get_currency( $from );
		$from_rate     = $from_currency ? $from_currency->get_rate() : 1;
	}

	if ( is_null( $to_rate ) ) {
		$to_currency = eac_get_currency( $to );
		$to_rate     = $to_currency ? $to_currency->get_rate() : 1;
	}

	if ( $from !== $to ) {
		$amount = eac_price_to_default( $amount, $to, $to_rate );
	}

	return eac_price_from_default( $amount, $from, $from_rate );
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

