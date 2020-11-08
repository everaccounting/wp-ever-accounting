<?php
/**
 * EverAccounting category Functions.
 *
 * All category related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

namespace EverAccounting\Categories;

use EverAccounting\Exception;

defined( 'ABSPATH' ) || exit();

require_once dirname( __FILE__ ) . '/hooks.php';
require_once dirname( __FILE__ ) . '/deprecated.php';

/**
 * Get all the available type of category the plugin support.
 *
 * @since 1.1.0
 * @return array
 */
function get_types() {
	$types = array(
		'expense' => __( 'Expense', 'wp-ever-accounting' ),
		'income'  => __( 'Income', 'wp-ever-accounting' ),
		'other'   => __( 'Other', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_category_types', $types );
}

/**
 * Main function querying categories.
 *
 * @param array $args
 * @since 1.1.0
 *
 * @return \EverAccounting\Categories\Query
 */
function query( $args = array() ) {
	return new Query( $args );
}

/**
 * Get category.
 *
 * @since 1.1.0
 *
 * @param $category
 *
 * @return null|Category
 */
function get( $category ) {
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
 * @since 1.1.0
 *
 * @param       $args  {
 *
 * @type string $name  Unique name of the category.
 * @type string $type  Category type.
 * @type string $color Color of the category
 * }
 *
 * @return \WP_Error|Mixed
 */
function insert( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$category     = new Category( $args['id'] );
		$category->set_props( $args );

		if ( null === $category->get_date_created() ) {
			$category->set_date_created( time() );
		}
		if ( ! $category->get_creator_id() ) {
			$category->set_creator_id();
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
		$existing_id = query()
			->where( 'type', $category->get_type() )
			->where( 'name', $category->get_name() )
			->get_var( 0 );
		if ( $existing_id ) {
			if ( ! empty( $existing_id ) && $existing_id != $category->get_id() ) {
				throw new  Exception( 'duplicate_entry', __( 'Duplicate category name.', 'wp-ever-accounting' ) );
			}
		}

		$category->save();

	} catch ( Exception $e ) {
		return new \WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $category;
}

/**
 * Delete a category.
 *
 * @since 1.1.0
 *
 * @param $category_id
 *
 * @return bool
 */
function delete( $category_id ) {
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

