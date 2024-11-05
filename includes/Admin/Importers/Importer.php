<?php

namespace EverAccounting\Admin\Importers;

use EverAccounting\ParseCsv\Csv;
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
	 * @since  1.0.0
	 * @var    string
	 */
	protected $capability = 'manage_options';

	/**
	 * The file being imported.
	 *
	 * @since  1.0.0
	 * @var string
	 */
	protected $file = '';

	/**
	 * The parsed CSV data being imported.
	 *
	 * @since  1.0.0
	 * @var   array
	 */
	protected $rows = array();

	/**
	 * Current step.
	 *
	 * @since  1.0.0
	 * @var int
	 */
	protected $step = 0;

	/**
	 * The number of items to process per step.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $per_step = 10;

	/**
	 * Start time of current import.
	 * (default value: 0)
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $start_time = 0;

	/**
	 * Constructor.
	 *
	 * @param string $file File path.
	 * @param int    $step Step number.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $file = '', $step = 1 ) {
		$this->file = $file;
		$this->step = $step;
		if ( ! empty( $this->file ) && file_exists( $this->file ) ) {
			$this->rows = $this->parse_csv( $this->file );
		}
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
	 * Process the import.
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function import() {
		if ( ! $this->can_import() ) {
			return 0;
		}

		$this->start_time = time();
		$offset           = ( $this->step - 1 ) * $this->per_step;
		$rows             = $this->rows;

		if ( ! empty( $offset ) && $offset < count( $rows ) ) {
			$rows = array_slice( $rows, $offset, $this->per_step, true );
		}

		$imported = 0;
		foreach ( $rows as $row ) {
			if ( ! is_wp_error( $this->import_item( $row ) ) ) {
				++$imported;
			}
		}

		return $imported;
	}

	/**
	 * Get the progress.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	public function get_percent_complete() {
		$count = count( $this->data );
		if ( ! $count || ! $this->step ) {
			return 0;
		}

		$percent = absint( ( $this->step / $count ) * 100 );
		if ( $percent >= 100 ) {
			FileUtil::delete( $this->file );
		}

		return $percent;
	}

	/**
	 * Abstract method to import item.
	 *
	 * @param array $data item data.
	 *
	 * @since 1.0.2
	 * @return mixed
	 */
	abstract protected function import_item( $data );

	/**
	 * Parse the CSV file.
	 *
	 * @param string $file File path.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function parse_csv( $file ) {
		if ( ! file_exists( $file ) ) {
			return array();
		}

		$rows = array_map( 'str_getcsv', FileUtil::file( $file ) );
		array_walk(
			$rows,
			function ( &$a ) use ( $rows ) {
				// Make sure the two arrays have the same lengths.
				$min     = min( count( $rows[0] ), count( $a ) );
				$headers = array_slice( $rows[0], 0, $min );
				$values  = array_slice( $a, 0, $min );
				$a       = array_combine( $headers, $values );
			}
		);
		array_shift( $rows );

		// Parse row.
		foreach ( $rows as &$row ) {
			$this->parse_row( $row );
		}

		return $rows;
	}

	/**
	 * Parse row.
	 *
	 * @param array $row Row data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function parse_row( &$row ) {
		$row = array_map( 'wp_unslash', $row );
		$row = array_map( 'trim', $row );
		foreach ( $row as &$value ) {
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

		return $row;
	}
}
