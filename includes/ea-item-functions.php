<?php
/**
 * EverAccounting Item Functions.
 *
 * All item related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning item.
 *
 * @param $item
 *
 * @return EverAccounting\Models\Item|null
 * @since 1.1.0
 *
 */
function eaccounting_get_item( $item ) {

}


/**
 *  Create new item programmatically.
 *
 *  Returns a new item object on success.
 *
 * @param array $args {
 *  An array of elements that make up an invoice to update or insert.
 *
 * @type int $id The item ID. If equal to something other than 0,
 *                                         the item with that id will be updated. Default 0.
 * @type string $name The name of the item.
 * @type string $sku The sku of the item.
 * @type int $image_id The image_id for the item.
 * @type string $description The description of the item.
 * @type double $sale_price The sale_price of the item.
 * @type double $purchase_price The purchase_price for the item.
 * @type int $quantity The quantity of the item.
 * @type int $category_id The category_id of the item.
 * @type int $tax_id The tax_id of the item.
 * @type int $enabled The enabled of the item.
 * }
 *
 * @return EverAccounting\Models\Item|WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_item( $args ) {

}

/**
 * Delete an item.
 *
 * @param $item_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_item( $item_id ) {

}

/**
 * Get items.
 *
 * @param array $args {
 *
 * @type string $name The name of the item.
 * @type string $sku The sku of the item.
 * @type int $image_id The image_id for the item.
 * @type string $description The description of the item.
 * @type double $sale_price The sale_price of the item.
 * @type double $purchase_price The purchase_price for the item.
 * @type int $quantity The quantity of the item.
 * @type int $category_id The category_id of the item.
 * @type int $tax_id The tax_id of the item.
 * @type int $enabled The enabled of the item.
 * }
 *
 * @param bool $callback
 *
 * @return array|int
 * @since 1.1.0
 */
function eaccounting_get_items( $args = array(), $callback = true ) {

}
