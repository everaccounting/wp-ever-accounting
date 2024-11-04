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
	 * @since  1.0.0
	 * @var    string
	 */
	protected $capability = 'manage_options';

	/**
	 * CSV file.
	 *
	 * @since  1.0.0
	 * @var string
	 */
	protected $file = '';

	/**
	 * CSV data being imported.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array();

	/**
	 * Current position.
	 *
	 * @since  1.0.0
	 * @var int
	 */
	protected $position = 0;

	/**
	 * Start time of current import.
	 *
	 * (default value: 0)
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $start_time = 0;

	/**
	 * Total items imported.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $imported = 0;

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
	 *
	 * @since 1.0.2
	 */
	public function set_file( $file ) {
		$filetypes = apply_filters(
			'eac_import_csv_filetypes',
			array(
				'csv' => 'text/csv',
				'txt' => 'text/plain',
			)
		);

		$filetype = wp_check_filetype( $file, $filetypes );

		if ( in_array( $filetype['type'], $filetype, true ) ) {
			$this->file = $file;
			$data       = array_map( 'str_getcsv', FileUtil::file( $this->file ) );
			array_walk(
				$data,
				function ( &$a ) use ( $data ) {
					// Make sure the two arrays have the same lengths.
					$min     = min( count( $data[0] ), count( $a ) );
					$headers = array_slice( $data[0], 0, $min );
					$values  = array_slice( $a, 0, $min );
					$a       = array_combine( $headers, $values );
				}
			);
			array_shift( $data );
			$this->data = $data;
		}
	}

	/**
	 * Set position.
	 *
	 * @param int $position Position.
	 *
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
	 * Process the import.
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function import() {
		$this->start_time = time();
		$data             = $this->data;
		if ( 0 !== $this->position && $this->position < count( $data ) ) {
			$data = array_slice( $data, $this->position );
		}

		foreach ( $data as $item ) {
			if ( ! is_wp_error( $this->import_item( $item ) ) ) {
				++$this->imported;
			}
			++$this->position;
			if ( $this->time_exceeded() || $this->memory_exceeded() ) {
				break;
			}
		}

		return $this->imported;
	}

	/**
	 * Get file pointer position as a percentage of file size.
	 *
	 * @return int
	 */
	public function get_percent_complete() {
		$count = count( $this->data );
		if ( ! $count || ! $this->position ) {
			return 0;
		}

		$percent = absint( min( floor( ( $this->position / $count ) * 100 ), 100 ) );
		if ( 100 === $percent ) {
			FileUtil::delete( $this->file );
		}

		return $percent;
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
