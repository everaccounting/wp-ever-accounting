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
	return apply_filters( 'ever_accounting_tax_enabled', get_option( 'eac_enabled_tax', 'no' ) === 'yes' );
}

/**
 * Are prices inclusive of tax?
 *
 * @return bool
 */
function eac_price_includes_tax() {
	return eac_tax_enabled() && apply_filters( 'ever_accounting_price_includes_tax', get_option( 'eac_prices_include_tax' ) === 'yes' );
}

/**
 * Get calculated tax.
 *
 * @param double $amount Amount to calculate tax for.
 * @param array  $taxes Taxes.
 * @param bool   $inclusive Whether the amount is inclusive of tax.
 *
 * @since 1.1.0
 *
 * @return array Array of tax amounts.
 */
function eac_calculate_taxes( $amount, $taxes, $inclusive = false ) {
	$default_data = array(
		'tax_id'      => 0,
		'rate'        => 0,
		'is_compound' => 'no',
	);
	foreach ( $taxes as $key => $tax ) {
		if ( is_a( $tax, '\EverAccounting\Models\DocumentTax' ) ) {
			$tax = $tax->get_data();
		} elseif ( is_object( $tax ) ) {
			$tax = get_object_vars( $tax );
		}
		// If rate id is not set, use then continue.
		if ( empty( $tax['tax_id'] ) ) {
			unset( $taxes[ $key ] );
			continue;
		}
		$taxes[ $key ]           = wp_parse_args( $tax, $default_data );
		$taxes[ $key ]['amount'] = 0;
	}

	if ( $inclusive ) {
		$non_compounded = $amount;
		foreach ( $taxes as &$tax ) {
			if ( 'yes' !== $tax['is_compound'] ) {
				continue;
			}
			$tax_amount      = $non_compounded - ( $non_compounded / ( 1 + ( $tax['rate'] / 100 ) ) );
			$tax['amount']   = $tax_amount;
			$non_compounded -= $tax_amount;
		}

		$regular_tax_rate = 1 + ( array_sum( array_column( wp_list_filter( $taxes, array( 'is_compound' => 'no' ) ), 'rate' ) ) / 100 );
		foreach ( $taxes as &$tax ) {
			if ( 'yes' === $tax['is_compound'] ) {
				continue;
			}
			$the_rate       = $tax['rate'] / 100 / $regular_tax_rate;
			$net_price      = $amount - ( $the_rate * $non_compounded );
			$tax['amount'] += $amount - $net_price;
		}
	} else {
		foreach ( $taxes as &$tax ) {
			if ( 'yes' === $tax['is_compound'] ) {
				continue;
			}
			$tax['amount'] = $amount * ( $tax['rate'] / 100 );
		}
		$pre_compounded = array_sum( wp_list_pluck( $taxes, 'amount' ) );
		foreach ( $taxes as &$tax ) {
			if ( 'yes' !== $tax['is_compound'] ) {
				continue;
			}
			$tax_amount      = ( $amount + $pre_compounded ) * ( $tax['rate'] / 100 );
			$tax['amount']  += $tax_amount;
			$pre_compounded += $tax_amount;
		}
	}

	return wp_list_pluck( $taxes, 'amount', 'tax_id' );
}

/**
 * Get calculated tax.
 *
 * @param double $amount Amount to calculate tax for.
 * @param array  $taxes Taxes.
 * @param bool   $inclusive Whether the amount is inclusive of tax.
 * @param bool   $flat Whether to return a flat tax amount or an array of tax amounts.
 *
 * @since 1.1.0
 *
 * @return array|float Tax amount or array of tax amounts.
 */
function eac_calculate_taxes_v1( $amount, $taxes, $inclusive = false, $flat = false ) {
	$default_tax = array(
		'tax_id'      => 0,
		'rate'        => 0,
		'is_compound' => 'no',
		'amount'      => 0,
	);

	if ( ! is_array( $taxes ) ) {
		$taxes = wp_parse_id_list( $taxes );
	}

	foreach ( $taxes as $key => $tax ) {
		// if $tax is a Tax object, convert it to an array.
		if ( is_a( $tax, '\EverAccounting\Models\DocumentTax' ) ) {
			$tax = $tax->get_data();
		} elseif ( is_numeric( $tax ) ) {
			$tax = array(
				'tax_id'      => $key,
				'rate'        => $tax,
				'is_compound' => 'no',
				'amount'      => 0,
			);
		}
		$taxes[ $key ] = wp_parse_args( $tax, $default_tax );
	}

	if ( $inclusive ) {
		$non_compounded = $amount;
		foreach ( $taxes as &$tax ) {
			if ( 'yes' !== $tax['is_compound'] ) {
				continue;
			}
			$tax_amount      = $non_compounded - ( $non_compounded / ( 1 + ( $tax['rate'] / 100 ) ) );
			$tax['amount']   = $tax_amount;
			$non_compounded -= $tax_amount;
		}

		$regular_tax_rate = 1 + ( array_sum( array_column( wp_list_filter( $taxes, array( 'is_compound' => 'no' ) ), 'rate' ) ) / 100 );
		foreach ( $taxes as &$tax ) {
			if ( 'yes' === $tax['is_compound'] ) {
				continue;
			}
			$the_rate       = $tax['rate'] / 100 / $regular_tax_rate;
			$net_price      = $amount - ( $the_rate * $non_compounded );
			$tax['amount'] += $amount - $net_price;
		}
	} else {
		foreach ( $taxes as &$tax ) {
			if ( 'yes' === $tax['is_compound'] ) {
				continue;
			}
			$tax['amount'] = $amount * ( $tax['rate'] / 100 );
		}
		$pre_compounded = array_sum( wp_list_pluck( $taxes, 'amount' ) );
		foreach ( $taxes as &$tax ) {
			if ( 'yes' !== $tax['is_compound'] ) {
				continue;
			}
			$tax_amount      = ( $amount + $pre_compounded ) * ( $tax['rate'] / 100 );
			$tax['amount']  += $tax_amount;
			$pre_compounded += $tax_amount;
		}
	}

	return $flat ? wp_list_pluck( $taxes, 'amount', 'tax_id' ) : $taxes;
}

/**
 * Get tax rate types.
 *
 * @since 1.1.0
 *
 * @return array
 */
function eac_get_tax_types() {
	return apply_filters(
		'ever_accounting_tax_types',
		array(
			'simple'   => __( 'Simple', 'wp-ever-accounting' ),
			'compound' => __( 'Compound', 'wp-ever-accounting' ),
		)
	);
}

/**
 * Get tax rate.
 *
 * @param int    $rate_id Tax rate ID.
 * @param string $column Optional. Column to get. Default null.
 * @param array  $args Optional. Additional arguments. Default empty array.
 *
 * @since 1.1.0
 *
 * @since 1.1.0
 * @return Tax|null
 */
function eac_get_tax( $rate_id, $column = null, $args = array() ) {
	return Tax::get( $rate_id, $column, $args );
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
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Tax::count( $args );
	}

	return Tax::query( $args );
}
