<?php
/**
 * Handle bills import.
 *
 * @since   1.1.3
 *
 * @package EverAccounting\Import
 */

namespace EverAccounting\Import;

use EverAccounting\Abstracts\CSV_Importer;
use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit();


/**
 * Class Bills
 *
 * @since   1.1.3
 *
 * @package EverAccounting\Import
 */
class Bills extends CSV_Importer {
	/**
	 * Get supported key and readable label.
	 *
	 * @return array
	 * @since 1.1.3
	 */
	protected function get_headers() {
		return eaccounting_get_io_headers( 'bill' );
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
			'vendor_name'     => array( $this, 'parse_text_field' ),
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
		
		if ( empty( $data['vendor_name'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Vendor Name', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['issue_date'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Issue Date', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['due_date'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Issue Date', 'wp-ever-accounting' ) );
		}
		
		$category    = eaccounting_get_categories( array( 'search' => $data['category_name'], 'search_cols' => array( 'name' ), 'type' => 'expense', ) ); //phpcs:ignore
		$category    = ( $category ) ? reset( $category ) : '';
		$category_id = ! empty( $category ) ? $category->get_id() : '';
		
		$vendor    = eaccounting_get_vendors( array( 'search' => $data['vendor_name'], 'search_cols' => array( 'name' ), ) ); //phpcs:ignore
		$vendor    = ( $vendor ) ? reset( $vendor ) : '';
		$vendor_id = ! empty( $vendor ) ? $vendor->get_id() : '';
		
		if ( empty( $category_id ) ) {
			return new \WP_Error( 'invalid_props', __( 'Category does not exist.', 'wp-ever-accounting' ) );
		}
		
		if ( empty( $vendor_id ) ) {
			return new \WP_Error( 'invalid_props', __( 'Vendor does not exist.', 'wp-ever-accounting' ) );
		}
		
		$due  = eaccounting()->settings->get( 'bill_due', 15 );
		$bill = new Bill();
		$bill->set_props(
			array(
				'invoice_number' => $data['document_number'],
				'order_number'   => $data['order_number'],
				'status'         => $data['status'],
				'issue_date'     => $data['issue_date'] ? eaccounting_date( $data['issue_date'], 'Y-m-d' ) : date_i18n( 'Y-m-d' ),
				'due_date'       => $data['due_date'] ? eaccounting_date( $data['issue_date'], 'Y-m-d' ) : date_i18n( 'Y-m-d', strtotime( "+ $due days", current_time( 'timestamp' ) ) ), //phpcs:ignore,
				'payment_date'   => $data['payment_date'],
				'category_id'    => $category_id,
				'vendor_id'      => $vendor_id,
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
		
		
		$bill_items = array();
		
		if ( is_array( $items_array ) && ! empty( $items_array ) ) {
			foreach ( $items_array as $item ) {
				$bill_items[] = array(
					'item_id'       => $item[0]->get_id(),
					'item_name'     => $item[0]->get_name(),
					'document_id'   => $bill->get_id(),
					'currency_code' => $bill->get_currency_code(),
				);
				
			}
		}
		
		
		$bill->set_items( $bill_items );
		$totals = $bill->calculate_totals();
		$bill->set_subtotal( $totals['subtotal'] );
		$bill->set_total_tax( $totals['total_tax'] );
		$bill->set_total_shipping( $totals['total_shipping'] );
		$bill->set_total_fees( $totals['total_fees'] );
		$bill->set_total_discount( $totals['total_discount'] );
		$bill->set_total( $totals['total'] );
		$bill->save();
		
		return $bill;
	}
}
