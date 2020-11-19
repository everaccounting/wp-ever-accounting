<?php
/**
 * EverAccounting invoice Functions.
 *
 * All invoice related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */
defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning invoice.
 *
 * @param $invoice
 *
 * @return EverAccounting\Models\Invoice|null
 * @since 1.1.0
 */
function eaccounting_get_invoice( $invoice ) {

}

/**
 *  Create new invoice programmatically.
 *
 *  Returns a new invoice object on success.
 *
 * @param array $args {
 *  An array of elements that make up an invoice to update or insert.
 *
 * @type int $id The invoice ID. If equal to something other than 0,
 *                                         the invoice with that id will be updated. Default 0.
 *
 * @type string $invoice_number The number of the invoice. Default null.
 *
 * @type string $order_number The order number of the invoice. Default null.
 *
 * @type string $status The status for the invoice.Default is empty.
 *
 * @type string $invoiced_at The date when the invoice is created. Default null.
 * @type string $due_at The date when the invoice is created. Default null.
 * @type string $status The status for the invoice.Default is empty.
 * @type double $subtotal The subtotal of the invoice.
 * @type double $discount The discount of the invoice.
 * @type double $tax The tax of the invoice.
 * @type double $shipping The shipping of the invoice.
 * @type double $total The total of the invoice.
 * @type string $currency_code The currency_code for the invoice.
 * @type string $currency_rate The currency_rate for the invoice.
 * @type int $category_id The category_id for the invoice.
 * @type int $contact_id The contact_id for the invoice.
 * @type string $contact_name The contact_name for the invoice.
 * @type string $contact_email The contact_email for the invoice.
 * @type string $contact_tax_number The contact_tax_number for the invoice.
 * @type string $contact_phone The contact_phone for the invoice.
 * @type string $contact_address The contact_address for the invoice.
 * @type string $note The note for the invoice.
 * @type string $footer The footer for the invoice.
 * @type string $attachment The attachment for the invoice.
 * @type string $parent_id The parent_id for the invoice.
 *
 *
 * }
 *
 * @return EverAccounting\Models\Invoice|\WP_Error
 * @since 1.1.0
 */
function eaccounting_insert_invoice( $args ) {

}

/**
 * Delete an invoice.
 *
 * @param $invoice_id
 *
 * @return bool
 * @since 1.1.0
 */
function eaccounting_delete_invoice( $invoice_id ) {

}

/**
 * Get invoice items.
 *
 * @param array $args {
 *
 * @type int $id The invoice ID.
 * @type string $invoice_number The number of the invoice.
 * @type string $order_number The order number of the invoice.
 * @type string $status The status for the invoice.
 * @type string $invoiced_at The date when the invoice is created.
 * @type string $due_at The date when the invoice is created.
 * @type string $status The status for the invoice.
 * @type double $subtotal The subtotal of the invoice.
 * @type double $discount The discount of the invoice.
 * @type double $tax The tax of the invoice.
 * @type double $shipping The shipping of the invoice.
 * @type double $total The total of the invoice.
 * @type string $currency_code The currency_code for the invoice.
 * @type string $currency_rate The currency_rate for the invoice.
 * @type int $category_id The category_id for the invoice.
 * @type int $contact_id The contact_id for the invoice.
 * @type string $contact_name The contact_name for the invoice.
 * @type string $contact_email The contact_email for the invoice.
 * @type string $contact_tax_number The contact_tax_number for the invoice.
 * @type string $contact_phone The contact_phone for the invoice.
 * @type string $contact_address The contact_address for the invoice.
 * @type string $note The note for the invoice.
 * @type string $footer The footer for the invoice.
 * @type string $attachment The attachment for the invoice.
 * @type string $parent_id The parent_id for the invoice.
 * }
 *
 * @param bool $callback
 *
 * @return array|int
 * @since 1.1.0
 */
function eaccounting_get_invoices( $args = array(), $callback = true ) {

}

