<?php

namespace EverAccounting\Controllers;

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoices
 *
 * @package EverAccounting\Controllers
 */
class Invoices {

	/**
	 * Insert an invoice or update an existing one.
	 *
	 * @param array $data Invoice data.
	 * @param bool  $wp_error Whether to return false or WP_Error on failure.
	 *
	 * @since 1.0.0
	 * @return Invoice|bool|\WP_Error The Invoice object on success. False or WP_Error on failure.
	 */
	public function insert( $data = array(), $wp_error = true ) {
		return Invoice::insert( $data, $wp_error );
//		$invoice = Invoice::make( isset( $data['id'] ) ? $data['id'] : null );
		// If the invoice have any items, delete them first.
//		if ( ! empty( $invoice->items ) ) {
//			foreach ( $invoice->items as $item ) {
//				foreach ($item->taxes as $tax){
//					$tax->delete();
//				}
//				$item->delete();
//			}
//			$invoice->items = array();
//		}
//
//		var_dump($invoice);
	}
}
