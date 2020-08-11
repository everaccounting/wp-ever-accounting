<?php
/**
 * EverAccounting category Functions
 *
 * All category related function of the plugin.
 *
 * @package EverAccounting
 * @version 1.0.2
 */

defined( 'ABSPATH' ) || exit();
/**
 * Get all the available type of category the plugin support.
 *
 * @return array
 * @since 1.0.2
 */
function eaccounting_get_category_types() {
	$types = array(
		'expense' => __( 'Expense', 'wp-ever-accounting' ),
		'income'  => __( 'Income', 'wp-ever-accounting' ),
		'other'   => __( 'Other', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_category_types', $types );
}

/**
 * Get category.
 *
 * @param $category
 *
 * @return bool|\EAccounting\Category
 * @since 1.0.2
 *
 */
function eaccounting_get_category( $category ) {
	if ( empty( $category ) ) {
		return false;
	}
	try {
		if ( $category instanceof \EAccounting\Category ) {
			return $category;
		}

		$category = new \EAccounting\Category( $category );
		if ( ! $category->exists() ) {
			throw new Exception( __( 'Invalid category.', 'wp-ever-accounting' ) );
		}

		return $category;
	} catch ( Exception $exception ) {
		return false;
	}
}
/**
 * Insert a category.
 *
 * @param $args
 *
 * @return WP_Error|Mixed
 * @since 1.0.2
 *
 */
function eaccounting_insert_category( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$category     = new \EAccounting\Category( $args['id'] );
		$category->set_props( $args );
		$category->save();

	} catch ( Exception $e ) {
		return new WP_Error( 'error', $e->getMessage() );
	}

	return $category;
}

/**
 * Delete a category.
 *
 * @param $category_id
 *
 * @return WP_Error|Mixed|bool
 * @since 1.0.2
 *
 */
function eaccounting_delete_category( $category_id ) {
	try {
		$category = new \EAccounting\Category( $category_id );
		if ( ! $category->exists() ) {
			throw new Exception( __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		$category->delete();

		return empty( $category->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
