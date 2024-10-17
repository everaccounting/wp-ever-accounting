<?php

namespace EverAccounting\Controllers;

use EverAccounting\Models\DocumentItem;
use EverAccounting\Models\DocumentTax;
use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

/**
 * Invoices controller.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Controllers
 */
class Invoices {

	/**
	 * Get an invoice from the database.
	 *
	 * @param mixed $invoice Invoice ID or object.
	 *
	 * @since 1.1.6
	 * @return Invoice|null Invoice object if found, otherwise null.
	 */
	public function get( $invoice ) {
		return Invoice::find( $invoice );
	}

	/**
	 * Insert a new invoice into the database.
	 *
	 * @param array $data Invoice data.
	 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
	 *
	 * @since 1.1.0
	 * @return Invoice|false|\WP_Error Invoice object on success, false or WP_Error on failure.
	 */
	public function insert( $data, $wp_error = true ) {
		global $wpdb;
		$invoice = Invoice::make( $data );
		// Setup invoice number.
		if ( ! $invoice->number ) {
			$invoice->number = $invoice->get_next_number();
		}

		if ( array_key_exists( 'items', $data ) && is_array( $data['items'] ) ) {
			$invoice->items()->delete();
			$invoice->items = array();

			foreach ( $data['items'] as $item_data ) {

				$item_data['quantity'] = isset( $item_data['quantity'] ) ? floatval( $item_data['quantity'] ) : 1;
				$item_data['item_id']  = isset( $item_data['item_id'] ) ? absint( $item_data['item_id'] ) : 0;
				$item                  = EAC()->items->get( $item_data['item_id'] );

				// If item not found, skip.
				if ( ! $item || $item_data['quantity'] <= 0 ) {
					continue;
				}
				$item_data['id']          = 0;
				$item_data['name']        = isset( $item_data['name'] ) ? sanitize_text_field( $item_data['name'] ) : $item->name;
				$item_data['description'] = isset( $item_data['description'] ) ? sanitize_text_field( $item_data['description'] ) : $item->description;
				$item_data['unit']        = isset( $item_data['unit'] ) ? sanitize_text_field( $item_data['unit'] ) : $item->unit;
				$item_data['type']        = isset( $item_data['type'] ) ? sanitize_text_field( $item_data['type'] ) : $item->type;
				$item_data['price']       = isset( $item_data['price'] ) ? floatval( $item_data['price'] ) : $item->price;
				$item_data['quantity']    = isset( $item_data['quantity'] ) ? floatval( $item_data['quantity'] ) : 1;
				$item_data['subtotal']    = isset( $item_data['subtotal'] ) ? floatval( $item_data['subtotal'] ) : $item_data['price'] * $item_data['quantity'];
				$item_data['discount']    = isset( $item_data['discount'] ) ? floatval( $item_data['discount'] ) : 0;
				$item_data['tax']         = isset( $item_data['tax'] ) ? floatval( $item_data['tax'] ) : 0;

				$invoice_item = new DocumentItem( $item_data );
				if ( array_key_exists( 'taxes', $item_data ) && is_array( $item_data['taxes'] ) ) {
					$invoice_item->taxes = array();

					foreach ( $item_data['taxes'] as $tax_data ) {
						$tax_data['tax_id'] = isset( $tax_data['tax_id'] ) ? absint( $tax_data['tax_id'] ) : 0;
						$tax_rate           = EAC()->taxes->get( $tax_data['tax_id'] );

						// If tax rate not found, skip.
						if ( ! $tax_rate ) {
							continue;
						}

						$tax_data['id']       = 0;
						$tax_data['name']     = isset( $tax_data['name'] ) ? sanitize_text_field( $tax_data['name'] ) : $tax_rate->name;
						$tax_data['rate']     = isset( $tax_data['rate'] ) ? floatval( $tax_data['rate'] ) : $tax_rate->rate;
						$tax_data['compound'] = isset( $tax_data['compound'] ) ? (bool) $tax_data['compound'] : $tax_rate->compound;
						$item_tax             = new DocumentTax( $tax_data );
						$invoice_item->taxes  = array_merge( $invoice_item->taxes, array( $item_tax ) );
					}

					// Update item taxes.
					$disc_subtotal = max( 0, $invoice_item->subtotal - $invoice_item->discount );
					$simple_tax    = 0;
					$compound_tax  = 0;
					foreach ( $invoice_item->taxes as &$item_tax ) {
						$item_tax->amount = $item_tax->compound ? 0 : ( $disc_subtotal * $item_tax->rate / 100 );
						$simple_tax      += $item_tax->amount;
					}
					foreach ( $invoice_item->taxes as &$item_tax ) {
						if ( $item_tax->compound ) {
							$item_tax->amount = ( $disc_subtotal + $simple_tax ) * $item_tax->rate / 100;
							$compound_tax    += $item_tax->amount;
						}
					}

					$invoice_item->tax = $simple_tax + $compound_tax;
				}

				$invoice_item->total = $invoice_item->subtotal - $invoice_item->discount + $invoice_item->tax;
				$invoice_item->total = max( 0, $invoice_item->total );
				$invoice->items      = array_merge( $invoice->items, array( $invoice_item ) );
			}
		}

		// Calculate invoice totals.
		$invoice->calculate_totals();

		$wpdb->query( 'START TRANSACTION' );
		$retval = $invoice->save();
		if ( is_wp_error( $retval ) ) {
			return $wp_error ? $retval : false;
		}

		foreach ( $invoice->items as $item ) {
			$item->document_id = $invoice->id;
			$retval            = $item->save();
			if ( is_wp_error( $retval ) ) {
				return $wp_error ? $retval : false;
			}

			foreach ( $item->taxes as $tax ) {
				$tax->document_id      = $invoice->id;
				$tax->document_item_id = $item->id;
				$retval                = $tax->save();
				if ( is_wp_error( $retval ) ) {
					return $wp_error ? $retval : false;
				}
			}
		}

		$wpdb->query( 'COMMIT' );

		return $invoice;
	}

