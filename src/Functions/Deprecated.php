<?php

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning account.
 *
 * @param mixed $account Account ID or object.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return EverAccounting\Models\Account|null
 */
function eaccounting_get_account( $account ) {
	return eac_get_account( $account );
}

/**
 *  Create new account programmatically.
 *
 *  Returns a new account object on success.
 *
 * @param array $data Account data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @throws \Exception When account is not created.
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return EverAccounting\Models\Account|\WP_Error|bool
 */
function eaccounting_insert_account( $data, $wp_error = true ) {
	return eac_insert_account( $data, $wp_error );
}

/**
 * Delete an account.
 *
 * @param int $account_id Account ID.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return bool
 */
function eaccounting_delete_account( $account_id ) {
	return eac_delete_account( $account_id );
}

/**
 * Get account items.
 *
 * @param array $args Query arguments.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return array|int
 */
function eaccounting_get_accounts( $args = array() ) {
	$count = false;
	if ( ! empty( $args['count_total'] ) ) {
		$count = true;
	}

	return eac_get_accounts( $args, $count );
}


/**
 * Main function for returning item.
 *
 * @param mixed $item Item object.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return \EverAccounting\Models\Item|null
 */
function eaccounting_get_item( $item ) {
	return eac_get_item( $item );
}

/**
 *  Create new item programmatically.
 *
 *  Returns a new item object on success.
 *
 * @param array $data Item data.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return \EverAccounting\Models\Item|WP_Error|bool
 */
function eaccounting_insert_item( $data, $wp_error = true ) {
	return eac_insert_product( $data, $wp_error );
}

/**
 * Delete an item.
 *
 * @param int $item_id Item ID.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return bool
 */
function eaccounting_delete_item( $item_id ) {
	return eac_delete_item( $item_id );
}

/**
 * Get items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Return only the total found items.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return array|int
 */
function eaccounting_get_products( $args = array(), $count = false ) {
	return eac_get_products( $args, $count );
}

/**
 * Main function for returning currency.
 *
 * @param object|string|int $currency Currency object, code or ID.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return \EverAccounting\Models\Currency|null
 */
function eaccounting_get_currency( $currency ) {
	return eac_get_currency( $currency );
}

/**
 * Get currency rate.
 *
 * @param string $currency Currency code.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return mixed|null
 */
function eaccounting_get_currency_rate( $currency ) {
	return eac_get_currency_rate( $currency );
}


/**
 *  Create new currency programmatically.
 *
 *  Returns a new currency object on success.
 *
 * @param array $args Currency arguments.
 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return \EverAccounting\Models\Currency|\WP_Error|bool
 */
function eaccounting_insert_currency( $args, $wp_error = true ) {
	return eac_insert_currency( $args, $wp_error );
}

/**
 * Delete a currency.
 *
 * @param string $currency_code Currency code.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return bool
 */
function eaccounting_delete_currency( $currency_code ) {
	return eac_delete_currency( $currency_code );
}

/**
 * Get currency items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return count or items.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return array|int|null
 */
function eaccounting_get_currencies( $args = array(), $count = false ) {
	return eac_get_currencies( $args, $count );
}

/**
 * Get all the available type of category the plugin support.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return array
 */
function eaccounting_get_category_types() {
	return eac_get_category_types();
}

/**
 * Get category.
 *
 * @param mixed $category Category ID or object.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return null|EverAccounting\Models\Category
 */
function eaccounting_get_category( $category ) {
	return eac_get_category( $category );
}

/**
 * Insert a category.
 *
 * @param array $data Category data.
 * @param bool  $wp_error Whether to return false or WP_Error on failure.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return int|\WP_Error|\EverAccounting\Models\Category The value 0 or WP_Error on failure. The Category object on success.
 */
function eaccounting_insert_category( $data = array(), $wp_error = true ) {
	return eac_insert_category( $data, $wp_error );
}

/**
 * Delete a category.
 *
 * @param int $category_id Category ID.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return bool
 */
function eaccounting_delete_category( $category_id ) {
	return eac_delete_category( $category_id );
}

/**
 * Get category items.
 *
 * @param array $args Query arguments.
 * @param bool  $count Whether to return the count of items.
 *
 * @deprecated 1.1.6
 * @since 1.1.0
 * @return int|array|null
 */
function eaccounting_get_categories( $args = array(), $count = false ) {
	return eac_get_categories( $args, $count );
}
