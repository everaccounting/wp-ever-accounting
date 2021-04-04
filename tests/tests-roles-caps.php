<?php
/**
 * Plugin custom user roles and caps
 *
 * @since 1.0.0
 * @package EverAccounting\Tests
 */

use EverAccounting\Tests\Framework\UnitTestCase;

/**
 * Plugin custom user roles and caps
 *
 * @since 1.0.0
 * @package EverAccounting\Tests
 */
class Tests_Roles_Caps extends UnitTestCase {
	public function test_admin_caps(){
		$this->assertEquals(current_user_can('manage_eaccounting'), true);
		$this->assertEquals(current_user_can('ea_manage_report'), true);
		$this->assertEquals(current_user_can('ea_manage_options'), true);
		$this->assertEquals(current_user_can('ea_import'), true);
		$this->assertEquals(current_user_can('ea_export'), true);
		$this->assertEquals(current_user_can('ea_manage_customer'), true);
		$this->assertEquals(current_user_can('ea_manage_vendor'), true);
		$this->assertEquals(current_user_can('ea_manage_account'), true);
		$this->assertEquals(current_user_can('ea_manage_payment'), true);
		$this->assertEquals(current_user_can('ea_manage_revenue'), true);
		$this->assertEquals(current_user_can('ea_manage_transfer'), true);
		$this->assertEquals(current_user_can('ea_manage_category'), true);
		$this->assertEquals(current_user_can('ea_manage_currency'), true);
		$this->assertEquals(current_user_can('ea_manage_item'), true);
		$this->assertEquals(current_user_can('ea_manage_invoice'), true);
		$this->assertEquals(current_user_can('ea_manage_bill'), true);
		$this->assertEquals(current_user_can('read'), true);
	}

	public function test_manager_caps(){
		global $current_user;
		$current_user->set_role('ea_manager');
		$this->assertEquals(current_user_can('manage_eaccounting'), true);
		$this->assertEquals(current_user_can('ea_manage_report'), true);
		$this->assertEquals(current_user_can('ea_manage_options'), true);
		$this->assertEquals(current_user_can('ea_import'), true);
		$this->assertEquals(current_user_can('ea_export'), true);
		$this->assertEquals(current_user_can('ea_manage_customer'), true);
		$this->assertEquals(current_user_can('ea_manage_vendor'), true);
		$this->assertEquals(current_user_can('ea_manage_account'), true);
		$this->assertEquals(current_user_can('ea_manage_payment'), true);
		$this->assertEquals(current_user_can('ea_manage_revenue'), true);
		$this->assertEquals(current_user_can('ea_manage_transfer'), true);
		$this->assertEquals(current_user_can('ea_manage_category'), true);
		$this->assertEquals(current_user_can('ea_manage_currency'), true);
		$this->assertEquals(current_user_can('ea_manage_item'), true);
		$this->assertEquals(current_user_can('ea_manage_invoice'), true);
		$this->assertEquals(current_user_can('ea_manage_bill'), true);
		$this->assertEquals(current_user_can('read'), true);
		$current_user->set_role('administrator');
	}

	public function test_accountant_caps(){
		global $current_user;
		$current_user->set_role('ea_accountant');
		$this->assertEquals(current_user_can('manage_eaccounting'), true);
		$this->assertEquals(current_user_can('ea_manage_report'), false);
		$this->assertEquals(current_user_can('ea_manage_options'), false);
		$this->assertEquals(current_user_can('ea_import'), false);
		$this->assertEquals(current_user_can('ea_export'), false);
		$this->assertEquals(current_user_can('ea_manage_customer'), true);
		$this->assertEquals(current_user_can('ea_manage_vendor'), true);
		$this->assertEquals(current_user_can('ea_manage_account'), true);
		$this->assertEquals(current_user_can('ea_manage_payment'), true);
		$this->assertEquals(current_user_can('ea_manage_revenue'), true);
		$this->assertEquals(current_user_can('ea_manage_transfer'), true);
		$this->assertEquals(current_user_can('ea_manage_category'), true);
		$this->assertEquals(current_user_can('ea_manage_currency'), true);
		$this->assertEquals(current_user_can('ea_manage_item'), true);
		$this->assertEquals(current_user_can('ea_manage_invoice'), true);
		$this->assertEquals(current_user_can('ea_manage_bill'), true);
		$this->assertEquals(current_user_can('read'), true);
		$current_user->set_role('administrator');
	}
}
