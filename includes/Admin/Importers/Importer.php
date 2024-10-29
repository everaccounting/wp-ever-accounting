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
	public $file = '';

	/**
	 * Importer parameters.
	 *
	 * @since  1.0.2
	 * @var array
	 */
	protected $params = array(
		'position'         => 0, // File pointer start.
		'end_position'     => - 1, // File pointer end.
		'limit'            => 100, // Max lines to read.
		'mapping'          => array(), // Column mapping. csv_heading => schema_heading.
		'parse'            => true, // Whether to sanitize and format data.
		'update_existing'  => false, // Whether to update existing items.
		'delimiter'        => ',', // CSV delimiter.
		'prevent_timeouts' => true, // Check memory and time usage and abort if reaching limit.
		'enclosure'        => '"', // The character used to wrap text in the CSV.
		'escape'           => "\0", // PHP uses '\' as the default escape character. This is not RFC-4180 compliant. This disables the escape character.
	);


	/**
	 * Importer constructor.
	 *
	 * @param string $file File.
	 * @param array  $params Parameters.
	 *
	 * @since 1.0.2
	 */
	public function __construct( $file, $params = array() ) {
		$this->file   = $file;
		$this->params = wp_parse_args( $params, $this->params );
	}

	/**
	 * Process the import.
	 *
	 * @param int $pos
	 *
	 * @since 1.0.2
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function import( $step ) {
		$valid_filetypes = apply_filters(
			'eac_import_csv_filetypes',
			array(
				'csv' => 'text/csv',
				'txt' => 'text/plain',
			)
		);

		$filetype = wp_check_filetype( $this->file );
		if ( ! in_array( $filetype['type'], $valid_filetypes, true ) ) {
			return new \WP_Error( 'invalid_file_type', esc_html__( 'Invalid file type. The importer supports CSV and TXT file formats.', 'wp-ever-accounting' ) );
		}

		$handle = fopen( $this->file, 'r' ); // @codingStandardsIgnoreLine.

		if ( false === $handle ) {
			return new \WP_Error( 'file_not_found', esc_html__( 'File not found.', 'wp-ever-accounting' ) );
		}

		if ( false !== $handle ) {

		}
	}
}
