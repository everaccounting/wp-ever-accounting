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
	 * Customer_CSV_Import constructor.
	 */
	public function __construct( string $file, int $position = 0 ) {

		parent::__construct( $file, $position );
	}


	/**
	 * Process a single item and save.
	 *
	 * @param array $data Raw CSV data.
	 *
	 * @return string|\WP_Error
	 */
	protected function import_item( $data ) {
//		$keys     = array_keys( eaccounting_importer_customer_fields() );
//		$defaults = array_fill_keys( $keys, '' );
//		$data     = wp_parse_args( $data, $defaults );
//
//		if ( empty( $data['name'] ) || empty( $data['currency_code'] ) ) {
//			return 'skipped';
//		}
//
//		$data['type'] = 'customer';

//		$customer = eaccounting_insert_contact();

	}

	/**
	 * Get formatting callback.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	protected function get_formatting_callback() {
		return array(
			'name'          => 'eaccounting_clean',
			'email'         => 'sanitize_email',
			'phone'         => 'eaccounting_clean',
			'fax'           => 'eaccounting_clean',
			'birth_date'    => array( $this, 'parse_date_field' ),
			'address'       => array( $this, 'parse_description_field' ),
			'country'       => array( $this, 'parse_country_field' ),
			'website'       => 'esc_url_raw',
			'tax_number'    => 'eaccounting_clean',
			'currency_code' => array( $this, 'parse_currency_code_field' ),
			'note'          => array( $this, 'parse_description_field' ),
		);
	}

}
