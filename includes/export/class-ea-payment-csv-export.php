<?php

namespace EverAccounting\Export;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Batch_Exporter;
use EverAccounting\Query_Transaction;

class Payment_CSV_Export extends CSV_Batch_Exporter {

	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'payments';


	/**
	 * Return an array of columns to export.
	 *
	 * @return array
	 * @since  1.0.2
	 */
	public function get_csv_columns() {
		return array(
			'paid_at'        => __( 'Paid At', 'wp-ever-accounting' ),
			'amount'         => __( 'Amount', 'wp-ever-accounting' ),
			'currency_code'  => __( 'Currency Code', 'wp-ever-accounting' ),
			'currency_rate'  => __( 'Currency Rate', 'wp-ever-accounting' ),
			'account_id'     => __( 'Account ID', 'wp-ever-accounting' ),
			'invoice_id'     => __( 'Invoice ID', 'wp-ever-accounting' ),
			'contact_id'     => __( 'Contact ID', 'wp-ever-accounting' ),
			'category_id'    => __( 'Category ID', 'wp-ever-accounting' ),
			'description'    => __( 'Description', 'wp-ever-accounting' ),
			'payment_method' => __( 'Payment Method', 'wp-ever-accounting' ),
			'reference'      => __( 'Reference', 'wp-ever-accounting' ),
			'reconciled'     => __( 'Reconciled', 'wp-ever-accounting' ),
		);
	}

	/**
	 *
	 * @since 1.0.2
	 */
	public function set_data() {
		$args              = array(
			'per_page' => $this->get_limit(),
			'page'     => $this->get_page(),
			'orderby'  => 'id',
			'order'    => 'ASC',
			'type'     => 'expense',
		);
		$query             = Query_Transaction::init()->where( $args );
		$items             = $query->get( OBJECT, 'eaccounting_get_transaction' );
		$this->total_count = $query->count();
		$this->rows        = array();

		foreach ( $items as $item ) {
			$this->rows[] = $this->generate_row_data( $item );
		}
	}


	/**
	 * Take a revenue and generate row data from it for export.
	 *
	 *
	 * @param \EverAccounting\Transaction $item
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		foreach($this->get_csv_columns() as $column => $label){
			$value = null;
			switch($column){
				case 'paid_at':
					$value = $item->get_paid_at();
					break;
				case 'amount':
					$value = $item->get_amount();
					break;
				case 'currency_code':
					$value = $item->get_currency_code();
					break;
				case 'currency_rate':
					$value = $item->get_currency_rate();
					break;
				case 'account_id':
					$value = $item->get_account_id();
					break;
				case 'invoice_id':
					$value = $item->get_invoice_id();
					break;
				case 'contact_id':
					$value = $item->get_contact_id();
					break;
				case 'category_id':
					$value = $item->get_category_id();
					break;
				case 'description':
					$value = $item->get_description();
					break;
				case 'payment_method':
					$value = $item->get_payment_method();
					break;
				case 'reference':
					$value = $item->get_reference();
					break;
				case 'reconciled':
					$value = $item->get_reconciled();
					break;
				default:
					$value = apply_filters('eaccounting_payment_csv_row_item', '', $column, $item, $this);
			}
			$props[$column] = $value;
		}

		return $props;
	}
}
