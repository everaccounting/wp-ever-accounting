<?php
/**
 * Handle revenue import.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Import
 */

namespace EverAccounting\Import;
defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Importer;
use EverAccounting\Query_Category;
use EverAccounting\Query_Currency;
use EverAccounting\Query_Account;

/**
 * Class Import_Revenues
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Import
 */
class Import_Revenues extends CSV_Importer {
	/**
	 * Get supported key and readable label.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function get_headers() {
		return eaccounting_get_io_headers( 'revenue' );
	}

	/**
	 * Return the required key to import item.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_required() {
		return array( 'paid_at', 'currency_code', 'account_name', 'category_name', 'payment_method' );
	}

	/**
	 * Get formatting callback.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function get_formatting_callback() {
		return array(
			'paid_at'        => array( $this, 'parse_date_field' ),
			'amount'         => array( $this, 'parse_text_field' ),
			'currency_code'  => array( $this, 'parse_currency_code_field' ),
			'currency_rate'  => array( $this, 'parse_float_field' ),
			'account_name'   => array( $this, 'parse_text_field' ),
			'vendor_name'    => array( $this, 'parse_text_field' ),
			'category_name'  => array( $this, 'parse_text_field' ),
			'description'    => array( $this, 'parse_description_field' ),
			'payment_method' => array( $this, 'parse_text_field' ),
			'reference'      => array( $this, 'parse_text_field' ),
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
		if ( empty( $data['paid_at'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Payment Date', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['account_name'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Account Name', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['currency_code'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Currency Code', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['category_name'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Category Name', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['payment_method'] ) ) {
			return new \WP_Error( 'empty_prop', __( 'Empty Payment Method', 'wp-ever-accounting' ) );
		}

		$category_id   = Query_Category::init()->select( 'id' )->where( 'name', $data['category_name'] )->value( 0 );
		$currency_code = Query_Currency::init()->select( 'id' )->where( 'code', $data['currency_code'] )->value( 0 );
		$account_id    = Query_Account::init()->select( 'id' )->where( 'name', $data['account_name'] )->value( 0 );

		if ( empty( $category_id ) ) {
			return new \WP_Error( 'invalid_props', __( 'Category does not exist.', 'wp-ever-accounting' ) );
		}

		if ( empty( $currency_code ) ) {
			return new \WP_Error( 'invalid_props', __( 'Currency Code not exists', 'wp-ever-accounting' ) );
		}

		if ( empty( $account_id ) ) {
			return new \WP_Error( 'invalid_props', __( 'Transaction associated account is not exist.', 'wp-ever-accounting' ) );
		}

		$data['category_id'] = $category_id;
		$data['account_id']  = $account_id;
		$data['type']        = 'income';


		return eaccounting_insert_transaction( $data );
	}

}
