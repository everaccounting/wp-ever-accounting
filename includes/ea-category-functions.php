<?php
/**
 * EverAccounting category Functions
 *
 * All category related function of the plugin.
 *
 * @package EverAccounting\Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit();
/**
 * Get all the available type of category the plugin
 * support.
 *
 * @return array
 * @since 1.0.0
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
 * @since 1.0.0
 *
 * @return bool|EAccounting_Category
 */
function eaccounting_get_category( $category ) {
	if ( empty( $category ) ) {
		return false;
	}
	try {
		if ( $category instanceof EAccounting_Category ) {
			return $category;
		}

		$category = new EAccounting_Category( $category );
		if ( ! $category->exists() ) {
			throw new Exception( __( 'Invalid category.', 'wp-ever-accounting' ) );
		}

		return $category;
	} catch ( Exception $exception ) {
		return false;
	}
}

function eaccounting_insert_category( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$category      = new EAccounting_Category( $args['id'] );
		$category->set_props( $args );
		$category->save();

	} catch ( Exception $e ) {
		return new WP_Error( 'error', $e->getMessage() );
	}

	return $category;
}

function eaccounting_delete_category( $category_id ) {
	try {
		$category = new EAccounting_Category( $category_id );
		if ( ! $category->exists() ) {
			throw new Exception( __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		$category->delete();

		return empty( $category->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
