<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Transfer_Helper {

	public static function create_transfer( $save = true, $props = array() ) {

		$default = array(
			'date'            => date( 'Y-m-d 00:00:00' ),
			'from_account_id' => null,
			'to_account_id'   => null,
			'amount'          => 100,
			'income_id'       => null,
			'expense_id'      => null,
			'payment_method'  => 'cash',
			'reference'       => 'Sample Bank Transfer',
			'description'     => 'Transfer money to new bank account',
			'date_created'    => date( 'Y-m-d' ),
		);

		if ( is_null( $default['from_account_id'] ) ) {
			$account                    = Account_Helper::create_account( true, array( 'name' => 'Senders Account', 'number' => rand( 0, 25000 ), 'currency_code' => 'USD', 'opening_balance' => '1500', ) );
			$default['from_account_id'] = $account->get_id();
		}
		if ( is_null( $default['to_account_id'] ) ) {
			$account                  = Account_Helper::create_account( true, array( 'name' => 'Receivers Account', 'number' => rand( 0, 25000 ), 'currency_code' => 'USD', 'opening_balance' => '1500', ) );
			$default['to_account_id'] = $account->get_id();
		}

		$props = array_merge( $default, $props );

		if ( $save ) {
			return eaccounting_insert_transfer( $props, false );
		}

		return $props;
	}
}