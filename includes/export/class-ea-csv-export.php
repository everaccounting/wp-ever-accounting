<?php
/**
 * CSV Exporter Class
 *
 * @package     EverAccounting
 * @subpackage  Export
 * @since       1.0.2
 */

namespace EverAccounting\Import;
defined( 'ABSPATH' ) || exit();

class CSV_Export {
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
		return (bool) current_user_can( apply_filters( 'manage_options', $this->capability ) );
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

		if ( ! affwp_is_func_disabled( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=eaccounting-export-' . $this->export_type . '-' . date( 'm-d-Y' ) . '.csv' );
		header( "Expires: 0" );
	}

	/**
	 * Set the CSV columns
	 *
	 * @access public
	 * @return array $cols All the columns
	 * @since 1.0.2
	 */
	public function csv_cols() {
		$cols = array(
			'id'   => __( 'ID', 'wp-ever-accounting' ),
			'date' => __( 'Date', 'wp-ever-accounting' )
		);

		return $cols;
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
		 *     `affwp_export_csv_cols_affiliates`
		 *
		 * @param $cols The export columns available.
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'affwp_export_csv_cols_' . $this->export_type, $cols );
	}

	/**
	 * Output the CSV columns
	 *
	 * @access public
	 * @return void
	 * @uses Affiliate_WP_Export::get_csv_cols()
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
	 * Retrieves the data being exported.
	 *
	 * @access public
	 * @return array $data Data for Export
	 * @since  1.0.2
	 *
	 */
	public function get_data() {
		// Just a sample data array
		$data = array(
			0 => array(
				'id'   => '',
				'data' => date( 'F j, Y' )
			),
			1 => array(
				'id'   => '',
				'data' => date( 'F j, Y' )
			)
		);

		return $data;
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
		$data = apply_filters( 'affwp_export_get_data', $data );

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
		$data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );

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
	 * @uses Affiliate_WP_Export::can_export()
	 * @uses Affiliate_WP_Export::headers()
	 * @uses Affiliate_WP_Export::csv_cols_out()
	 * @uses Affiliate_WP_Export::csv_rows_out()
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
		 * @param Affiliate_WP_Export $this Affiliate_WP_Export instance.
		 *
		 * @since 1.9.2 Renamed to 'affwp_export_type_end' to prevent a conflict with another
		 *              dynamic hook.
		 *
		 * @since 1.9
		 */
		do_action( "affwp_export_{$this->export_type}_end", $this );
		exit;
	}
}
