<?php

namespace EverAccounting\Admin\Importers;

use EverAccounting\Utilities\FileUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Class Importer.
 *
 * @since 1.0.0
 *
 * @package EverAccounting\Admin\Tools\Importers
 */
abstract class Importer {
	/**
	 * Capability needed to perform the current import.
	 *
	 * @since  1.0.2
	 * @var    string
	 */
	protected $capability = 'manage_options';

	/**
	 * CSV file.
	 *
	 * @since  1.0.2
	 * @var string
	 */
	protected $file = '';

	/**
	 * Current position.
	 *
	 * @since  1.0.2
	 * @var int
	 */
	protected $position = 0;

	/**
	 * Delimiter.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	protected $delimiter = ',';

	/**
	 * The character used to wrap text in the CSV.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $enclosure = '"';

	/**
	 * The character used to escape the enclosure character.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $escape = "\0"; // This should be double-quoted.

	/**
	 * Start time of current import.
	 *
	 * (default value: 0)
	 *
	 * @since 1.0.2
	 * @var int
	 */
	protected $start_time = 0;

	/**
	 * Report.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $report = array(
		'errors'   => array(),
		'updated'  => 0,
		'imported' => 0,
		'skipped'  => 0,
		'failed'   => 0,
	);

	/**
	 * Abstract method to import item.
	 *
	 * @param array $data item data.
	 *
	 * @since 1.0.2
	 * @return mixed
	 */
	abstract public function import_item( $data );

	/**
	 * Set file.
	 *
	 * @param string $file File path.
	 * @since 1.0.2
	 */
	public function set_file( $file ) {
		$this->file = $file;
	}

	/**
	 * Set position.
	 *
	 * @param int $position Position.
	 * @since 1.0.2
	 */
	public function set_position( $position ) {
		$this->position = $position;
	}

	/**
	 * Get position.
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function get_position() {
		return $this->position;
	}

	/**
	 * Can user import?
	 *
	 * @since  1.0.2
	 * @return bool Whether the user can import or not
	 */
	public function can_import() {
		return (bool) current_user_can( apply_filters( 'eac_import_capability', $this->capability ) );
	}

	/**
	 * Check file type.
	 *
	 * @since 1.0.2
	 * @return bool Whether the file is valid or not
	 */
	public function check_filetype() {
		$filetypes = apply_filters(
			'eac_import_csv_filetypes',
			array(
				'csv' => 'text/csv',
				'txt' => 'text/plain',
			)
		);

		$filetype = wp_check_filetype( $this->file, $filetypes );

		return in_array( $filetype['type'], $filetype, true );
	}

	/**
	 * Get file pointer position as a percentage of file size.
	 *
	 * @return int
	 */
	public function get_percent_complete() {
		$size = filesize( $this->file );
		if ( ! $size || ! $this->position ) {
			return 0;
		}

		$percent = absint( min( floor( ( $this->position / $size ) * 100 ), 100 ) );
		if ( 100 === $percent ) {
			$wp_filesystem = FileUtil::get_filesystem();
			$wp_filesystem->delete( $this->file );
		}

		return $percent;
	}

	/**
	 * Process the import.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function import() {
		$handle = fopen( $this->file, 'r' ); // @codingStandardsIgnoreLine.
		if ( false === $handle ) {
			return $this->report;
		}

		$this->start_time = time();
		$rows             = array();
		$headers          = array_map( 'trim', fgetcsv( $handle, 0, $this->delimiter, $this->enclosure, $this->escape ) );
		// Remove BOM signature from the first item.
		if ( isset( $headers[0] ) ) {
			if ( 'efbbbf' === substr( bin2hex( $headers[0] ), 0, 6 ) ) {
				$headers[0] = str_replace( $this->enclosure, '', substr( $headers[0], 3 ) );
			}
		}

		if ( 0 !== $this->position ) {
			fseek( $handle, $this->position );
		}

		// count the number of rows in the file.

		// Prepare the rows.
		while ( ! feof( $handle ) ) {
			$row = fgetcsv( $handle, 0, $this->delimiter, $this->enclosure, $this->escape );
			if ( false !== $row && count( array_filter( $row ) ) ) {
				$rows[] = $this->parse_row( $headers, $row );
			}
		}
		$this->position = ftell( $handle );

		// close the file.
		fclose( $handle ); // @codingStandardsIgnoreLine.

		$counter = 0;
		foreach ( $rows as $row ) {
			$ret_val = $this->import_item( $row );
			if ( is_wp_error( $ret_val ) ) {
				if ( 'missing_required' === $ret_val->get_error_code() ) {
					$this->report['failed'] += 1;
				} elseif ( 'duplicate' === $ret_val->get_error_code() ) {
					$this->report['skipped'] += 1;
				} else {
					$this->report['errors'][] = $ret_val;
				}
			} else {
				$this->report['imported'] += 1;
			}
			++$counter;
			if ( $this->time_exceeded() || $this->memory_exceeded() ) {
				$this->file_position = $counter;
				break;
			}
		}

		return $this->report;
	}

	/**
	 * Parse a single row.
	 *
	 * @param array $headers Headers.
	 * @param array $row Row.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	protected function parse_row( $headers, $row ) {
		foreach ( $row as &$value ) {
			// Convert UTF8.
			if ( function_exists( 'mb_convert_encoding' ) ) {
				$encoding = mb_detect_encoding( $value, mb_detect_order(), true );
				if ( $encoding ) {
					$value = mb_convert_encoding( $value, 'UTF-8', $encoding );
				} else {
					$value = mb_convert_encoding( $value, 'UTF-8', 'UTF-8' );
				}
			} else {
				$value = wp_check_invalid_utf8( $value, true );
			}

			if ( in_array( mb_substr( $value, 0, 2 ), array( "'=", "'+", "'-", "'@" ), true ) ) {
				$value = mb_substr( $value, 1 );
			}
		}

		// make the headers and rows same length. then combine them.
		$row = array_pad( $row, count( $headers ), '' );

		return array_combine( $headers, $row );
	}

	/**
	 * Time exceeded.
	 *
	 * Ensures the batch never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	protected function time_exceeded() {
		$finish = $this->start_time + 20; // 20 seconds
		$return = false;
		if ( time() >= $finish ) {
			$return = true;
		}

		return $return;
	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the batch process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	protected function memory_exceeded() {
		$memory_limit   = $this->get_memory_limit() * 0.9; // 90% of max memory
		$current_memory = memory_get_usage( true );
		$return         = false;
		if ( $current_memory >= $memory_limit ) {
			$return = true;
		}

		return $return;
	}

	/**
	 * Get memory limit
	 *
	 * @return int
	 */
	protected function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || - 1 === intval( $memory_limit ) ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}

		return intval( $memory_limit ) * 1024 * 1024;
	}
}
