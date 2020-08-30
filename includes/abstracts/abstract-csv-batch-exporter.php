<?php
/**
 * Overview of CSV Batch Exporter Main Class.
 * Provide Base Structure for CSV Batch Importer
 *
 * @since       1.0.2
 * @subpackage  Abstracts
 * @package     EverAccounting
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit();

abstract class CSV_Batch_Exporter extends CSV_Exporter {
	/**
	 * Page being exported
	 *
	 * @since 1.0.2
	 * @var integer
	 */
	protected $page = 1;

	/**
	 * Columns ids and names.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $column_names = array();

	/**
	 * List of columns to export, or empty for all.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $columns_to_export = array();


	/**
	 * Type of export used in filter names.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	protected $export_type = '';

	/**
	 * Filename to export to.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	protected $filename = 'wc-export.csv';

	/**
	 * Batch limit.
	 *
	 * @since 1.0.2
	 * @var integer
	 */
	protected $limit = 50;

	/**
	 * Number exported.
	 *
	 * @since 1.0.2
	 * @var integer
	 */
	protected $exported_row_count = 0;

	/**
	 * Raw data to export.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $row_data = array();

	/**
	 * Total rows to export.
	 *
	 * @since 1.0.2
	 * @var integer
	 */
	protected $total_rows = 0;

	/**
	 * The delimiter parameter sets the field delimiter (one character only).
	 *
	 * @since 1.0.2
	 * @var string
	 */
	protected $delimiter = ',';

	/**
	 * Set column names.
	 *
	 * @since 1.0.2
	 *
	 * @param array $column_names Column names array.
	 *
	 */
	public function set_column_names( $column_names ) {
		$this->column_names = array();

		foreach ( $column_names as $column_id => $column_name ) {
			$this->column_names[ eaccounting_clean( $column_id ) ] = eaccounting_clean( $column_name );
		}
	}

	/**
	 * Set columns to export.
	 *
	 * @since 1.0.2
	 *
	 * @param array $columns Columns array.
	 *
	 */
	public function set_columns_to_export( $columns ) {
		$this->columns_to_export = array_map( 'eaccounting_clean', $columns );
	}

	/**
	 * Get file path to export to.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	protected function get_file_path() {
		$upload_dir = wp_upload_dir();

		return trailingslashit( $upload_dir['basedir'] ) . $this->get_filename();
	}

	/**
	 * Get the file contents.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_file() {
		$file = '';
		if ( @file_exists( $this->get_file_path() ) ) { // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			$file = @file_get_contents( $this->get_file_path() ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents, WordPress.WP.AlternativeFunctions.file_system_read_file_get_contents
		} else {
			@file_put_contents( $this->get_file_path(), '' ); // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_file_put_contents, Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
			@chmod( $this->get_file_path(), 0664 ); // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.chmod_chmod, WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents, Generic.PHP.NoSilencedErrors.Discouraged
		}

		return $file;
	}

	/**
	 * Serve the file and remove once sent to the client.
	 *
	 * @since 1.0.2
	 */
	public function export() {
		$this->send_headers();
		$this->send_content( $this->get_file() );
		@unlink( $this->get_file_path() ); // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_unlink, Generic.PHP.NoSilencedErrors.Discouraged
		die();
	}

	/**
	 * Generate the CSV file.
	 *
	 * @since 1.0.2
	 */
	public function generate_file() {
		if ( 1 === $this->get_page() ) {
			@unlink( $this->get_file_path() ); // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_unlink, Generic.PHP.NoSilencedErrors.Discouraged,
		}
		$this->prepare_data_to_export();
		$this->write_csv_data( $this->get_csv_data() );
	}

	/**
	 * Write data to the file.
	 *
	 * @since 1.0.2
	 *
	 * @param string $data Data.
	 *
	 */
	protected function write_csv_data( $data ) {
		$file = $this->get_file();

		// Add columns when finished.
		if ( 100 === $this->get_percent_complete() ) {
			$file = chr( 239 ) . chr( 187 ) . chr( 191 ) . $this->export_column_headers() . $file;
		}

		$file .= $data;
		@file_put_contents( $this->get_file_path(), $file ); // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_file_put_contents, Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
	}


	/**
	 * Get batch limit.
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function get_limit() {
		return apply_filters( "woocommerce_{$this->export_type}_export_batch_limit", $this->limit, $this );
	}

	/**
	 * Set batch limit.
	 *
	 * @since 1.0.2
	 *
	 * @param int $limit Limit to export.
	 *
	 */
	public function set_limit( $limit ) {
		$this->limit = absint( $limit );
	}

	/**
	 * Get page.
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function get_page() {
		return $this->page;
	}

	/**
	 * Set page.
	 *
	 * @since 1.0.2
	 *
	 * @param int $page Page Nr.
	 *
	 */
	public function set_page( $page ) {
		$this->page = absint( $page );
	}

	/**
	 * Get count of records exported.
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function get_total_exported() {
		return ( ( $this->get_page() - 1 ) * $this->get_limit() ) + $this->exported_row_count;
	}

	/**
	 * Get total % complete.
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function get_percent_complete() {
		return $this->total_rows ? floor( ( $this->get_total_exported() / $this->total_rows ) * 100 ) : 100;
	}

	/**
	 * Generate and return a filename.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_filename() {
		return sanitize_file_name( apply_filters( "woocommerce_{$this->export_type}_export_get_filename", $this->filename ) );
	}


}
