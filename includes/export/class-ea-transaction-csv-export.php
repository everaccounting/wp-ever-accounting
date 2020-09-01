<?php

namespace EverAccounting\Export;

use EverAccounting\Abstracts\CSV_Exporter;
use EverAccounting\Query_Transaction;

class Transaction_CSV_Export extends CSV_Batch_Exporter {

	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'transactions';

	/**
	 * Transaction_CSV_Export constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Return an array of columns to export.
	 *
	 * @since  1.0.2
	 * @return array
	 */
	public function get_default_column_names() {
		return apply_filters(
			"eaccounting_product_export_{$this->export_type}_default_columns",
			array(
				'id'     => __( 'ID', 'wp-ever-accounting' ),
				'type'   => __( 'Type', 'wp-ever-accounting' ),
				'amount' => __( 'Amount', 'wp-ever-accounting' ),
			)
		);
	}

	/**
	 *
	 * @since 1.0.2
	 */
	public function prepare_data_to_export() {
		$args = array(
			'per_page' => $this->get_limit(),
			'page'     => $this->get_page(),
			'orderby'  => 'id',
			'order'    => 'ASC',
		);
		$query = Query_Transaction::init()->where( $args );
		$transactions     = $query->get();
		$this->total_rows = $query->count();
		$this->row_data   = array();

		foreach ( $transactions as $transaction ) {
			$this->row_data[] = $this->generate_row_data( $transaction );
		}
	}


	/**
	 * Take a product and generate row data from it for export.
	 *
	 *
	 * @param $transaction
	 *
	 * @return array
	 */
	protected function generate_row_data( $transaction ) {
		return get_object_vars( $transaction );
	}
}
