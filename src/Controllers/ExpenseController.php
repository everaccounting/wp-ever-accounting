<?php
/**
 * Payment Controller
 *
 * Handles payments's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       PaymentController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;
use EverAccounting\Core\Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Class PaymentController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class ExpenseController extends Singleton {

	/**
	 * PaymentController constructor.
	 */
	public function __construct() {
		add_filter( 'eaccounting_prepare_payment_data', array( __CLASS__, 'prepare_payment_data' ), 10, 2 );
		add_action( 'eaccounting_validate_payment_data', array( __CLASS__, 'validate_payment_data' ), 10, 2 );
	}

	/**
	 * Prepare payment data before inserting into database.
	 *
	 * @param int $id
	 * @param array $data
	 *
	 * @return array
	 * @since 1.1.0
	 *
	 */
	public static function prepare_payment_data( $data, $id = null ) {
		if ( empty( $data['date_created'] ) ) {
			$data['date_created'] = current_time( 'mysql' );
		}
		if ( empty( $data['creator_id'] ) ) {
			$data['creator_id'] = eaccounting_get_current_user_id();
		}

		$account = eaccounting_get_account( $data['account_id'] );
		if ( ! empty( $data['account_id'] ) && $account ) {
			$data['currency_code'] = $account->get_currency_code();
		}

		$currency = eaccounting_get_currency( $data['currency_code'] );
		if ( ! empty( $data['currency_code'] ) && $currency ) {
			$data['currency_rate'] = $currency->get_rate();
			$data['amount']        = eaccounting_sanitize_price( $data['amount'], $data['currency_code'] );
		}

		$data['type'] = 'expense';

		return eaccounting_clean( $data );
	}

	/**
	 * Validate payment data.
	 *
	 * @param array $data
	 * @param null $id
	 * @param \WP_Error $errors
	 *
	 * @since 1.1.0
	 *
	 */
	public static function validate_payment_data( $data, $id = null ) {
		if ( empty( $data['payment_date'] ) ) {
			throw new Exception( 'empty_prop', __( 'Payment date is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['payment_method'] ) ) {
			throw new Exception( 'empty_prop', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}

		$category = eaccounting_get_category( $data['category_id'] );
		if ( empty( $category ) || ! in_array( $category->get_type(), array( 'expense', 'other' ), true ) ) {
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
			throw new Exception( 'empty_prop', __( 'Payment amount is required.', 'wp-ever-accounting' ) );
		}
	}

}
