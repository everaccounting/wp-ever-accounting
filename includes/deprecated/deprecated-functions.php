<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 * @version  1.1.0
 */

use Ever_Accounting\Account;
use Ever_Accounting\Currency;
use Ever_Accounting\Category;
use Ever_Accounting\Item;

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

/**
 * Main function for returning item.
 *
 * @param $item
 *
 * @return Item|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_item( $item ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Items::get()' );

	return \Ever_Accounting\Items::get( $item );
}

/**
 * Get item by sku.
 *
 * @param $sku
 *
 * @return Item
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_item_by_sku( $sku ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Items::get_by_sku()' );

	return \Ever_Accounting\Items::get_by_sku( $sku );
}

/**
 *  Create new item programmatically.
 *
 * @param array $args Arguments.
 * @param \WP_Error $wp_error Either true or false
 *
 * @return Item|\WP_Error
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_insert_item( $args, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Items::insert()' );

	return \Ever_Accounting\Items::insert( $args );
}

/**
 * Delete an item.
 *
 * @param $item_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_delete_item( $item_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Items::delete()' );

	return \Ever_Accounting\Items::delete( $item_id );
}

/**
 * Get items.
 *
 * @param array $args
 *
 * @return array|int
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_items( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Items::query()' );

	return \Ever_Accounting\Items::query( $args );
}

/**
 * Main function for returning account.
 *
 * @param array|int $account Account array or id.
 *
 * @return Account|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_account( $account ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Accounts::get()' );

	return \Ever_Accounting\Accounts::get( $account );
}

/**
 * Get account currency code
 *
 * @param Account $account account object.
 *
 * @return mixed|null
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_account_currency_code( $account ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Accounts::get_currency_code()' );

	return \Ever_Accounting\Accounts::get_currency_code( $account );
}

/**
 * Create new account programmatically.
 *
 * @param array $args
 *
 * @return Account| \WP_Error
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_insert_account( $data, $wp_error = true ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Accounts::insert()' );

	return \Ever_Accounting\Accounts::insert( $data );
}

/**
 * Delete an account.
 *
 * @param $account_id
 *
 * @return bool
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_delete_account( $account_id ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Accounts::delete()' );

	return \Ever_Accounting\Accounts::delete( $account_id );
}

/**
 * Get account items.
 *
 * @param array $args
 *
 * @return array|int
 * @since 1.1.0
 * @deprecatd 1.1.4
 */
function eaccounting_get_accounts( $args = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.4', '\Ever_Accounting\Accounts::query()' );

	return \Ever_Accounting\Accounts::query( $args );
}

