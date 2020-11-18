<?php
/**
 * EverAccounting category Functions.
 *
 * All category related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit();

/**
 * Get all the available type of category the plugin support.
 *
 * @return array
 * @since 1.1.0
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
 * @return null|EverAccounting\Models\Category
 * @since 1.1.0
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
 * @param       $args {
 * An array of elements that make up an category to update or insert.
 *
 * @type int $id The category ID. If equal to something other than 0, the category with that ID will be updated. Default 0.
 *
 * @type string $name Unique name of the category.
 *
 * @type string $type Category type.
 *
 * @type string $color Color of the category.
 *
 * @type int $enabled The status of the category. Default 1.
 *
 * @type string $date_created The date when the category is created. Default is current current time.
 *
 * }
 *
 * @return WP_Error|\EverAccounting\Models\Category
 * @since 1.1.0
 */
function eaccounting_insert_category( $args ) {
	$category = new EverAccounting\Models\Category( $args );

	return $category->save();
}

/**
 * Delete a category.
 *
 * @param $category_id
 *
 * @return bool
 * @since 1.1.0
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
 * @param array $args {
 *  Optional. Arguments to retrieve categories.
 *
 * @type string $name Unique name of the category.
 *
 * @type string $type Category type.
 *
 * @type string $color Color of the category.
 *
 * @type int $enabled The status of the category.
 *
 * }
 *
 * @param bool  $callback
 *
 * @return array|int
 * @since 1.1.0
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
