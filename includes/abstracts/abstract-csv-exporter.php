<?php
/**
 * Overview of CSV Exporter Main Class.
 *
 * @package     EverAccounting
 * @subpackage  Abstracts
 * @since       1.0.2
 */

namespace EverAccounting\Export;
defined( 'ABSPATH' ) || exit();

abstract class CSV_Exporter {
	/**
	 * Our export type. Used for export-type specific filters/actions.
	 * @var string
	 * @since 1.0.2
	 */
	public $export_type = 'default';

	/**
	 * Capability needed to perform the current export.
	 *
	 * @access public
	 * @since  1.0.2
	 * @var    string
	 */
	public $capability = 'manage_options';

	/**
	 * Batch limit.
	 * @since 1.0.2
	 * @var integer
	 */
	protected $limit = 50;

	/**
	 * Number exported.
	 *
	 * @var integer
     * @since 1.0.2
	 */
	protected $exported_row_count = 0;

	/**
	 * Raw data to export.
	 *
	 * @var array
     * @since 1.0.2
	 */
	protected $row_data = array();

	/**
	 * Total rows to export.
	 *
	 * @var integer
     * @since 1.0.2
	 */
	protected $total_rows = 0;

	/**
	 * List of columns to export, or empty for all.
	 *
	 * @var array
     *           @since 1.0.2
	 */
	protected $columns_to_export = array();

	/**
	 * The delimiter parameter sets the field delimiter (one character only).
	 * @since 1.0.2
	 * @var string
	 */
	protected $delimiter = ',';

	/**
	 * Sets the CSV columns.
	 *
	 * @access public
	 * @return array<string,string> CSV columns.
	 * @since  1.0.2
	 *
	 */
	abstract public function csv_cols();

	/**
	 * Retrieves the data for export.
	 *
	 * @access public
	 * @return array[] Multi-dimensional array of data for export.
	 * @since  1.0.2
	 *
	 */
	abstract public function get_data();

	/**
	 * Can we export?
	 *
	 * @access public
	 * @return bool Whether we can export or not
	 * @since 1.0.2
	 */
	public function can_export() {
		/**
		 * Filters the capability needed to perform an export.
		 *
		 * @param string $capability Capability needed to perform an export.
		 *
		 * @since 1.0.2
		 *
		 */
		return (bool) current_user_can( apply_filters( 'eaccounting_export_capability', $this->capability ) );
	}

	/**
	 * Set the export headers
	 *
	 * @access public
	 * @return void
	 * @since 1.0.2
	 */
	public function headers() {
		ignore_user_abort( true );
		@set_time_limit( 0 );


		@ini_set( 'zlib.output_compression', 'Off' ); // @codingStandardsIgnoreLine
		@ini_set( 'output_buffering', 'Off' ); // @codingStandardsIgnoreLine
		@ini_set( 'output_handler', '' ); // @codingStandardsIgnoreLine
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=eaccounting-export-' . $this->export_type . '-' . date( 'm-d-Y' ) . '.csv' );
		header( 'Pragma: no-cache' );
		header( "Expires: 0" );
	}

	/**
	 * Retrieve the CSV columns
	 *
	 * @access public
	 * @return array $cols Array of the columns
	 * @since 1.0.2
	 */
	public function get_csv_cols() {
		$cols = $this->csv_cols();

		/**
		 * Filters the available CSV export columns for this export.
		 *
		 * This dynamic filter is appended with the export type string, for example:
		 *
		 *     `eaccounting_export_csv_cols_`
		 *
		 * @param array $cols The export columns available.
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'eaccounting_export_csv_cols_' . $this->export_type, $cols );
	}

	/**
	 * Output the CSV columns
	 *
	 * @access public
	 * @return void
	 * @since 1.0.2
	 */
	public function csv_cols_out() {
		$cols = $this->get_csv_cols();
		$i    = 1;
		foreach ( $cols as $col_id => $column ) {
			echo '"' . $column . '"';
			echo $i == count( $cols ) ? '' : ',';
			$i ++;
		}
		echo "\r\n";
	}

	/**
	 * Prepares a batch of data for export.
	 *
	 * @access public
	 *
	 * @param array $data Export data.
	 *
	 * @return array Filtered export data.
	 * @since  1.0.2
	 *
	 */
	public function prepare_data( $data ) {
		/**
		 * Filters the export data.
		 *
		 * The data set will differ depending on which exporter is currently in use.
		 *
		 * @param array $data Export data.
		 *
		 * @since 1.0.2
		 *
		 */
		$data = apply_filters( 'eaccounting_export_get_data', $data );

		/**
		 * Filters the export data for a given export type.
		 *
		 * The dynamic portion of the hook name, `$this->export_type`, refers to the export type.
		 *
		 * @param array $data Export data.
		 *
		 * @since 1.0.2
		 *
		 */
		$data = apply_filters( 'eaccounting_export_get_data_' . $this->export_type, $data );

		return $data;
	}

	/**
	 * Output the CSV rows
	 *
	 * @access public
	 * @return void
	 * @since 1.0.2
	 */
	public function csv_rows_out() {
		$data = $this->prepare_data( $this->get_data() );

		$cols = $this->get_csv_cols();

		// Output each row
		foreach ( $data as $row ) {
			$i = 1;
			foreach ( $row as $col_id => $column ) {
				// Make sure the column is valid
				if ( array_key_exists( $col_id, $cols ) ) {
					echo '"' . $column . '"';
					echo $i == count( $cols ) + 1 ? '' : ',';
				}

				$i ++;
			}
			echo "\r\n";
		}
	}

	/**
	 * Perform the export
	 *
	 * @access public
	 * @return void
	 * @since 1.0.2
	 */
	public function export() {
		if ( ! $this->can_export() ) {
			wp_die( __( 'You do not have permission to export data.', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
		}
		// Set headers
		$this->headers();

		// Output CSV columns (headers)
		$this->csv_cols_out();

		// Output CSV rows
		$this->csv_rows_out();

		/**
		 * Fires at the end of an export.
		 *
		 * The dynamic portion of the hook name, `$this->export_type`, refers to
		 * the export type set by the extending sub-class.
		 *
		 * @param CSV_Exporter $this CSV_Exporter instance.
		 *
		 *
		 * @since 1.0.2
		 */
		do_action( "eaccounting_export_{$this->export_type}_end", $this );
		exit;
	}
}