	/**
	 * Delete an invoice from the database.
	 *
	 * @param int $id Invoice ID.
	 *
	 * @since 1.1.0
	 * @return bool True on success, false on failure.
	 */
	public function delete( $id ) {
		$invoice = $this->get( $id );
		if ( ! $invoice ) {
			return false;
		}

		return $invoice->delete();
	}

	/**
	 * Get query results for invoices.
	 *
	 * @param array $args Query arguments.
	 * @param bool  $count Optional. Whether to return only the total found invoices for the query.
	 *
	 * @since 1.1.0
	 * @return array|int|Invoice[] Array of invoice objects, the total found invoices for the query, or the total found invoices for the query as int when `$count` is true.
	 */
	public function query( $args = array(), $count = false ) {
		if ( $count ) {
			return Invoice::count( $args );
		}

		return Invoice::results( $args );
	}

	/**
	 * Get invoice statuses.
	 *
	 * @return mixed|void
	 */
	public function get_statuses() {
		$statuses = array(
			'draft'     => esc_html__( 'Draft', 'wp-ever-accounting' ),
			'sent'      => esc_html__( 'Sent', 'wp-ever-accounting' ),
			'partial'   => esc_html__( 'Partial', 'wp-ever-accounting' ),
			'paid'      => esc_html__( 'Paid', 'wp-ever-accounting' ),
			'overdue'   => esc_html__( 'Overdue', 'wp-ever-accounting' ),
			'cancelled' => esc_html__( 'Cancelled', 'wp-ever-accounting' ),
		);

		return apply_filters( 'eac_invoice_statuses', $statuses );
	}

	/**
	 * Get invoice columns.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'item'     => get_option( 'eac_invoice_col_item_label', esc_html__( 'Item', 'wp-ever-accounting' ) ),
			'price'    => get_option( 'eac_invoice_col_price_label', esc_html__( 'Price', 'wp-ever-accounting' ) ),
			'quantity' => get_option( 'eac_invoice_col_quantity_label', esc_html__( 'Quantity', 'wp-ever-accounting' ) ),
			'tax'      => get_option( 'eac_invoice_col_tax_label', esc_html__( 'Tax', 'wp-ever-accounting' ) ),
			'subtotal' => get_option( 'eac_invoice_col_subtotal_label', esc_html__( 'Subtotal', 'wp-ever-accounting' ) ),
		);

		return apply_filters( 'eac_invoice_columns', $columns );
	}
}
