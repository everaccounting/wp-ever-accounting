<?php
defined( 'ABSPATH' ) || exit();

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 *
 * @return string|array
 */
function eaccounting_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'eaccounting_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Sanitize date
 * since 1.0.0
 *
 * @param $date
 * @param string $format
 * @param string $fallback
 *
 * @return bool|string
 */
function eaccounting_sanitize_date( $date, $fallback = false, $format = 'Y-m-d' ) {
	$formatted = date( $format, strtotime( $date ) );
	$d         = DateTime::createFromFormat( $format, $formatted );

	return $d && $d->format( $format ) === $formatted ? $formatted : $fallback;
}

/**
 * Instance of money class.
 *
 * For formatting with currency code
 * eaccounting_money( 100000, 'USD', true )->format()
 * For inserting into database
 * eaccounting_money( "$100,000", "USD", false )->getAmount()
 *
 * @param mixed $amount
 * @param string $currency
 * @param bool $convert
 *
 * @return EAccounting_Money
 */
function eaccounting_money( $amount, $currency = 'USD', $convert = false ) {
	return new EAccounting_Money( $amount, eaccounting_currency( $currency ), $convert );
}

/**
 * Instance of currency class.
 *
 * @param string $currency
 *
 * @return EAccounting_Currency
 */
function eaccounting_currency( $currency ) {
	return new EAccounting_Currency( $currency );
}

/**
 * Sanitize price for inserting into database
 * since 1.0.0
 *
 * @param $amount
 * @param $code
 *
 * @return float|int
 */
function eaccounting_sanitize_price( $amount, $code ) {
	return eaccounting_money( $amount, $code, false )->getAmount();
}

/**
 * Format price with currency code & number format
 *
 * since 1.0.0
 *
 * @param $amount
 * @param $code
 *
 * @return string
 */
function eaccounting_format_price( $amount, $code = null ) {
	if ( $code == null ) {
		$code = eaccounting_get_default_currency();
	}

	return eaccounting_money( $amount, $code, true )->format();
}

/**
 * @param $method
 * @param $amount
 * @param $from
 * @param $to
 * @param $rate
 * @param bool $format
 *
 * @return float|int|string
 */
function eaccounting_convert_price( $method, $amount, $from, $to, $rate, $format = false ) {
	$money = eaccounting_money( $amount, $to );
	// No need to convert same currency
	if ( $from == $to ) {
		return $format ? $money->format() : $money->getAmount();
	}

	try {
		$money = $money->$method( (double) $rate );
	} catch ( Exception $e ) {
		return 0;
	}

	return $format ? $money->format() : $money->getAmount();
}

/**
 *
 * @param $amount
 * @param $to
 * @param $rate
 * @param bool $format
 *
 * @return float|int|string
 * @since 1.0.2
 */
function eaccounting_price_convert_from_default( $amount, $to, $rate, $format = false ) {
	$code = eaccounting_get_default_currency();

	return eaccounting_convert_price( 'multiply', $amount, $code, $to, $rate, $format );
}

function eaccounting_price_convert_to_default( $amount, $from, $rate, $format = false ) {
	$code = eaccounting_get_default_currency();

	return eaccounting_convert_price( 'divide', $amount, $from, $code, $rate, $format );
}

/**
 * @param $args
 * @param $column
 * @param $table
 *
 * @return string
 * @since 1.0.1
 */
function eaccounting_parse_date_query( $args, $column, $table ) {
	global $wpdb;
	$query = '';
	if ( ! empty( $query ) && is_string( $query ) ) {
		$query = $wpdb->prepare( " AND $table.$column= %s", sanitize_text_field( $args ) );
	} elseif ( is_array( $args ) ) {
		if ( ! empty( $args['start'] ) ) {
			$query .= $wpdb->prepare( " AND $table.$column >= %s", sanitize_text_field( $args['start'] ) );
		}
		if ( is_array( $args ) && ! empty( $args['end'] ) ) {
			$query .= $wpdb->prepare( " AND $table.$column <= %s", sanitize_text_field( $args['end'] ) );
		}
	}

	return $query;
}
