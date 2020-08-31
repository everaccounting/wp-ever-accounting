<?php
/**
 * Handles Batch CSV export.
 *
 * @package  WooCommerce/Export
 * @version  3.1.0
 */

namespace EverAccounting\Export;

use EverAccounting\Abstracts\CSV_Batch_Exporter;

defined( 'ABSPATH' ) || exit;

class Export_Payments extends CSV_Batch_Exporter {

	/**
	 * Name of the back process.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $process = 'export-payments';

	/**
	 * Export type.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'payments';

	/**
	 * Exporter capability.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $capability = 'manage_options';

	/**
	 * Export_Payments constructor.
	 */
	public function __construct( $data = null ) {
	}

	/**
	 * Sets the CSV columns.
	 *
	 * @access public
	 * @since  1.0.2
	 *
	 * @return array<string,string> CSV columns.
	 */
	public function csv_cols() {

	}

	/**
	 * Retrieves the data for export.
	 *
	 * @access public
	 * @since  1.0.2
	 *
	 * @return array[] Multi-dimensional array of data for export.
	 */
	public function get_data() {

	}
}
