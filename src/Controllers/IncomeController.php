<?php
/**
 * Revenue Controller
 *
 * Handles expense's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       RevenueController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;


defined( 'ABSPATH' ) || exit;

/**
 * Class RevenueController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class IncomeController extends Singleton {

	/**
	 * RevenueController constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_pre_save_income', array( __CLASS__, 'validate_expense_data' ), 10, 2 );
	}

	/**
	 * Prepare expense data before inserting into database.
	 *
	 * @since 1.1.0
	 *
	 * @param int   $id
	 * @param array $data
	 *
	 * @return array
	 */
	public static function prepare_expense_data( $data, $id = null ) {
		$currency = null;
		$account  = eaccounting_get_account( $data['account_id'] );
		if ( ! empty( $data['account_id'] ) && $account ) {
			$data['currency_code'] = $account->get_currency_code();
			$currency              = eaccounting_get_currency( $account->get_currency_code() );
		}

		if ( $currency ) {
			$data['currency_rate'] = $currency->get_rate();
			$data['amount']        = eaccounting_sanitize_price( $data['amount'], $data['currency_code'] );
		}

		$data['type'] = 'expense';

		return eaccounting_clean( $data );
	}

	/**
	 * Validate expense data.
	 *
	 * @since 1.1.0
	 *
	 * @param array     $data
	 * @param null      $id
	 * @param \WP_Error $errors
	 */
	public static function validate_expense_data( $data, $id = null ) {
		if ( empty( $data['payment_date'] ) ) {
			throw new Exception( 'empty_prop', __( 'Income date is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['payment_method'] ) ) {
			throw new Exception( 'empty_prop', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}

		$category = eaccounting_get_category( $data['category_id'] );
		if ( empty( $category ) || ! in_array( $category->get_type(), array( 'income', 'other' ), true ) ) {
			throw new Exception( 'empty_prop', __( 'A valid income category is required.', 'wp-ever-accounting' ) );
		}

		$account = eaccounting_get_account( $data['account_id'] );
		if ( empty( $account ) ) {
			throw new Exception( 'empty_prop', __( 'Account is required.', 'wp-ever-accounting' ) );
		}

		$customer = eaccounting_get_customer( $data['contact_id'] );
		if ( ! empty( $data['contact_id'] ) && empty( $customer ) ) {
			throw new Exception( 'empty_prop', __( 'Customer is not valid.', 'wp-ever-accounting' ) );
		}

		if ( empty( eaccounting_sanitize_number( $data['amount'] ) ) ) {
			throw new Exception( 'empty_prop', __( 'Income amount is required.', 'wp-ever-accounting' ) );
		}

	}

}
