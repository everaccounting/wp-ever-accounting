<?php

namespace EverAccounting\Import;

class CSV_Import {
	/**
	 * Import type.
	 *
	 * Used for import-type specific filters/actions.
	 *
	 * @access public
	 * @since  2.1
	 * @var    string
	 */
	public $import_type = 'default';

	/**
	 * Capability needed to perform the current import.
	 *
	 * @since  2.1
	 * @var    string
	 */
	public $capability = 'manage_options';

	/**
	 * The file being imported.
	 *
	 * @since  2.1
	 * @var    resource
	 */
	public $file;

	/**
	 * Whether the import file is empty.
	 *
	 * @access public
	 * @since  2.1
	 * @var    bool
	 */
	public $is_empty = false;

	/**
	 * Instantiates the importer.
	 *
	 * @access public
	 *
	 * @param resource $_file File to import.
	 * @param int $_step Current step.
	 *
	 * @since  2.1
	 *
	 */
	public function __construct( $_file = '', $_step = 1 ) {
		$this->step = $_step;
		$this->file = $_file;
	}

	/**
	 * Determines whether the current user can perform an import.
	 *
	 * @access public
	 * @return bool Whether the current use can import.
	 * @since  2.1
	 *
	 */
	public function can_import() {
		/**
		 * Filters the capability needed to perform an import.
		 *
		 * @param string $capability Capability needed to perform an export.
		 *
		 * @since 2.0
		 *
		 */
		return (bool) current_user_can( apply_filters( 'manage_options', $this->capability ) );
	}

	/**
	 * Converts a string containing delimiters to an array.
	 *
	 * @access public
	 *
	 * @param string $str Optional. Input string to convert to an array. Default empty.
	 *
	 * @return array Derived array.
	 * @since  2.1
	 *
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

		} elseif ( false !== strpos( $str, '/' ) && ! filter_var( $str, FILTER_VALIDATE_URL ) ) {

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
	 * Trims a column value for preview.
	 *
	 * @access public
	 *
	 * @param string $str Optional. Input string to trim down. Default empty.
	 *
	 * @return string String trimmed for preview.
	 * @since  2.1
	 *
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
