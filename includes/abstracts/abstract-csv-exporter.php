<?php
/**
 * Overview of CSV Exporter Main Class.
 *
 * @since       1.0.2
 * @subpackage  Abstracts
 * @package     EverAccounting
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit();

abstract class CSV_Exporter {
	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'default';

	/**
	 * Capability needed to perform the current export.
	 *
	 * @since  1.0.2
	 * @var    string
	 */
	public $capability = 'manage_options';

	/**
	 * Raw data to export.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $rows = array();

	/**
	 * Number exported.
	 *
	 * @since 1.0.2
	 * @var integer
	 */
	protected $exported_count = 0;

	/**
	 * List of columns to export, or empty for all.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $columns_to_export = array();

	/**
	 * The delimiter parameter sets the field delimiter (one character only).
	 *
	 * @since 1.0.2
	 * @var string
	 */
	protected $delimiter = ',';

	/**
	 * Set columns available columns to export.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public abstract function get_csv_columns();

	/**
	 * Prepare data that will be exported.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public abstract function set_data();

	/**
	 * Set filename to export to.
	 *
	 * @param string $filename Filename to export to.
	 */
	public function set_filename( $filename ) {
		$this->filename = sanitize_file_name( str_replace( '.csv', '', $filename ) . '.csv' );
	}

	/**
	 * Set columns to export.
	 *
	 * @since 1.0.2
	 *
	 * @param array $columns Columns array.
	 */
	public function set_columns_to_export( $columns ) {
		if ( ! empty( $columns ) && is_array( $columns ) ) {
			$this->columns_to_export = array_map( 'eaccounting_clean', $columns );
		}
	}

	/**
	 * Return an array of supported column names and ids.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_columns() {
		return apply_filters( "eaccounting_{$this->export_type}_export_column_names", $this->get_csv_columns(), $this );
	}

	/**
	 * Generate and return a filename.
	 *
	 * @return string
	 */
	public function get_filename() {
		$date      = date( "Ymd" );
		$file_name = empty( $this->filename ) ? "{$this->export_type}-$date.csv" : $this->filename;

		return sanitize_file_name( apply_filters( "eaccounting_{$this->export_type}_export_get_filename", $file_name ) );
	}

	/**
	 * Return an array of columns to export.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_columns_to_export() {
		return $this->columns_to_export;
	}

	/**
	 * Export column headers in CSV format.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	protected function export_column_headers() {
		$columns    = $this->get_columns();
		$export_row = array();
		$buffer     = fopen( 'php://output', 'w' );
		ob_start();

		foreach ( $columns as $column_id => $column_name ) {
			if ( ! $this->is_column_exporting( $column_id ) ) {
				continue;
			}
			$export_row[] = $this->format_data( $column_name );
		}

		$this->fputcsv( $buffer, $export_row );

		return ob_get_clean();
	}

	/**
	 * Export rows in CSV format.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	protected function export_rows() {
		$data   = $this->rows;
		$buffer = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
		ob_start();

		array_walk( $data, array( $this, 'export_row' ), $buffer );

		return apply_filters( "eaccounting_{$this->export_type}_export_rows", ob_get_clean(), $this );
	}

	/**
	 * Export rows to an array ready for the CSV.
	 *
	 * @since 1.0.2
	 *
	 * @param array    $row_data Data to export.
	 * @param string   $key      Column being exported.
	 * @param resource $buffer   Output buffer.
	 */
	protected function export_row( $row_data, $key, $buffer ) {
		$columns    = $this->get_columns();
		$export_row = array();

		foreach ( $columns as $column_id => $column_name ) {
			if ( ! $this->is_column_exporting( $column_id ) ) {
				continue;
			}
			if ( isset( $row_data[ $column_id ] ) ) {
				$export_row[] = $this->format_data( $row_data[ $column_id ] );
			} else {
				$export_row[] = '';
			}
		}

		$this->fputcsv( $buffer, $export_row );

		++ $this->exported_count;
	}

	/**
	 * Format and escape data ready for the CSV file.
	 *
	 * @since 1.0.2
	 *
	 * @param string $data Data to format.
	 *
	 * @return string
	 */
	public function format_data( $data ) {
		if ( ! is_scalar( $data ) ) {
			if ( is_a( $data, '\EverAccounting\DateTime' ) ) {
				$data = $data->date( 'Y-m-d G:i:s' );
			} else {
				$data = ''; // Not supported.
			}
		} elseif ( is_bool( $data ) ) {
			$data = $data ? 1 : 0;
		}

		$use_mb = function_exists( 'mb_convert_encoding' );

		if ( $use_mb ) {
			$encoding = mb_detect_encoding( $data, 'UTF-8, ISO-8859-1', true );
			$data     = 'UTF-8' === $encoding ? $data : utf8_encode( $data );
		}

		return $this->escape_data( $data );
	}

	/**
	 * Escape a string to be used in a CSV context
	 *
	 *
	 * @since 1.0.2
	 *
	 * @param string $data CSV field to escape.
	 *
	 * @return string
	 */
	public function escape_data( $data ) {
		$active_content_triggers = array( '=', '+', '-', '@' );

		if ( in_array( mb_substr( $data, 0, 1 ), $active_content_triggers, true ) ) {
			$data = "'" . $data;
		}

		return $data;
	}


	/**
	 * See if a column is to be exported or not.
	 *
	 * @since 1.0.2
	 *
	 * @param string $column_id ID of the column being exported.
	 *
	 * @return boolean
	 */
	public function is_column_exporting( $column_id ) {
		$column_id         = strstr( $column_id, ':' ) ? current( explode( ':', $column_id ) ) : $column_id;
		$columns_to_export = $this->get_columns_to_export();

		if ( empty( $columns_to_export ) ) {
			return true;
		}

		if ( in_array( $column_id, $columns_to_export, true ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Can we export?
	 *
	 * @since  1.0.2
	 * @return bool Whether we can export or not
	 */
	public function can_export() {
		/**
		 * Filters the capability needed to perform an export.
		 *
		 * @since 1.0.2
		 *
		 * @param string $capability Capability needed to perform an export.
		 *
		 */
		return (bool) current_user_can( apply_filters( 'eaccounting_export_capability', $this->capability ) );
	}

	/**
	 * Set the export headers.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function send_headers() {
		@ini_set( 'zlib.output_compression', 'Off' );
		@ini_set( 'output_buffering', 'Off' );
		@ini_set( 'output_handler', '' );
		ignore_user_abort( true );
		if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
			@set_time_limit( 0 );
		}

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $this->get_filename() );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
	}

	/**
	 * Set the export content.
	 *
	 * @since 1.0.2
	 *
	 * @param string $csv_data All CSV content.
	 */
	public function send_content( $csv_data ) {
		echo $csv_data;
	}

	/**
	 * Do the export.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function export() {
		if ( ! $this->can_export() ) {
			wp_die( __( 'You do not have permission to export data.', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
		}

		// Set headers
		$this->send_headers();

		// Output contents
		$this->send_content( chr( 239 ) . chr( 187 ) . chr( 191 ) . $this->export_column_headers() . $this->export_rows() );
		die();
	}

	/**
	 * Write to the CSV file, ensuring escaping works across versions of
	 * PHP.
	 *
	 * PHP 5.5.4 uses '\' as the default escape character. This is not RFC-4180 compliant.
	 * \0 disables the escape character.
	 *
	 * @since 1.0.2
	 *
	 * @param resource $buffer     Resource we are writing to.
	 * @param array    $export_row Row to export.
	 */
	protected function fputcsv( $buffer, $export_row ) {

		if ( version_compare( PHP_VERSION, '5.5.4', '<' ) ) {
			ob_start();
			$temp = fopen( 'php://output', 'w' ); // @codingStandardsIgnoreLine
			fputcsv( $temp, $export_row, $this->delimiter, '"' ); // @codingStandardsIgnoreLine
			fclose( $temp ); // @codingStandardsIgnoreLine
			$row = ob_get_clean();
			$row = str_replace( '\\"', '\\""', $row );
			fwrite( $buffer, $row ); // @codingStandardsIgnoreLine
		} else {
			fputcsv( $buffer, $export_row, $this->delimiter, '"', "\0" ); // @codingStandardsIgnoreLine
		}
	}
}