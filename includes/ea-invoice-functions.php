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

function eaccounting_get_invoice_statuses() {
	return array(
		'draft'     => __( 'Draft', 'wp-ever-accounting' ),
		'sent'      => __( 'Sent', 'wp-ever-accounting' ),
		'viewed'    => __( 'Viewed', 'wp-ever-accounting' ),
		'approved'  => __( 'Approved', 'wp-ever-accounting' ),
		'partial'   => __( 'Partial', 'wp-ever-accounting' ),
		'paid'      => __( 'Paid', 'wp-ever-accounting' ),
		'overdue'   => __( 'Overdue', 'wp-ever-accounting' ),
		'unpaid'    => __( 'Unpaid', 'wp-ever-accounting' ),
		'cancelled' => __( 'Cancelled', 'wp-ever-accounting' ),
	);
}

/**
 * Get next invoice number.
 *
 * @return string
 * @since 1.1.0
 */
function eaccounting_get_next_invoice_number() {
	$prefix = 'INV-';
	$next   = '1';
	$digit  = 5;

	return $prefix . str_pad( $next, $digit, '0', STR_PAD_LEFT );
}

/**
 * Main function for returning invoice.
 *
 * @param $invoice
 *
 * @return EverAccounting\Models\Invoice|null
 * @since 1.1.0
 *
 */
function eaccounting_get_invoice( $invoice ) {

}

/**
 *  Create new invoice programmatically.
 *
 *  Returns a new invoice object on success.
 *
 * @param array $args {
 *                                  An array of elements that make up an invoice to update or insert.
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
 *
 */
function eaccounting_insert_invoice( $args, $wp_error = true ) {

}

/**
 * Delete an invoice.
 *
 * @param $invoice_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_invoice( $invoice_id ) {

}

/**
 * Get invoice items.
 *
 * @param bool $callback
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
 * @return array|int
 * @since 1.1.0
 *
 */
function eaccounting_get_invoices( $args = array(), $callback = true ) {

}


/**
 * Main function for returning invoice history.
 *
 * @param $invoice_history
 *
 * @return EverAccounting\Models\InvoiceHistory|null
 * @since 1.1.0
 *
 */
function eaccounting_get_invoice_history( $invoice_history ) {

}

/**
 *  Create new invoice history programmatically.
 *
 *  Returns a new invoice history object on success.
 *
 * @param array $args {
 *                           An array of elements that make up an invoice to update or insert.
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
 *
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
 *
 */
function eaccounting_delete_invoice_history( $invoice_history_id ) {

}

/**
 * Get invoice histories.
 *
 * @param bool $callback
 *
 * @param array $args {
 *
 * @type string $status The status for the invoice history.
 * @type int $notify The notify of the invoice history.
 * @type string $description The description of the invoice history.
 * }
 *
 * @return array|int
 * @since 1.1.0
 *
 */
function eaccounting_get_invoice_histories( $args = array(), $callback = true ) {

}


/**
 * Main function for returning invoice item.
 *
 * @param $invoice_item
 *
 * @return EverAccounting\Models\InvoiceItem|null
 * @since 1.1.0
 *
 */
function eaccounting_get_invoice_item( $invoice_item ) {

}

/**
 *  Create new invoice item programmatically.
 *
 *  Returns a new invoice item object on success.
 *
 * @param array $args {
 *                          An array of elements that make up an invoice to update or insert.
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
 *
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
 *
 */
function eaccounting_delete_invoice_item( $invoice_item_id ) {

}

/**
 * Get invoice items.
 *
 * @param bool $callback
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
 * @return array|int
 * @since 1.1.0
 *
 */
function eaccounting_get_invoice_items( $args = array(), $callback = true ) {

}
