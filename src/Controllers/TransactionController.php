<?php
/**
 * Transaction Controller
 *
 * Handles transaction's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       TransactionController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;


defined( 'ABSPATH' ) || exit;

/**
 * Class TransactionController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class TransactionController extends Singleton {

	/**
	 * TransactionController constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_pre_save_transaction', array( __CLASS__, 'validate_transaction_data' ), 10, 2 );
	}

	/**
	 * Validate transaction data.
	 *
	 * @param array $data
	 * @param null $id
	 *
	 * @since 1.1.0
	 *
	 */
	public static function validate_transaction_data( $data, $id ) {
		if ( empty( $data['payment_date'] ) ) {
			throw new Exception( 'empty_prop', __( 'Transaction date is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['type'] ) ) {
			throw new Exception( 'empty_prop', __( 'Transaction type is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['payment_method'] ) ) {
			throw new Exception( 'empty_prop', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['category_id'] ) ) {
			throw new Exception( 'empty_prop', __( 'Category id is required.', 'wp-ever-accounting' ) );
		}
		$category = eaccounting_get_category( $data['category_id'] );
		if ( empty( $category ) ) {
			throw new Exception( 'empty_prop', __( 'A valid transaction category is required.', 'wp-ever-accounting' ) );
		}

		$account = eaccounting_get_account( $data['account_id'] );
		if ( empty( $account ) ) {
			throw new Exception( 'empty_prop', __( 'Account is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( eaccounting_sanitize_number( $data['amount'] ) ) ) {
			throw new Exception( 'empty_prop', __( 'Transaction amount is required.', 'wp-ever-accounting' ) );
		}
	}
}
