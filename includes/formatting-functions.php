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
 * get options
 * since 1.0.0
 *
 * @param string $key
 * @param string $section
 * @param bool $default
 *
 * @return string|array
 */
function eaccounting_get_option( $key = '', $section = 'eaccounting_general', $default = false ) {
	$option = get_option( $section, [] );
	$value  = ! empty( $option[ $key ] ) ? $option[ $key ] : $default;
	$value  = apply_filters( 'eaccounting_get_option', $value, $key, $default );

	return apply_filters( 'eaccounting_get_option_' . $key, $value, $key, $default );
}

/**
 * Decimal price_currency
 * since 1.0.0
 * @return array|string
 */
function eaccounting_get_price_currency() {
	return eaccounting_get_option( 'currency', 'eaccounting_localisation', 'USD' );
}

/**
 * Decimal price_currency_symbol
 * since 1.0.0
 * @return array|string
 */
function eaccounting_get_price_currency_symbol() {
	$currency = eaccounting_get_price_currency();
//	$symbols = eaccounting_get_currency_symbols();
//	if ( array_key_exists( $currency, $symbols ) ) {
//		return $symbols[ $currency ];
//	}

//	return $symbols['USD'];
}


/**
 * Return the number of decimals after the decimal point.
 *
 * since 1.0.0
 * @return int
 */
function eaccounting_get_price_precision() {
	return eaccounting_get_option( 'precision', 'eaccounting_localisation', '2' );
}


/**
 * Decimal sep
 * since 1.0.0
 * @return array|string
 */
function eaccounting_get_price_decimal_separator() {
	return eaccounting_get_option( 'decimal_separator', 'eaccounting_localisation', '.' );
}

/**
 * Thousand sep
 * since 1.0.0
 * @return array|string
 */
function eaccounting_get_price_thousands_separator() {
	return eaccounting_get_option( 'thousand_separator', 'eaccounting_localisation', ',' );
}

/**
 * Get the price format depending on the currency position.
 *
 * @return string
 */
function eaccounting_get_price_format() {
	$currency_pos = eaccounting_get_option( 'currency_pos', 'eaccounting_localisation', 'right' );

	$format = '%1$s%2$s';

	switch ( $currency_pos ) {
		case 'left':
			$format = '%1$s%2$s';
			break;
		case 'right':
			$format = '%2$s%1$s';
			break;
		case 'left_space':
			$format = '%1$s&nbsp;%2$s';
			break;
		case 'right_space':
			$format = '%2$s&nbsp;%1$s';
			break;
	}

	return apply_filters( 'eaccounting_price_format', $format, $currency_pos );
}


/**
 * Returns a sanitized price by stripping out thousands separators.
 *
 * since 1.0.0
 *
 * @param $price
 *
 * @return string
 */
function eaccounting_sanitize_price( $price ) {
	$is_negative   = false;
	$thousands_sep = eaccounting_get_price_thousands_separator();
	$decimal_sep   = eaccounting_get_price_decimal_separator();

	// Sanitize the price
	if ( $decimal_sep == ',' && false !== ( $found = strpos( $price, $decimal_sep ) ) ) {
		if ( ( $thousands_sep == '.' || $thousands_sep == ' ' ) && false !== ( $found = strpos( $price, $thousands_sep ) ) ) {
			$price = str_replace( $thousands_sep, '', $price );
		} elseif ( empty( $thousands_sep ) && false !== ( $found = strpos( $price, '.' ) ) ) {
			$price = str_replace( '.', '', $price );
		}

		$price = str_replace( $decimal_sep, '.', $price );
	} elseif ( $thousands_sep == ',' && false !== ( $found = strpos( $price, $thousands_sep ) ) ) {
		$price = str_replace( $thousands_sep, '', $price );
	}

	if ( $price < 0 ) {
		$is_negative = true;
	}

	$price = preg_replace( '/[^0-9\.]/', '', $price );

	$precision = eaccounting_get_price_precision();
	$price     = number_format( (double) $price, $precision, '.', '' );

	if ( $is_negative ) {
		$price *= - 1;
	}

	/**
	 * Filter the sanitized price before returning
	 *
	 * @param string $price Price
	 *
	 * @since unknown
	 *
	 */
	return apply_filters( 'eaccounting_sanitize_price', $price );
}


/**
 * Returns a nicely formatted price.
 *
 * since 1.0.0
 *
 * @param      $price
 * @param bool $decimals
 *
 * @return string
 */
function eaccounting_format_price( $price, $decimals = true ) {
	$price         = eaccounting_sanitize_price( $price );
	$thousands_sep = eaccounting_get_price_thousands_separator();
	$decimal_sep   = eaccounting_get_price_decimal_separator();

	// Format the price
	if ( $decimal_sep == ',' && false !== ( $sep_found = strpos( $price, $decimal_sep ) ) ) {
		$whole = substr( $price, 0, $sep_found );
		$part  = substr( $price, $sep_found + 1, ( strlen( $price ) - 1 ) );
		$price = $whole . '.' . $part;
	}

	// Strip , from the price (if set as the thousands separator)
	if ( $thousands_sep == ',' && false !== ( $found = strpos( $price, $thousands_sep ) ) ) {
		$price = str_replace( ',', '', $price );
	}

	// Strip ' ' from the price (if set as the thousands separator)
	if ( $thousands_sep == ' ' && false !== ( $found = strpos( $price, $thousands_sep ) ) ) {
		$price = str_replace( ' ', '', $price );
	}

	if ( empty( $price ) ) {
		$price = 0;
	}
	$precision = eaccounting_get_price_precision();
	$formatted = number_format( $price, $precision, $decimal_sep, $thousands_sep );

	return apply_filters( 'eaccounting_format_price', $formatted, $price, $decimals, $decimal_sep, $thousands_sep );
}

/**
 * Get nicely formatted price with currency
 * since 1.0.0
 *
 * @param $price
 * @param bool $currency
 *
 * @return string
 */
function eaccounting_price( $price, $currency = false ) {
	if ( ! $currency ) {
		$currency = eaccounting_get_price_currency();
	}

	$format          = eaccounting_get_price_format();
	$currency_symbol = eaccounting_get_price_currency_symbol();

	return sprintf( $format, eaccounting_format_price( $price ), $currency_symbol );
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
