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
use EverAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit;

/**
 * Class AccountController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class AccountController extends Singleton {
	/**
	 * AccountController constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_pre_save_account', array( __CLASS__, 'validate_account_data' ), 10, 2 );
		add_action( 'eaccounting_delete_account', array( __CLASS__, 'delete_default_account' ) );
		add_action( 'eaccounting_delete_account', array( __CLASS__, 'update_transaction_account' ) );
	}


	/**
	 * Validate account data.
	 *
	 * @since 1.1.0
	 * 
	 * @param array $data
	 * @param int $id
	 * @param Account $account
	 * 
	 * @throws \Exception
	 */
	public static function validate_account_data( $data, $id ) {
		global $wpdb;
		if ( $id != (int) $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_accounts WHERE number='%s'", eaccounting_clean( $data['number'] ) ) ) ) { // @codingStandardsIgnoreLine
			throw new \Exception( __( 'Duplicate account.', 'wp-ever-accounting' ) );
		}

	}

	/**
	 * When an account is deleted check if
	 * default account need to be updated or not.
	 *
	 * @since 1.1.0
	 * 
	 * @param $id
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
	 * @since 1.0.2
	 * 
	 * @param $id
	 *
	 * @return bool
	 * 
	 */
	public static function update_transaction_account( $id ) {
		global $wpdb;
		$id = absint( $id );
		if ( empty( $id ) ) {
			return false;
		}

		return $wpdb->update( $wpdb->prefix . 'ea_transactions', array( 'account_id' => '' ), array( 'account_id' => absint( $id ) ) );
	}

}
