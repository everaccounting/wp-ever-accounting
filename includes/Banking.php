<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Banking
 *
 * @package EverAccounting
 */
class Banking {

	/**
	 * Banking constructor.
	 */
	public function __construct() {
		add_action( 'eac_update_account_balance', array( __CLASS__, 'update_account_balance' ) );
	}

	/**
	 * Update account balance.
	 *
	 * @param int $account_id Account ID.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function update_account_balance( $account_id ) {
//		global $wpdb;
//		$account = EAC()->accounts->get( $account_id );
//		if ( ! $this ) {
//			return;
//		}
//
//		// when currency is equal to bank currency we will get the transactions in default currencies
//		$balance = (float) $wpdb->get_var(
//			$wpdb->prepare(
//				"SELECT SUM(CASE WHEN type='payment' then amount WHEN type='expense' then - amount END) as total
//				 FROM {$wpdb->prefix}ea_transactions WHERE account_id=%d", $account_id )
//		);
//
//		$account->balance = $balance;
//		$account->save();
	}
}
