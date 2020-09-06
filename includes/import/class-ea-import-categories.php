<?php
/**
 * Handle category import.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Import
 */

namespace EverAccounting\Import;
defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Importer;

/**
 * Class Import_Categories
 * @since   1.0.2
 *
 * @package EverAccounting\Import
 */
class Import_Categories extends CSV_Importer {
	/**
	 * Get supported key and readable label.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function get_headers() {
		return eaccounting_get_io_headers( 'category' );
	}

	/**
	 * Return the required key to import item.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_required() {
		return array( 'name', 'type' );
	}

	/**
	 * Get formatting callback.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function get_formatting_callback() {
		return array(
			'name'  => array( $this, 'parse_text_field' ),
			'type'  => array( $this, 'parse_text_field' ),
			'color' => array( $this, 'parse_text_field' ),
		);
	}

	/**
	 * Process a single item and save.
	 *
	 * @param array $data Raw CSV data.
	 *
	 * @return string|\WP_Error
	 */
	protected function import_item( $data ) {
		if ( empty( $data['name'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Name', '' ) );
		}
		if ( empty( $data['type'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Type', '' ) );
		}

		return eaccounting_insert_category( $data );
	}

}
