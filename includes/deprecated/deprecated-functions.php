<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 * @version  1.1.0
 */

use Ever_Accounting\Currency;
use Ever_Accounting\Category;

defined( 'ABSPATH' ) || exit;


/**
 * Create new currency programmatically.
 *
 * @param array $args
 *
 * @since 1.1.0
 * @return Currency|\WP_Error
 * @deprecated 1.1.4
 */
function eaccounting_insert_currency( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Currencies::insert()' );

	return \Ever_Accounting\Currencies::insert( $args );
}

/**
 * Delete a currency.
 *
 * @param $currency_code
 *
 * @return bool
 * @since 1.1.0
 * @deprecated 1.1.4
 */
function eaccounting_delete_currency( $currency_code ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Currencies::delete()' );

	return \Ever_Accounting\Currencies::delete( $currency_code );
}

/**
 * Create new category programmatically.
 *
 * @param array $args
 *
 * @since 1.1.0
 * @return Category|\WP_Error
 * @deprecated 1.1.4
 */
function eaccounting_insert_category( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Categories::insert()' );

	return \Ever_Accounting\Categories::insert( $args );
}

/**
 * Delete a category.
 *
 * @param $category_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecated 1.1.4
 */
function eaccounting_delete_category( $category_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Categories::delete()' );

	return \Ever_Accounting\Categories::delete( $category_id );
}

/**
 * Get category items.
 *
 * @param array $args
 *
 * @return int|array|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_categories( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Categories::query()' );

	return \Ever_Accounting\Categories::query( $args );
}

/**
 * Get all the available type of category the plugin support.
 *
 * @return array
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_category_types() {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Categories::get_types()' );

	return \Ever_Accounting\Categories::get_types();
}

/**
 * Get the category type label of a specific type.
 *
 * @param $type
 *
 * @return string
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_category_type( $type ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Categories::get_type()' );

	return \Ever_Accounting\Categories::get_type( $type );
}
