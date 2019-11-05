<?php
/**
 * Decimal sep
 * since 1.0.0
 * @return array|string
 */
function eaccounting_get_amount_currency() {
	return eaccounting_get_option( 'currency', 'eaccounting_localization', 'USD' );
}

/**
 * Decimal sep
 * since 1.0.0
 * @return array|string
 */
function eaccounting_get_amount_decimal_separator() {
	return eaccounting_get_option( 'decimal_separator', 'eaccounting_localization', '.' );
}

/**
 * Thousand sep
 * since 1.0.0
 * @return array|string
 */
function eaccounting_get_amount_thousands_separator() {
	return eaccounting_get_option( 'thousands_separator', 'eaccounting_localization', ',' );
}


/**
 * Return the number of decimals after the decimal point.
 *
 * @return int
 * @since  1.0
 */
function eaccounting_get_amount_decimals() {
	return absint( eaccounting_get_option( 'thousands_separator', 'eaccounting_localization', 2 ) );
}

/**
 * Get the price format depending on the currency position.
 *
 * @return string
 */
function eaccounting_get_amount_format() {
	$currency_pos = eaccounting_get_option( 'currency_pos', 'eaccounting_localization', 'right' );

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

	return apply_filters( 'eaccounting_amount_format', $format, $currency_pos );
}

/**
 * Returns a sanitized amount by stripping out thousands separators.
 *
 * since 1.0.0
 *
 * @param $amount
 *
 * @return string
 */
function eaccounting_sanitize_amount( $amount ) {
	$is_negative   = false;
	$thousands_sep = eaccounting_get_amount_thousands_separator();
	$decimal_sep   = eaccounting_get_amount_decimal_separator();

	// Sanitize the amount
	if ( $decimal_sep == ',' && false !== ( $found = strpos( $amount, $decimal_sep ) ) ) {
		if ( ( $thousands_sep == '.' || $thousands_sep == ' ' ) && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
			$amount = str_replace( $thousands_sep, '', $amount );
		} elseif ( empty( $thousands_sep ) && false !== ( $found = strpos( $amount, '.' ) ) ) {
			$amount = str_replace( '.', '', $amount );
		}

		$amount = str_replace( $decimal_sep, '.', $amount );
	} elseif ( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( $thousands_sep, '', $amount );
	}

	if ( $amount < 0 ) {
		$is_negative = true;
	}

	$amount = preg_replace( '/[^0-9\.]/', '', $amount );

	$decimals = eaccounting_get_amount_decimals();
	$amount   = number_format( (double) $amount, $decimals, '.', '' );

	if ( $is_negative ) {
		$amount *= - 1;
	}

	/**
	 * Filter the sanitized price before returning
	 *
	 * @param string $amount Price
	 *
	 * @since unknown
	 *
	 */
	return apply_filters( 'eaccounting_sanitize_amount', $amount );
}


/**
 * Returns a nicely formatted amount.
 *
 * since 1.0.0
 *
 * @param      $amount
 * @param bool $decimals
 *
 * @return string
 */
function eaccounting_format_amount( $amount, $decimals = true ) {
	$thousands_sep = eaccounting_get_amount_thousands_separator();
	$decimal_sep   = eaccounting_get_amount_decimal_separator();

	// Format the amount
	if ( $decimal_sep == ',' && false !== ( $sep_found = strpos( $amount, $decimal_sep ) ) ) {
		$whole  = substr( $amount, 0, $sep_found );
		$part   = substr( $amount, $sep_found + 1, ( strlen( $amount ) - 1 ) );
		$amount = $whole . '.' . $part;
	}

	// Strip , from the amount (if set as the thousands separator)
	if ( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( ',', '', $amount );
	}

	// Strip ' ' from the amount (if set as the thousands separator)
	if ( $thousands_sep == ' ' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( ' ', '', $amount );
	}

	if ( empty( $amount ) ) {
		$amount = 0;
	}

	$decimals  = eaccounting_get_amount_decimals();
	$formatted = number_format( $amount, $decimals, $decimal_sep, $thousands_sep );

	return apply_filters( 'eaccounting_format_amount', $formatted, $amount, $decimals, $decimal_sep, $thousands_sep );
}


function eaccounting_amount( $amount, $currency = false ) {
	if(!$currency){
		$currency = eaccounting_get_amount_currency();
	}
	$format = eaccounting_get_amount_format();
	$currency_symbol = eaccounting_get_currency_symbol($currency);

	return sprintf($format, eaccounting_format_amount($amount), $currency_symbol);
}


function eaccounting_get_currency_symbol( $currency = '' ) {
	switch ( $currency ) :
		case "GBP" :
			$symbol = '&pound;';
			break;
		case "BRL" :
			$symbol = 'R&#36;';
			break;
		case "EUR" :
			$symbol = '&euro;';
			break;
		case "USD" :
		case "AUD" :
		case "NZD" :
		case "CAD" :
		case "HKD" :
		case "MXN" :
		case "SGD" :
			$symbol = '&#36;';
			break;
		case "JPY" :
			$symbol = '&yen;';
			break;
		case "BDT" :
			$symbol = '&#2547;';
			break;
		case "AOA" :
			$symbol = 'Kz';
			break;
		default :
			$symbol = $currency;
			break;
	endswitch;

	return apply_filters( 'eaccounting_currency_symbol', $symbol, $currency );
}
