<?php

use EverAccounting\Models\Term;

defined( 'ABSPATH' ) || exit;

/**
 * Get term groups
 *
 * @since   1.0.0
 * @return array
 */
function eac_get_term_groups() {
	$groups = array(
		'product_cat' => __( 'Product Categories', 'wp-ever-accounting' ),
		'product_tag' => __( 'Product Tags', 'wp-ever-accounting' ),
		'income_cat'  => __( 'Income Categories', 'wp-ever-accounting' ),
		'expense_cat' => __( 'Expense Categories', 'wp-ever-accounting' ),
		'tax_rate'    => __( 'Tax Rates', 'wp-ever-accounting' ),
	);

	return apply_filters( 'ever_accounting_term_groups', $groups );
}

/**
 * Get term.
 *
 * @param mixed  $term Term ID or object.
 * @param string $group Term group.
 *
 * @since 1.1.0
 * @return null|EverAccounting\Models\Term
 */
function eac_get_term( $term, $group = '' ) {
	if ( ! empty( $group ) && ! array_key_exists( $group, eac_get_term_groups() ) ) {
		return null;
	}
	$term = Term::get( $term );
	if ( ! empty( $group ) && ! $term->is_group( $group ) ) {
		return null;
	}

	return $term;
}

/**
 * Insert a term.
 *
 * @param array $data Term data.
 * @param bool  $wp_error Whether to return false or WP_Error on failure.
 *
 * @since 1.1.0
 * @return int|\WP_Error|Term|bool The value 0 or WP_Error on failure. The Term object on success.
 */
function eac_insert_term( $data = array(), $wp_error = true ) {
	return Term::insert( $data, $wp_error );
}

/**
 * Delete a term.
 *
 * @param int    $term_id Term ID.
 * @param string $group Term group.
 *
 * @since 1.1.0
 * @return bool
 */
function eac_delete_term( $term_id, $group = '' ) {
	$term = eac_get_term( $term_id );
	if ( ! $term || ( ! empty( $group ) && ! $term->is_group( $group ) ) ) {
		return false;
	}

	return $term->delete();
}

/**
 * Get term items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return the count of items.
 *
 * @since 1.1.0
 * @return int|array|Term[]
 */
function eac_get_terms( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Term::count( $args );
	}

	return Term::query( $args );
}
