<?php
/**
 * EverAccounting Tax functions.
 *
 * Functions related to taxes.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Models\Tax;

defined( 'ABSPATH' ) || exit;

/**
 * Is tax enabled.
 *
 * @since 1.1.0
 * @return bool
 */
function eac_tax_enabled() {
	return apply_filters( 'ever_accounting_tax_enabled', get_option( 'eac_tax_enabled', 'no' ) === 'yes' );
}

/**
 * Get calculated tax.
 *
 * @param double $amount Amount to calculate tax for.
 * @param array  $rates Taxes.
 * @param bool   $inclusive Whether the amount is inclusive of tax.
 *
 * @since 1.1.0
 *
 * @return array Array of tax amounts.
 */
function eac_calculate_taxes( $amount, $rates, $inclusive = false ) {
	$default_data = array(
		'tax_id'      => 0,
		'rate'        => 0,
		'compound' => 'no',
	);
	foreach ( $rates as $key => $rate ) {
		if ( is_a( $rate, '\EverAccounting\Models\DocumentTax' ) ) {
			$rate = $rate->to_array();
		} elseif ( is_object( $rate ) ) {
			$rate = get_object_vars( $rate );
		}
		// If rate id is not set, use then continue.
		if ( empty( $rate['tax_id'] ) ) {
			unset( $rates[ $key ] );
			continue;
		}
		$rates[ $key ]           = wp_parse_args( $rate, $default_data );
		$rates[ $key ]['amount'] = 0;
	}

	if ( $inclusive ) {
		$non_compounded = $amount;
		foreach ( $rates as &$rate ) {
			if ( 'yes' !== $rate['compound'] ) {
				continue;
			}
			$tax_amount      = $non_compounded - ( $non_compounded / ( 1 + ( $rate['rate'] / 100 ) ) );
			$rate['amount']  = $tax_amount;
			$non_compounded -= $tax_amount;
		}

		$regular_tax_rate = 1 + ( array_sum( array_column( wp_list_filter( $rates, array( 'is_compound' => 'no' ) ), 'rate' ) ) / 100 );
		foreach ( $rates as &$rate ) {
			if ( 'yes' === $rate['is_compound'] ) {
				continue;
			}
			$the_rate        = $rate['rate'] / 100 / $regular_tax_rate;
			$net_price       = $amount - ( $the_rate * $non_compounded );
			$rate['amount'] += $amount - $net_price;
		}
	} else {
		foreach ( $rates as &$rate ) {
			if ( 'yes' === $rate['is_compound'] ) {
				continue;
			}
			$rate['amount'] = $amount * ( $rate['rate'] / 100 );
		}
		$pre_compounded = array_sum( wp_list_pluck( $rates, 'amount' ) );
		foreach ( $rates as &$rate ) {
			if ( 'yes' !== $rate['is_compound'] ) {
				continue;
			}
			$tax_amount      = ( $amount + $pre_compounded ) * ( $rate['rate'] / 100 );
			$rate['amount'] += $tax_amount;
			$pre_compounded += $tax_amount;
		}
	}

	return wp_list_pluck( $rates, 'amount', 'tax_id' );
}

/**
 * Get tax rate.
 *
 * @param mixed $data Tax rate ID.
 *
 * @since 1.1.0
 *
 * @since 1.1.0
 * @return Tax|null
 */
function eac_get_tax( $data ) {
	return Tax::find( $data );
}

/**
 * Insert tax rate.
 *
 * @param array $args Tax rate arguments.
 * @param bool  $wp_error Return WP_Error on failure.
 *
 * @since 1.1.0
 *
 * @return Tax|false|int|WP_Error
 */
function eac_insert_tax( $args, $wp_error = true ) {
	return Tax::insert( $args, $wp_error );
}

/**
 * Delete a tax rate.
 *
 * @param int $rate_id Tax rate ID.
 *
 * @since 1.1.0
 * @return bool
 */
function eac_delete_tax( $rate_id ) {
	$rate = eac_get_tax( $rate_id );

	if ( ! $rate ) {
		return false;
	}

	return $rate->delete();
}

/**
 * Get tax rates.
 *
 * @param array $args Query arguments.
 * @param bool  $count Optional. Whether to return count or not. Default false.
 *
 * @since 1.1.0
 *
 * @return array|int|Tax[]
 */
function eac_get_taxes( $args = array(), $count = false ) {
	if ( $count ) {
		return Tax::count( $args );
	}

	return Tax::results( $args );
}
