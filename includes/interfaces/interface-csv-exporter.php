<?php

namespace EverAccounting\Interfaces;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Promise for structuring CSV exporters.
 *
 * @since 2.0
 */
interface CSV_Exporter extends Exporter {

	/**
	 * Sets the CSV columns.
	 *
	 * @access public
	 * @return array<string,string> CSV columns.
	 * @since  2.0
	 *
	 */
	public function csv_cols();

	/**
	 * Retrieves the CSV columns array.
	 *
	 * Alias for csv_cols(), usually used to implement a filter on the return.
	 *
	 * @access public
	 * @return array<string,string> CSV columns.
	 * @since  2.0
	 *
	 */
	public function get_csv_cols();

	/**
	 * Outputs the CSV columns.
	 *
	 * @access public
	 * @return void
	 * @since  2.0
	 *
	 */
	public function csv_cols_out();

	/**
	 * Outputs the CSV rows.
	 *
	 * @access public
	 * @return void
	 * @since  2.0
	 *
	 */
	public function csv_rows_out();

}
