<?php
/**
 * Handle accounts import.
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
 * Class Import_Accounts
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Import
 */
class Import_Accounts extends CSV_Importer {
	/**
	 * Get supported key and readable label.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function get_headers() {
		return eaccounting_get_io_headers( 'account' );
	}


	/**
	 * Return the required key to import item.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_required() {
		return array( 'name', 'number', 'currency_code' );
	}

	/**
	 * Get formatting callback.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function get_formatting_callback() {
		return array(
			'name'            => array( $this, 'parse_text_field' ),
			'number'          => array( $this, 'parse_text_field' ),
			'currency_code'   => array( $this, 'parse_currency_code_field' ),
			'opening_balance' => array( $this, 'parse_float_field' ),
			'bank_name'       => array( $this, 'parse_text_field' ),
			'bank_phone'      => array( $this, 'parse_text_field' ),
			'bank_address'    => array( $this, 'parse_description_field' ),
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
		//
		if ( empty( $data['name'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Account Name', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['number'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Account Number', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['currency_code'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Currency Code', 'wp-ever-accounting' ) );
		}

		$currency_code = null;
		$exists        = Query_Currency::init()->find( $data['currency_code'], 'code' );

		if ( empty( $exists ) ) {
			$currency = eaccounting_insert_currency( array(
				'name'      => $data['currency_code'],
				'code'      => $data['currency_code'],
				'rate'      => 1,
				'precision' => 0
			) );

			if ( ! is_wp_error( $currency ) ) {
				$currency_code = $currency->get_code();
			}
		}

		$data['currency_code'] = $currency_code;

		return eaccounting_insert_account( $data );
	}
}
