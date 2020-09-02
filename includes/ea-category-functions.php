<?php
/**
 * EverAccounting category Functions.
 *
 * All category related function of the plugin.
 *
 * @since 1.0.2
 * @package EverAccounting
 */

use EverAccounting\Category;
use EverAccounting\Query_Category;
use EverAccounting\Exception;

defined( 'ABSPATH' ) || exit();
/**
 * Get all the available type of category the plugin support.
 *
 * @since 1.0.2
 * @return array
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
 * @since 1.0.2
 *
 * @return null|Category
 */
function eaccounting_get_category( $category ) {
	if ( empty( $category ) ) {
		return null;
	}
	try {
		if ( $category instanceof Category ) {
			$_category = $category;
		} elseif ( is_object( $category ) && ! empty( $category->id ) ) {
			$_category = new Category( null );
			$_category->populate( $category );
		} else {
			$_category = new Category( absint( $category ) );
		}

		if ( ! $_category->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid category.', 'wp-ever-accounting' ) );
		}

		return $_category;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 * Insert a category.
 *
 * @param       $args {
 *
 * @type string $name Unique name of the category.
 * @type string $type Category type.
 * @type string $color Color of the category
 * }
 *
 * @since 1.0.2
 *
 * @return WP_Error|Mixed
 */
function eaccounting_insert_category( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$category     = new Category( $args['id'] );
		$category->set_props( $args );

		if ( null == $category->get_date_created() ) {
			$category->set_date_created( time() );
		}
		if ( ! $category->get_creator_id() ) {
			$category->set_creator_id();
		}
		if ( ! $category->get_company_id() ) {
			$category->set_company_id();
		}
		if ( ! $category->get_color( 'edit' ) ) {
			$category->set_color( eaccounting_get_random_color() );
		}
		if ( empty( $category->get_name( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Category name is required', 'wp-ever-accounting' ) );
		}
		if ( empty( $category->get_type( 'edit' ) ) ) {
			throw new  Exception( 'empty_props', __( 'Category type is required', 'wp-ever-accounting' ) );
		}
		$existing_id = Query_Category::init()
		                             ->where( 'type', $category->get_type() )
		                             ->where( 'name', $category->get_name() )
		                             ->value( 0 );
		if ( $existing_id ) {
			if ( ! empty( $existing_id ) && $existing_id != $category->get_id() ) {
				throw new  Exception( 'duplicate_entry', __( 'Duplicate category name.', 'wp-ever-accounting' ) );
			}
		}

		$category->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $category;
}

/**
 * Delete a category.
 *
 * @param $category_id
 *
 * @since 1.0.2
 *
 * @return bool
 */
function eaccounting_delete_category( $category_id ) {
	try {
		$category = new Category( $category_id );
		if ( ! $category->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid category.', 'wp-ever-accounting' ) );
		}

		$category->delete();

		return empty( $category->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}

/**
 * Delete category id from transactions.
 *
 * @since 1.0.2
 *
 * @param $id
 *
 * @return bool
 */
function eaccounting_update_transaction_category( $id ) {
	$id = absint( $id );
	if ( empty( $id ) ) {
		return false;
	}
	$transactions = \EverAccounting\Query::init();

	return $transactions->table( 'ea_transactions' )->where( 'category_id', absint( $id ) )->update( array( 'category_id' => '' ) );
}

add_action( 'eaccounting_delete_category', 'eaccounting_update_transaction_category' );
