<?php
/**
 * Handle Customer Import.
 *
 * @since       1.0.2
 * @subpackage  Abstracts
 * @package     EverAccounting
 */

namespace EverAccounting\Import;

use EverAccounting\Abstracts\CSV_Importer;

defined( 'ABSPATH' ) || exit();


class Customer_CSV_Import extends CSV_Importer {
	/**
	 * Get database column and readable label.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	protected function get_headers() {
		return eaccounting_get_io_headers( 'customer' );
	}

	/**
	 * Return the required key to import item.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_required() {
		return array( 'name', 'currency_code' );
	}

	/**
	 * Get formatting callback.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	protected function get_formatting_callback() {
		return array(
			'email'         => 'sanitize_email',
			'birth_date'    => array( $this, 'parse_date_field' ),
			'address'       => array( $this, 'parse_description_field' ),
			'country'       => array( $this, 'parse_country_field' ),
			'website'       => 'esc_url_raw',
			'currency_code' => array( $this, 'parse_currency_code_field' ),
			'note'          => array( $this, 'parse_description_field' ),
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
			return 'skipped';
		}
		$data['type'] = 'customer';

		return eaccounting_insert_contact( $data );
	}

}
