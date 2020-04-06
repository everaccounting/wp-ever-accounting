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
 *
 * @return bool|string
 */
function eaccounting_sanitize_date( $date, $format = 'Y-m-d' ) {
	$d = DateTime::createFromFormat( $format, $date );

	return $d && $d->format( $format ) === $date ? $date : false;
}

/**
 * Instance of money class.
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
