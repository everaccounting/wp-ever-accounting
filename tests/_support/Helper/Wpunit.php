<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Wpunit extends \Codeception\Module {

	// create a new account helper.
	public function create_account( $args = array() ) {
		$account = eaccounting_insert_account(
			array_merge(
				array(
					'name'            => 'Test Account',
					'number'          => '12345678',
					'currency_code'   => 'USD',
					'opening_balance' => '10000.000',
					'bank_name'       => 'Standard Chartered Bank',
					'bank_phone'      => '+12375896',
					'bank_address'    => 'Liverpool, United Kingdom',
				),
				$args
			)
		);

		return $account;
	}

	// Create a new Category helper.
	public function create_category( $args = array() ) {
		$category = eaccounting_insert_category(
			array_merge(
				array(
					'name'  => 'Test Category',
					'type'  => 'expense',
					'color' => 'red',
				),
				$args
			)
		);

		return $category;
	}

}
