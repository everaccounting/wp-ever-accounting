<?php

namespace EverAccounting\Abstracts;

/**
 * Class CSV_Batch_Exporter
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */
abstract class CSV_Batch_Exporter extends CSV_Exporter {
	/**
	 * Page being exported
	 *
	 * @var integer
	 */
	protected $page = 1;

	/**
	 * Batch limit.
	 *
	 * @since 1.0.2
	 * @var integer
	 */
	protected $limit = 1;

	/**
	 * Total rows to export.
	 *
	 * @since 1.0.2
	 * @var integer
	 */
	protected $total_count = 0;

	/**
	 * Set page.
	 *
	 * @since 1.0.2
	 *
	 * @param int $page Page Nr.
	 */
	public function set_page( $page ) {
		$this->page = absint( $page );
	}

	/**
	 * Set batch limit.
	 *
	 * @since 3.1.0
	 *
	 * @param int $limit Limit to export.
	 */
	public function set_limit( $limit ) {
		$this->limit = absint( $limit );
	}

	/**
	 * Get file path to export to.
	 *
	 * @return string
	 */
	protected function get_file_path() {
		$upload_dir = wp_upload_dir();

		return trailingslashit( $upload_dir['basedir'] ) . $this->get_filename();
	}

	/**
	 * Get batch limit.
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function get_limit() {
		return apply_filters( "eaccounting_{$this->export_type}_export_batch_limit", $this->limit, $this );
	}

	/**
	 * Get the file contents.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_file() {
		$file = '';
		if ( @file_exists( $this->get_file_path() ) ) {
			$file = @file_get_contents( $this->get_file_path() );
		} else {
			@file_put_contents( $this->get_file_path(), '' );
			@chmod( $this->get_file_path(), 0664 );
		}

		return $file;
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
	 * Get count of records exported.
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function get_total_exported() {
		return ( ( $this->get_page() - 1 ) * $this->get_limit() ) + $this->exported_count;
	}

	/**
	 * Get total % complete.
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function get_percent_complete() {
		return $this->total_count ? floor( ( $this->get_total_exported() / $this->total_count ) * 100 ) : 100;
	}

	/**
	 * Generate the CSV file.
	 *
	 * @since 1.0.2
	 *
	 * @param int $step
	 */
	public function process_step( $step ) {
		$this->set_page( $step );
		if ( 1 === $this->get_page() ) {
			@unlink( $this->get_file_path() );
		}
		$this->set_data();
		$this->write_csv_data( $this->export_rows() );
	}

	/**
	 * Write data to the file.
	 *
	 * @since 1.0.2
	 *
	 * @param string $data Data.
	 */
	protected function write_csv_data( $data ) {
		$file = $this->get_file();
		// Add columns when finished.
		if ( 100 == $this->get_percent_complete() ) {
			$file = chr( 239 ) . chr( 187 ) . chr( 191 ) . $this->export_column_headers() . $file;
		}

		$file .= $data;
		@file_put_contents( $this->get_file_path(), $file );
	}


	/**
	 * Serve the file and remove once sent to the client.
	 *
	 * @since 1.0.2
	 */
	public function export() {
		$this->send_headers();
		$this->send_content( $this->get_file() );
//		@unlink( $this->get_file_path() );
		die();
	}
}
