<?php

namespace EverAccounting\Importer;

class Importer {

	/**
	 * The parsed CSV file being imported.
	 *
	 * @access public
	 * @since  2.1
	 * @var    \parseCSV
	 */
	public $csv;

	/**
	 * Total rows in the CSV file.
	 *
	 * @access public
	 * @since  2.1
	 * @var    int
	 */
	public $total;

	/**
	 * Map of CSV columns > database fields
	 *
	 * @access public
	 * @since  2.1
	 * @var    array
	 */
	public $field_mapping = array();

	/**
	 * Form data passed via Ajax.
	 *
	 * @access public
	 * @since  2.1
	 * @var    array
	 */
	public $data = array();

	/**
	 * CSV_Importer constructor.
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
	 * Maps CSV columns to their corresponding import fields.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param array $import_fields Import fields to map.
	 */
	public function map_fields( $import_fields = array() ) {
		$this->field_mapping = $import_fields;
	}

	/**
	 * Retrieves the CSV columns.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return array The columns in the CSV.
	 */
	public function get_columns() {
		return array_values( array_filter( $this->csv->titles ) );
	}

	/**
	 * Maps a single CSV row to the data passed in via init().
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param array $csv_row CSV row data.
	 * @return array CSV row data mapped to form-defined arguments.
	 */
	public function map_row( $csv_row ) {
		$mapped_row = array();

		foreach ( $this->data as $key => $field ) {
			if ( ! empty( $this->data[ $key ] ) && ! empty( $csv_row[ $this->data[ $key ] ] ) ) {
				$mapped_row[ $key ] = $csv_row[ $this->data[ $key ] ];
			}
		}

		return $mapped_row;
	}

	/**
	 * Retrieves the first row of the CSV.
	 *
	 * This is used for showing an example of what the import will look like.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return array The first row after the header of the CSV.
	 */
	public function get_first_row() {
		return array_map( array( $this, 'trim_preview' ), current( $this->csv->data ) );
	}


	/**
	 * Performs the import process.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return void
	 */
	public function import() {}

}
