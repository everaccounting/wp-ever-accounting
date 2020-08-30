<?php

namespace EverAccounting\Import;

class Importer {

	/**
	 * The file being imported
	 *
	 * @since 2.6
	 */
	public $file;

	/**
	 * The parsed CSV file being imported
	 *
	 * @since 2.6
	 */
	public $csv;

	/**
	 * Total rows in the CSV file
	 *
	 * @since 2.6
	 */
	public $total;

	/**
	 * The current step being processed
	 *
	 * @since 2.6
	 */
	public $step;

	/**
	 * The number of items to process per step
	 *
	 * @since 2.6
	 */
	public $per_step = 20;

	/**
	 * The capability required to import data
	 *
	 * @since 2.6
	 */
	public $capability_type = 'manage_options';

	/**
	 * Is the import file empty
	 *
	 * @since 2.6
	 */
	public $is_empty = false;

	/**
	 * Map of CSV columns > database fields
	 *
	 * @since 2.6
	 */
	public $field_mapping = array();

	/**
	 * Importer constructor.
	 */
	public function __construct( $file, $step = 1 ) {
		if ( ! class_exists( 'parseCSV' ) ) {
			require_once EACCOUNTING_ABSPATH . '/includes/libraries/parsecsv.php';
		}

		$this->step = $step;
		$this->file = $file;
		$this->done = false;
		$this->csv  = new \parseCSV();
		$this->csv->auto( $this->file );

		$this->total = count( $this->csv->data );
	}


	/**
	 * Initialize the updater. Runs after import file is loaded but before any processing is done.
	 *
	 * @since 2.6
	 * @return void
	 */
	public function init() {
	}


	/**
	 * Can we import?
	 *
	 * @since 2.6
	 * @return bool Whether we can import or not
	 */
	public function can_import() {
		return (bool) apply_filters( 'eaccounting_import_capability', current_user_can( $this->capability_type ) );
	}

	/**
	 * Get the CSV columns
	 *
	 * @since 2.6
	 * @return array The columns in the CSV
	 */
	public function get_columns() {

		return $this->csv->titles;
	}

	/**
	 * Get the first row of the CSV
	 *
	 * This is used for showing an example of what the import will look like
	 *
	 * @since 2.6
	 * @return array The first row after the header of the CSV
	 */
	public function get_first_row() {

		return array_map( array( $this, 'trim_preview' ), current( $this->csv->data ) );

	}

	/**
	 * Process a step
	 *
	 * @since 2.6
	 * @return bool
	 */
	public function process_step() {

		$more = false;

		if ( ! $this->can_import() ) {
			wp_die( __( 'You do not have permission to import data.', 'easy-digital-downloads' ), __( 'Error', 'easy-digital-downloads' ), array( 'response' => 403 ) );
		}

		return $more;
	}

	/**
	 * Return the calculated completion percentage
	 *
	 * @since 2.6
	 * @return int
	 */
	public function get_percentage_complete() {
		return 100;
	}

	/**
	 * Map CSV columns to import fields
	 *
	 * @since 2.6
	 * @return void
	 */
	public function map_fields( $import_fields = array() ) {

		// Probably add some sanitization here later

		$this->field_mapping = $import_fields;
	}

	/**
	 * Convert a string containing delimiters to an array
	 *
	 * @since 2.6
	 *
	 * @param string $str Input string to convert to an array
	 *
	 * @return array
	 */
	public function str_to_array( $str = '' ) {

		$array = array();

		if ( is_array( $str ) ) {
			return array_map( 'trim', $str );
		}

		// Look for standard delimiters
		if ( false !== strpos( $str, '|' ) ) {

			$delimiter = '|';

		} elseif ( false !== strpos( $str, ',' ) ) {

			$delimiter = ',';

		} elseif ( false !== strpos( $str, ';' ) ) {

			$delimiter = ';';

		} elseif ( false !== strpos( $str, '/' ) && ! filter_var( str_replace( ' ', '%20', $str ), FILTER_VALIDATE_URL ) && '/' !== substr( $str, 0, 1 ) ) {

			$delimiter = '/';

		}

		if ( ! empty( $delimiter ) ) {

			$array = (array) explode( $delimiter, $str );

		} else {

			$array[] = $str;
		}

		return array_map( 'trim', $array );

	}

	/**
	 * Convert a files string containing delimiters to an array.
	 *
	 * This is identical to str_to_array() except it ignores all / characters.
	 *
	 * @since 2.9.20
	 *
	 * @param string $str Input string to convert to an array
	 *
	 * @return array
	 */
	public function convert_file_string_to_array( $str = '' ) {

		$array = array();

		if ( is_array( $str ) ) {
			return array_map( 'trim', $str );
		}

		// Look for standard delimiters
		if ( false !== strpos( $str, '|' ) ) {

			$delimiter = '|';

		} elseif ( false !== strpos( $str, ',' ) ) {

			$delimiter = ',';

		} elseif ( false !== strpos( $str, ';' ) ) {

			$delimiter = ';';

		}

		if ( ! empty( $delimiter ) ) {

			$array = (array) explode( $delimiter, $str );

		} else {

			$array[] = $str;
		}

		return array_map( 'trim', $array );

	}

	/**
	 * Trims a column value for preview
	 *
	 * @since 2.6
	 *
	 * @param string $str input string to trim down
	 *
	 * @return string
	 */
	public function trim_preview( $str = '' ) {

		if ( ! is_numeric( $str ) ) {

			$long = strlen( $str ) >= 30;
			$str  = substr( $str, 0, 30 );
			$str  = $long ? $str . '...' : $str;

		}

		return $str;

	}
}
