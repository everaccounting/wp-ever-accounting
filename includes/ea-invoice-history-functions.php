<?php
/**
 * EverAccounting invoice history Functions.
 *
 * All invoice history related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */
defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning invoice history.
 *
 * @param $invoice_history
 *
 * @return EverAccounting\Models\InvoiceHistory|null
 * @since 1.1.0
 */
function eaccounting_get_invoice_history( $invoice_history ) {

}

/**
 *  Create new invoice history programmatically.
 *
 *  Returns a new invoice history object on success.
 *
 * @param array $args {
 *  An array of elements that make up an invoice to update or insert.
 *
 * @type int $id The invoice history ID. If equal to something other than 0,
 *                                         the invoice item with that id will be updated. Default 0.
 * @type string $status The status for the invoice history.
 * @type int $notify The notify of the invoice history.
 * @type string $description The description of the invoice history.
 *
 * }
 *
 * @return EverAccounting\Models\InvoiceHistory|\WP_Error
 * @since 1.1.0
 */
function eaccounting_insert_invoice_history( $args ) {

}

/**
 * Delete an invoice history.
 *
 * @param $invoice_history_id
 *
 * @return bool
 * @since 1.1.0
 */
function eaccounting_delete_invoice_history( $invoice_history_id ) {

}

/**
 * Get invoice histories.
 *
 * @param array $args {
 *
 * @type string $status The status for the invoice history.
 * @type int $notify The notify of the invoice history.
 * @type string $description The description of the invoice history.
 *
 * @param bool $callback
 *
 * @return array|int
 * @since 1.1.0
 */
function eaccounting_get_invoice_histories( $args = array(), $callback = true ) {

}

