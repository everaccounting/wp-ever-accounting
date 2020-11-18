<?php
/**
 * Handle customer import.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Import
 */

namespace EverAccounting\Import;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Importer;
use EverAccounting\Query_Currency;

/**
 * Class Import_Customers
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Import
 */
class Import_Customers extends CSV_Importer {
	/**
	 * Get supported key and readable label.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function get_headers() {
		return eaccounting_get_io_headers( 'customer' );
	}


	/**
	 * Return the required key to import item.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_required() {
		return array( 'name', 'currency_code' );
	}

	/**
	 * Get formatting callback.
	 *
	 * @return array
	 * @since 1.0.2
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
			return new \WP_Error( 'empty_prop', __( 'Empty Name', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['currency_code'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Currency Code', 'wp-ever-accounting' ) );
		}

		$exists = Query_Currency::init()->find( $data['currency_code'], 'code' );

		if ( empty( $exists ) ) {
			return new \WP_Error( 'invalid_prop', __( 'Currency with provided code does not not exist.', 'wp-ever-accounting' ) );
		}
		$data['type'] = 'customer';

		return eaccounting_insert_contact( $data );
	}
}
