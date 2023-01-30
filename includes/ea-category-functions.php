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
 * @since 1.1.0
 * @return array
 */
function eaccounting_get_category_types() {
	$types = array(
		'expense' => esc_html__( 'Expense', 'wp-ever-accounting' ),
		'income'  => esc_html__( 'Income', 'wp-ever-accounting' ),
		'other'   => esc_html__( 'Other', 'wp-ever-accounting' ),
		'item'    => esc_html__( 'Item', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_category_types', $types );
}

/**
 * Get category.
 *
 * @param mixed $category Category ID or object.
 *
 * @since 1.1.0
 * @return null|EverAccounting\Models\Category
 */
function eaccounting_get_category( $category ) {
	return \EverAccounting\Models\Category::get( $category );
}

/**
 * Insert a category.
 *
 * @param array $data {
 *                            An array of elements that make up an category to update or insert.
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
 * @param bool $wp_error Whether to return false or WP_Error on failure.
 *
 * @since 1.1.0
 * @return int|\WP_Error|\EverAccounting\Models\Category|bool The value 0 or WP_Error on failure. The Category object on success.
 */
function eaccounting_insert_category( $data = array(), $wp_error = true ) {
	return \EverAccounting\Models\Category::insert( $data, $wp_error );
}

/**
 * Delete a category.
 *
 * @param int $category_id Category ID.
 *
 * @since 1.1.0
 * @return bool
 */
function eaccounting_delete_category( $category_id ) {
	try {
		$category = new EverAccounting\Models\Category( $category_id );

		return $category->exists() ? $category->delete() : false;
	} catch ( \Exception $e ) {
		return false;
	}
}

/**
 * Get category items.
 *
 * @param array $args Query arguments.
 *
 * @since 1.1.0
 * @return int|array|null
 */
function eaccounting_get_categories( $args = array() ) {
	return \EverAccounting\Models\Category::query( $args );
}
