<?php
defined( 'ABSPATH' ) || exit();
/**
 * Get all the available type of category the plugin support.
 *
 * @since      1.0.2
 * @deprecated 1.1.0
 * @return array
 */
function eaccounting_get_category_types() {
	return \EverAccounting\Categories\get_types();
}

/**
 * Get category.
 *
 * @since      1.0.2
 *
 * @deprecated 1.1.0
 *
 * @param $category
 *
 * @return null|EverAccounting\Categories\Category
 */
function eaccounting_get_category( $category ) {
	return \EverAccounting\Categories\get( $category );
}

/**
 * Insert a category.
 *
 * @since      1.0.2
 *
 * @deprecated 1.1.0
 *
 * @param       $args  {
 *
 * @type string $name  Unique name of the category.
 * @type string $type  Category type.
 * @type string $color Color of the category
 * }
 * @return WP_Error|Mixed
 */
function eaccounting_insert_category( $args ) {
	return \EverAccounting\Categories\insert( $args );
}

/**
 * Delete a category.
 *
 * @since      1.0.2
 *
 * @deprecated 1.1.0
 *
 * @param $category_id
 *
 * @return bool
 */
function eaccounting_delete_category( $category_id ) {
	return \EverAccounting\Categories\delete( $category_id );
}
