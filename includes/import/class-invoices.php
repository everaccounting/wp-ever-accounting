<?php
/**
 * Handle invoices import.
 *
 * @since   1.1.3
 *
 * @package EverAccounting\Import
 */

namespace EverAccounting\Import;

use EverAccounting\Abstracts\CSV_Importer;
use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit();


/**
 * Class Invoices
 *
 * @since   1.1.3
 *
 * @package EverAccounting\Import
 */
class Invoices extends CSV_Importer {
	/**
	 * Get supported key and readable label.
	 *
	 * @return array
	 * @since 1.1.3
	 */
	protected function get_headers() {
		return eaccounting_get_io_headers( 'invoice' );
	}


	/**
	 * Return the required key to import item.
	 *
	 * @return array
	 * @since 1.1.3
	 */
	public function get_required() {
		return array( 'currency_code', 'category_name', 'customer_name', 'issue_date', 'due_date' );
	}

	/**
	 * Get formatting callback.
	 *
	 * @return array
	 * @since 1.1.3
	 */
	protected function get_formatting_callback() {
		return array(
			'document_number' => array( $this, 'parse_text_field' ),
			'order_number'    => array( $this, 'parse_text_field' ),
			'status'          => array( $this, 'parse_text_field' ),
			'issue_date'      => array( $this, 'parse_date_field' ),
			'due_date'        => array( $this, 'parse_date_field' ),
			'payment_date'    => array( $this, 'parse_date_field' ),
			'category_name'   => array( $this, 'parse_text_field' ),
			'customer_name'   => array( $this, 'parse_text_field' ),
			'items'           => array( $this, 'parse_text_field' ),
			'discount'        => array( $this, 'parse_text_field' ),
			'discount_type'   => array( $this, 'parse_text_field' ),
			'subtotal'        => array( $this, 'parse_text_field' ),
			'total_shipping'  => array( $this, 'parse_text_field' ),
			'currency_code'   => array( $this, 'parse_currency_code_field' ),
			'total'           => array( $this, 'parse_text_field' ),
			'paid'            => array( $this, 'parse_text_field' ),
			'due'             => array( $this, 'parse_text_field' ),
			'key'             => array( $this, 'parse_text_field' ),
			'note'            => array( $this, 'parse_description_field' ),
		);
	}

	/**
	 * Process a single item and save.
	 *
	 * @param array $data Raw CSV data.
	 *
	 * @return string|\WP_Error
	 */
	protected function import_item( $data ) {
		if ( empty( $data['currency_code'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Currency Code', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['category_name'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Category Name', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['customer_name'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Customer Name', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['issue_date'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Issue Date', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['due_date'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Issue Date', 'wp-ever-accounting' ) );
		}

		$category    = eaccounting_get_categories( array( 'search' => $data['category_name'], 'search_cols' => array( 'name' ), 'type' => 'income', ) ); //phpcs:ignore
		$category    = ( $category ) ? reset( $category ) : '';
		$category_id = ! empty( $category ) ? $category->get_id() : '';

		$customer    = eaccounting_get_customers( array( 'search' => $data['customer_name'], 'search_cols' => array( 'name' ), ) ); //phpcs:ignore
		$customer    = ( $customer ) ? reset( $customer ) : $customer;
		$customer_id = ! empty( $customer ) ? $customer->get_id() : '';

		if ( empty( $category_id ) ) {
			return new \WP_Error( 'invalid_props', __( 'Category does not exist.', 'wp-ever-accounting' ) );
		}

		if ( empty( $customer_id ) ) {
			return new \WP_Error( 'invalid_props', __( 'Customer does not exist.', 'wp-ever-accounting' ) );
		}

		$due     = eaccounting()->settings->get( 'invoice_due', 15 );
		$invoice = new \EverAccounting\Models\Invoice();
		$invoice->set_props(
			array(
				'invoice_number' => $data['document_number'],
				'order_number'   => $data['order_number'],
				'status'         => $data['status'],
				'issue_date'     => $data['issue_date'] ? eaccounting_date( $data['issue_date'], 'Y-m-d' ) : date_i18n( 'Y-m-d' ),
				'due_date'       => $data['due_date'] ? eaccounting_date( $data['due_date'], 'Y-m-d' ) : date_i18n( 'Y-m-d', strtotime( "+ $due days", current_time( 'timestamp' ) ) ), //phpcs:ignore,
				'payment_date'   => eaccounting_date( $data['payment_date'], 'Y-m-d' ),
				'category_id'    => $category_id,
				'customer_id'    => $customer_id,
				'currency_code'  => $data['currency_code'],
				'discount'       => $data['discount'],
				'discount_type'  => $data['discount_type'],
				'key'            => $data['key'],
				'note'           => $data['note'],
			)
		);

		$items       = $data['items'];
		$all_items   = explode( ',', $items );
		$items_array = array();
		foreach ( $all_items as $item ) {
			$items_array[] = eaccounting_get_items( array( 'search' => $item, 'search_cols' => array( 'name' ) ) ); //phpcs:ignore
		}

		$invoice_items = array();
		if ( is_array( $items_array ) && ! empty( $items_array ) ) {
			foreach ( $items_array as $item ) {
				$invoice_items[] = array(
					'item_id'       => $item[0]->get_id(),
					'item_name'     => $item[0]->get_name(),
					'document_id'   => $invoice->get_id(),
					'currency_code' => $invoice->get_currency_code(),
				);

			}
		}


		$invoice->set_items( $invoice_items );

		$totals = $invoice->calculate_totals();
		$invoice->set_subtotal( $totals['subtotal'] );
		$invoice->set_total_tax( $totals['total_tax'] );
		$invoice->set_total_shipping( $totals['total_shipping'] );
		$invoice->set_total_fees( $totals['total_fees'] );
		$invoice->set_total_discount( $totals['total_discount'] );
		$invoice->set_total( $totals['total'] );
		$invoice->save();

		if ( ! empty( $data['payment_date'] ) && ! empty( $data['paid'] ) ) {
			$invoice->add_payment( array( 'date' => $data['payment_date'], 'amount' => $data['paid'], 'account_id' => eaccounting()->settings->get( 'default_account' ), 'payment_method' => eaccounting()->settings->get( 'default_payment_method' ) ) );
		}

		return $invoice;
	}
}
