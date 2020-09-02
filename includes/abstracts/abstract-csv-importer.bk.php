<?php
/**
 * CSV Importer Main Class.
 *
 * @since       1.0.2
 * @subpackage  Abstracts
 * @package     EverAccounting
 */

namespace EverAccounting\Abstracts;

abstract class CSV_Importer {
	/**
	 * Capability needed to perform the current import.
	 *
	 * @access public
	 * @since  1.0.2
	 * @var    string
	 */
	public $capability = 'manage_options';

	/**
	 * CSV file.
	 *
	 * @var string
	 */
	protected $file = '';

	/**
	 * Importer parameters.
	 *
	 * @var array
	 */
	protected $params = array();

	/**
	 * Can user import?
	 *
	 * @since  1.0.2
	 * @return bool Whether the user can import or not
	 */
	public function can_import() {
		/**
		 * Filters the capability needed to perform an import.
		 *
		 * @since 1.0.2
		 *
		 * @param string $capability Capability needed to perform an import.
		 *
		 */
		return (bool) current_user_can( apply_filters( 'eaccounting_import_capability', $this->capability ) );
	}


	public function import( $file, $args = array() ) {
		$default_args = array(
			'start_pos'        => 0, // File pointer start.
			'end_pos'          => - 1, // File pointer end.
			'lines'            => - 1, // Max lines to read.
			'mapping'          => array(), // Column mapping. csv_heading => schema_heading.
			'parse'            => false, // Whether to sanitize and format data.
			'update_existing'  => false, // Whether to update existing items.
			'delimiter'        => ',', // CSV delimiter.
			'prevent_timeouts' => true, // Check memory and time usage and abort if reaching limit.
			'enclosure'        => '"', // The character used to wrap text in the CSV.
			'escape'           => "\0", // PHP uses '\' as the default escape character. This is not RFC-4180 compliant. This disables the escape character.
		);

		$this->params = wp_parse_args( $args, $default_args );
		$this->file   = $file;

		if ( isset( $this->params['mapping']['from'], $this->params['mapping']['to'] ) ) {
			$this->params['mapping'] = array_combine( $this->params['mapping']['from'], $this->params['mapping']['to'] );
		}

		$this->read();


	}


	public function read() {
		$valid_filetypes = self::get_valid_csv_filetypes();
		$filetype        = wp_check_filetype( $this->file, $valid_filetypes );
		if ( ! in_array( $filetype['type'], $valid_filetypes, true ) ) {
			wp_die( esc_html__( 'Invalid file type. The importer supports CSV and TXT file formats.', 'wp-ever-accounting' ) );
		}

		$handle = fopen( $this->file, 'r' );
		if ( false !== $handle ) {
			$this->raw_keys = version_compare( PHP_VERSION, '5.3', '>=' ) ? array_map( 'trim', fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'], $this->params['escape'] ) ) : array_map( 'trim', fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'] ) ); // @codingStandardsIgnoreLine

			// Remove BOM signature from the first item.
			if ( isset( $this->raw_keys[0] ) ) {
				$this->raw_keys[0] = $this->remove_utf8_bom( $this->raw_keys[0] );
			}

			if ( 0 !== $this->params['start_pos'] ) {
				fseek( $handle, (int) $this->params['start_pos'] );
			}

