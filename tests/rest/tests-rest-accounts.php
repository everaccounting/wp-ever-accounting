<?php

use EverAccounting\Tests\Framework\REST_UnitTestCase;

class Tests_REST_Accounts extends REST_UnitTestCase {
	public function test_account_fields(){
		$account = \EverAccounting\Tests\Framework\Helpers\Account_Helper::create_account();
		var_dump($account);
	}
}
