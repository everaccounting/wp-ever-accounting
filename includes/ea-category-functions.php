<?php
/**
 * EverAccounting category Functions.
 *
 * All category related function of the plugin.
 *
 * @package EverAccounting
 * @since 1.0.2
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
 * @return bool|\EverAccounting\Category
 * @since 1.0.2
 *
 */
function eaccounting_get_category( $category ) {
	try {
		if ( $category instanceof \EverAccounting\Category ) {
			$_category = $category;
		} elseif ( is_object( $category ) && ! empty( $category->id ) ) {
			$_category = new \EverAccounting\Category( null );
			$_category->populate( $category );
		} else {
			$_category = new \EverAccounting\Category( absint( $category ) );
		}

		if ( ! $_category->exists() ) {
			throw new Exception( __( 'Invalid category.', 'wp-ever-accounting' ) );
		}

		return $_category;
	} catch ( Exception $exception ) {
		return null;
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
		$category     = new \EverAccounting\Category( $args['id'] );
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
		$category = new \EverAccounting\Category( $category_id );
		if ( ! $category->exists() ) {
			throw new Exception( __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		$category->delete();

		return empty( $category->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
