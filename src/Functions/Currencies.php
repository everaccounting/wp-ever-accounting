<?php

defined( 'ABSPATH' ) || exit;

/**
 * Get currency symbol
 *
 * @param string $code Currency code.
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_currency_symbol( $code = '' ) {
	$currencies = eac_get_currencies();
	if ( empty( $code ) ) {
		$code = eac_get_base_currency();
	}

	if ( empty( $currencies[ $code ] ) || empty( $currencies[ $code ]['symbol'] ) ) {
		return $code;
	}

	return $currencies[ $code ]['symbol'];
}

/**
 * Get currency position.
 *
 * @param string $code Currency code.
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_currency_position( $code = '' ) {
	if ( empty( $code ) ) {
		$code = eac_get_base_currency();
	}

	$currencies = eac_get_currencies();
	if ( empty( $currencies[ $code ] ) || empty( $currencies[ $code ]['position'] ) ) {
		return 'left';
	}

	return $currencies[ $code ]['position'];
}

/**
 * Get currency decimal separator.
 *
 * @param string $code Currency code.
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_currency_decimal_separator( $code = '' ) {
	if ( empty( $code ) ) {
		$code = eac_get_base_currency();
	}

	$currencies = eac_get_currencies();
	if ( empty( $currencies[ $code ] ) || empty( $currencies[ $code ]['decimal_sep'] ) ) {
		return '.';
	}

	return $currencies[ $code ]['decimal_sep'];
}

/**
 * Get currency a thousand separator.
 *
 * @param string $code Currency code.
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_currency_thousand_separator( $code = '' ) {
	if ( empty( $code ) ) {
		$code = eac_get_base_currency();
	}

	$currencies = eac_get_currencies();
	if ( empty( $currencies[ $code ] ) || empty( $currencies[ $code ]['thousand_sep'] ) ) {
		return ',';
	}

	return $currencies[ $code ]['thousand_sep'];
}

/**
 * Get currency decimal places.
 *
 * @param string $code Currency code.
 *
 * @since 1.0.2
 *
 * @return int
 */
function eac_get_currency_precision( $code = '' ) {
	if ( empty( $code ) ) {
		$code = eac_get_base_currency();
	}

	$currencies = eac_get_currencies();
	if ( empty( $currencies[ $code ] ) || empty( $currencies[ $code ]['precision'] ) ) {
		return 2;
	}

	return $currencies[ $code ]['precision'];
}

/**
 * Get currency rate.
 *
 * @param string $code Currency code.
 *
 * @since 1.0.2
 *
 * @return string
 */
function eac_get_currency_rate( $code = '' ) {
	if ( empty( $code ) ) {
		$code = eac_get_base_currency();
	}

	$currencies = eac_get_currencies();
	if ( empty( $currencies[ $code ] ) || empty( $currencies[ $code ]['rate'] ) ) {
		return 1;
	}

	return $currencies[ $code ]['rate'];
}

/**
 * Update currency.
 *
 * @param array $data Currency data.
 *
 * @since 1.0.0
 * @return bool
 */
function eac_update_currency( $data ) {
	$currencies = eac_get_currencies();
	if ( empty( $data['code'] ) || empty( $currencies[ $data['code'] ] ) ) {
		return false;
	}
	$code     = sanitize_text_field( strtoupper( $data['code'] ) );
	$currency = wp_parse_args( $data, $currencies[ $code ] );
	$currency = wp_array_slice_assoc( $currency, array_keys( $currencies[ $code ] ) );
	$is_base  = ! empty( $data['base'] ) && 'yes' === $data['base'];

	// When a currency is set as base, make sure all other currencies rate is updated.
	if ( $is_base ) {
		$new_rate = $currencies[ $code ]['rate'];
		foreach ( $currencies as $currency_code => &$currency_data ) {
			if ( $currency_code !== $code ) {
				$currencies[ $currency_code ]['rate'] = $currency_data['rate'] / $new_rate;
				if ( isset( $currency_data['base'] ) ) {
					unset( $currencies['base'] );
				}
			}
		}

		$currency['rate']   = 1;
		$currency['base']   = 'yes';
		$currency['status'] = 'active';
	}
	// status should either be active or inactive.
	$currency['status']    = in_array( $currency['status'], array( 'active', 'inactive' ), true ) ? $currency['status'] : 'inactive';
	$currency['precision'] = max( 0, min( 4, $currency['precision'] ) );
	$currency['rate']      = max( 0, $currency['rate'] );
	$currencies[ $code ]   = $currency;

	return update_option( 'eac_currencies', $currencies );
}

/**
 * Get currencies.
 *
 * @param string $status Currency status.
 *
 * @since 1.0.0
 * @return array
 */
function eac_get_currencies( $status = null ) {
	$currencies = get_option( 'eac_currencies', array() );
	if ( in_array( $status, array( 'active', 'inactive' ), true ) ) {
		$currencies = array_filter(
			$currencies,
			function ( $currency ) use ( $status ) {
				return $currency['status'] === $status;
			}
		);
	}

	// set a formatted name for each currency.
	foreach ( $currencies as $code => $currency ) {
		// set rate upto 4 decimal places.
		$currencies[ $code ]['rate']           = round( $currency['rate'], 4 );
		$currencies[ $code ]['formatted_name'] = sprintf( '%s (%s)', $currency['name'], $currency['symbol'] );
	}

	return apply_filters( 'ever_accounting_currencies', $currencies );
}
