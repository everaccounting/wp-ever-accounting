<?php
/**
 * Account Controller
 *
 * Handles account's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       AccountController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;
use EverAccounting\Models\Account;
use EverAccounting\Repositories\Accounts;
use EverAccounting\Repositories\Currencies;

defined( 'ABSPATH' ) || exit;

/**
 * Class AccountController
 *
 * @since   1.1.1
 *
 * @package EverAccounting\Controllers
 */
class AccountController extends Singleton {
	/**
	 * AccountController constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_pre_save_account', array( __CLASS__, 'validate_account_data' ), 10, 3 );
		add_action( 'eaccounting_delete_account', array( __CLASS__, 'delete_default_account' ) );
		add_action( 'eaccounting_delete_account', array( __CLASS__, 'update_transaction_account' ) );
	}


	/**
	 * Validate account data.
	 *
	 * @param array     $data
	 * @param int      $id
	 * @param Account $account
	 *
	 * @since 1.1.0
	 */
	public static function validate_account_data( $data, $id, $account ) {
		if ( empty( $data['name'] ) ) {
			$account->error( 'empty_prop', __( 'Account name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['number'] ) ) {
			$account->error( 'empty_prop', __( 'Account number is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['currency_code'] ) ) {
			$account->error( 'empty_prop', __( 'Currency code is required.', 'wp-ever-accounting' ) );
		}

		$currency = Currencies::instance()->get_by( 'code', $data['currency_code'] );
		if ( ! $currency ) {
			$account->error( 'invalid_prop', __( 'Currency code is invalid.', 'wp-ever-accounting' ) );
		}

		if ( intval( $id ) !== (int) Accounts::instance()->get_var(
			'id',
			array(
				'number' => $data['number'],
			)
		) ) {
			$account->error( 'invalid_prop', __( 'Duplicate account number.', 'wp-ever-accounting' ) );
		}

	}

	/**
	 * When an account is deleted check if
	 * default account need to be updated or not.
	 *
	 * @param $id
	 *
	 * @since 1.1.0
	 */
	public static function delete_default_account( $id ) {
		$default_account = eaccounting()->settings->get( 'default_account' );
		if ( intval( $default_account ) === intval( $id ) ) {
			eaccounting()->settings->set( array( array( 'default_account' => '' ) ), true );
		}
	}

	/**
	 * Delete account id from transactions.
	 *
	 * @param $id
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	function update_transaction_account( $id ) {
		global $wpdb;
		$id = absint( $id );
		if ( empty( $id ) ) {
			return false;
		}

		return $wpdb->update( Transactions::instance()->get_table(), array( 'account_id' => '' ), array( 'account_id', absint( $id ) ) );
	}

}
