<?php
/**
 * EverAccounting invoice item Functions.
 *
 * All invoice item related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */
defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning invoice item.
 *
 * @param $invoice_item
 *
 * @return EverAccounting\Models\InvoiceItem|null
 * @since 1.1.0
 */
function eaccounting_get_invoice_item( $invoice_item ) {

}

/**
 *  Create new invoice item programmatically.
 *
 *  Returns a new invoice item object on success.
 *
 * @param array $args {
 *  An array of elements that make up an invoice to update or insert.
 *
 * @type int $id The invoice item ID. If equal to something other than 0,
 *                                         the invoice item with that id will be updated. Default 0.
 * @type int $invoice_id The invoice_id of the invoice item.
 * @type int $item_id The item_id of the invoice item.
 * @type string $name The name for the invoice item.
 * @type string $sku The sku of the invoice item.
 * @type double $quantity The quantity of the item.
 * @type double $price The price for the item.
 * @type double $total The total of the item.
 * @type int $tax_id The tax_id of the invoice item.
 * @type string $tax_name The tax_name of the invoice item.
 * @type double $tax The tax of the invoice item.
 *
 * }
 *
 * @return EverAccounting\Models\InvoiceItem|\WP_Error
 * @since 1.1.0
 */
function eaccounting_insert_invoice_item( $args ) {

}

/**
 * Delete an invoice item.
 *
 * @param $invoice_item_id
 *
 * @return bool
 * @since 1.1.0
 */
function eaccounting_delete_invoice_item( $invoice_item_id ) {

}

/**
 * Get invoice items.
 *
 * @param array $args {
 *
 * @type int $invoice_id The invoice_id of the invoice item.
 * @type int $item_id The item_id of the invoice item.
 * @type string $name The name for the invoice item.
 * @type string $sku The sku of the invoice item.
 * @type double $quantity The quantity of the item.
 * @type double $price The price for the item.
 * @type double $total The total of the item.
 * @type int $tax_id The tax_id of the invoice item.
 * @type string $tax_name The tax_name of the invoice item.
 * @type double $tax The tax of the invoice item.
 * }
 *
 * @param bool $callback
 *
 * @return array|int
 * @since 1.1.0
 */
function eaccounting_get_invoice_items( $args = array(), $callback = true ) {

}

