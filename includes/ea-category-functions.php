<?php
/**
 * EverAccounting category Functions.
 *
 * All category related function of the plugin.
 *
 * @since   1.0.2
 * @package EverAccounting
 */

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
 * @since 1.0.2
 *
 * @param $category
 *
 * @return null|EverAccounting\Models\Category
 */
function eaccounting_get_category( $category ) {
	if ( empty( $category ) ) {
		return null;
	}
	$result = new EverAccounting\Models\Category( $category );

	return $result->exists() ? $result : null;
}

/**
 * Insert a category.
 *
 * @since 1.0.2
 *
 * @param       $args  {
 *
 * @type string $name  Unique name of the category.
 * @type string $type  Category type.
 * @type string $color Color of the category
 * }
 *
 * @return WP_Error|\EverAccounting\Models\Category
 */
function eaccounting_insert_category( $args ) {
	$category = new EverAccounting\Models\Category( $args );

	return $category->save();
}

/**
 * Delete a category.
 *
 * @since 1.0.2
 *
 * @param $category_id
 *
 * @return bool
 */
function eaccounting_delete_category( $category_id ) {
	$category = new EverAccounting\Models\Category( $category_id );
	if ( ! $category->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Categories::instance()->delete( $category->get_id() );
}

/**
 * Get category items.
 *
 * @since 1.1.0
 *
 * @param array $args
 *
 * @param bool  $callback
 *
 * @return array|int
 */
function eaccounting_get_categories( $args = array(), $callback = true ) {
	return \EverAccounting\Repositories\Categories::instance()->get_items(
		$args,
		function ( $item ) use ( $callback ) {
			if ( $callback ) {
				$category = new \EverAccounting\Models\Category();
				$category->set_props( $item );
				$category->set_object_read( true );

				return $category;
			}

			return $item;
		}
	);
}