			while ( 1 ) {
				$row = version_compare( PHP_VERSION, '5.3', '>=' ) ? fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'], $this->params['escape'] ) : fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'] ); // @codingStandardsIgnoreLine

				if ( false !== $row ) {
					$this->raw_data[]                                 = $row;
					$this->file_positions[ count( $this->raw_data ) ] = ftell( $handle );

					if ( ( $this->params['end_pos'] > 0 && ftell( $handle ) >= $this->params['end_pos'] ) || 0 === -- $this->params['lines'] ) {
						break;
					}
				} else {
					break;
				}
			}

			$this->file_position = ftell( $handle );
		}

		if ( ! empty( $this->params['mapping'] ) ) {
			$this->set_mapped_keys();
		}

		if ( $this->params['parse'] ) {
			$this->set_parsed_data();
		}
	}

	/**
	 * Remove UTF-8 BOM signature.
	 *
	 * @param string $string String to handle.
	 *
	 * @return string
	 */
	protected function remove_utf8_bom( $string ) {
		if ( 'efbbbf' === substr( bin2hex( $string ), 0, 6 ) ) {
			$string = substr( $string, 3 );
		}

		return $string;
	}

	/**
	 * Set file mapped keys.
	 */
	protected function set_mapped_keys() {
		$mapping = $this->params['mapping'];

		foreach ( $this->raw_keys as $key ) {
			$this->mapped_keys[] = isset( $mapping[ $key ] ) ? $mapping[ $key ] : $key;
		}
	}

	/**
	 * Map and format raw data to known fields.
	 */
	protected function set_parsed_data() {
		$parse_functions = $this->get_formatting_callback();
		$mapped_keys     = $this->get_mapped_keys();
		$use_mb          = function_exists( 'mb_convert_encoding' );

		// Parse the data.
		foreach ( $this->raw_data as $row_index => $row ) {
			// Skip empty rows.
			if ( ! count( array_filter( $row ) ) ) {
				continue;
			}

			$this->parsing_raw_data_index = $row_index;

			$data = array();

			do_action( 'woocommerce_product_importer_before_set_parsed_data', $row, $mapped_keys );

			foreach ( $row as $id => $value ) {
				// Skip ignored columns.
				if ( empty( $mapped_keys[ $id ] ) ) {
					continue;
				}

				// Convert UTF8.
				if ( $use_mb ) {
					$encoding = mb_detect_encoding( $value, mb_detect_order(), true );
					if ( $encoding ) {
						$value = mb_convert_encoding( $value, 'UTF-8', $encoding );
					} else {
						$value = mb_convert_encoding( $value, 'UTF-8', 'UTF-8' );
					}
				} else {
					$value = wp_check_invalid_utf8( $value, true );
				}

				$data[ $mapped_keys[ $id ] ] = call_user_func( $parse_functions[ $id ], $value );
			}

			/**
			 * Filter product importer parsed data.
			 *
			 * @param array               $parsed_data Parsed data.
			 * @param WC_Product_Importer $importer    Importer instance.
			 */
			$this->parsed_data[] = apply_filters( 'woocommerce_product_importer_parsed_data', $this->expand_data( $data ), $this );
		}
	}

	/**
	 * Get formatting callback.
	 *
	 * @since 4.3.0
	 * @return array
	 */
	protected function get_formatting_callback() {

		/**
		 * Columns not mentioned here will get parsed with 'wc_clean'.
		 * column_name => callback.
		 */
		$data_formatting = array();

		$callbacks = array();

		// Figure out the parse function for each column.
		foreach ( $this->get_mapped_keys() as $index => $heading ) {
			$callback = 'eaccounting_clean';

			if ( isset( $data_formatting[ $heading ] ) ) {
				$callback = $data_formatting[ $heading ];
			}

			$callbacks[] = $callback;
		}

		return apply_filters( 'woocommerce_product_importer_formatting_callbacks', $callbacks, $this );
	}


	/**
	 * Get file mapped headers.
	 *
	 * @return array
	 */
	public function get_mapped_keys() {
		return ! empty( $this->mapped_keys ) ? $this->mapped_keys : $this->raw_keys;
	}

	/**
	 * Get all the valid filetypes for a CSV file.
	 *
	 * @return array
	 */
	protected static function get_valid_csv_filetypes() {
		return apply_filters(
			'eaccounting_import_csv_filetypes',
			array(
				'text/csv',
				'text/comma-separated-values',
				'text/plain',
				'text/anytext',
				'text/*',
				'text/plain',
				'text/anytext',
				'text/*',
				'application/csv',
				'application/excel',
				'application/vnd.ms-excel',
				'application/vnd.msexcel',
			)
		);
	}
}
