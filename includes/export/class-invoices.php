<?php
/**
 * Handle invoices export.
 *
 * @since   1.1.3
 *
 * @package EverAccounting\Export
 */

namespace EverAccounting\Export;

use EverAccounting\Abstracts\CSV_Exporter;

defined( 'ABSPATH' ) || exit();

/**
 * Class Invoices
 *
 * @since   1.1.3
 *
 * @package EverAccounting\Export
 */
class Invoices extends CSV_Exporter {

	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	public $export_type = 'invoices';


	/**
	 * Return an array of columns to export.
	 *
	 * @return array
	 * @since  1.1.3
	 */
	public function get_columns() {
		return eaccounting_get_io_headers( 'invoice' );
	}

	/**
	 *
	 * @since 1.1.3
	 */
	public function get_rows() {
		$args  = array(
			'per_page' => $this->limit,
			'page'     => $this->page,
			'orderby'  => 'id',
			'order'    => 'ASC',
			'return'   => 'objects',
			'number'   => - 1,
		);
		$args  = apply_filters( 'eaccounting_invoice_export_query_args', $args );
		$items = eaccounting_get_invoices( $args );
		$rows  = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a product and generate row data from it for export.
	 *
	 * @param \EverAccounting\Models\Invoice $item
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		foreach ( $this->get_columns() as $column => $label ) {
			$value = null;
			switch ( $column ) {
				case 'document_number':
					$value = $item->get_document_number();
					break;
				case 'order_number':
					$value = $item->get_order_number();
					break;
				case 'status':
					$value = $item->get_status();
					break;
				case 'issue_date':
					$value = $item->get_issue_date();
					break;
				case 'due_date':
					$value = $item->get_due_date();
					break;
				case 'payment_date':
					$value = $item->get_payment_date();
					break;
				case 'category_name':
					$category_id = $item->get_category_id();
					$category    = eaccounting_get_category( $category_id );
					$value       = ( $category->exists() ) ? $category->get_name() : '';
					break;
				case 'customer_name':
					$customer_id = $item->get_contact_id();
					$customer    = eaccounting_get_customer( $customer_id );
					$value       = ( $customer->exists() ) ? $customer->get_name() : '';
					break;
				case 'items':
					$document_items = $item->get_items();
					$item_names     = array();
					foreach ( $document_items as $single ) {
						$item_names[] = $single->get_item_name();
					}
					$value = implode( ',', $item_names );
					break;
				case 'discount':
					$value = $item->get_discount();
					break;
				case 'discount_type':
					$value = $item->get_discount_type();
					break;
				case 'subtotal':
					$value = $item->get_subtotal();
					break;
				case 'total_shipping':
					$value = $item->get_total_shipping();
					break;
				case 'currency_code':
					$value = $item->get_currency_code();
					break;
				case 'total':
					$value = $item->get_total();
					break;
				case 'paid':
					$value = $item->get_total_paid();
					break;
				case 'due':
					$value = $item->get_total_due();
					break;
				case 'key':
					$value = $item->get_key();
					break;
				case 'note':
					$value = $item->get_note();
					break;
				default:
					$value = apply_filters( 'eaccounting_invoice_csv_row_item', '', $column, $item, $this );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
