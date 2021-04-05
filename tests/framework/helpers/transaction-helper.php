<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Transaction_Helper {

	public static function create_payment( $save = true, $props = array() ) {
		$default = array(
			'account_id'     => null,
			'payment_date'   => date( 'Y-m-d' ),
			'amount'         => 1000,
			'vendor_id'      => null,
			'category_id'    => null,
			'payment_method' => '',
			'date_created'   => date( 'Y-m-d' ),
		);
		$props   = array_merge( $default, $props );

		if( empty( $props['account_id'] ) ){
			$props['account_id'] = Account_Helper::create_account();
		}

		if( empty( $props['vendor_id'] ) ){
			$props['vendor_id'] = Contact_Helper::create_vendor();
		}

		if( empty( $props['category_id'] ) ){
			$props['category_id'] = Category_Helper::create_category(true, ['type' => 'expense', 'name' => 'Expense']);
		}

		if ( $save ) {
			return eaccounting_insert_payment( $props );
		}

		return $props;
	}

	public static function create_revenue( $save = true, $props = array() ) {
		$default = array(
			'account_id'     => null,
			'payment_date'   => date( 'Y-m-d' ),
			'amount'         => 1000,
			'customer_id'      => null,
			'category_id'    => null,
			'payment_method' => '',
			'date_created'   => date( 'Y-m-d' ),
		);
		$props   = array_merge( $default, $props );

		if( empty( $props['account_id'] ) ){
			$props['account_id'] = Account_Helper::create_account();
		}

		if( empty( $props['customer_id'] ) ){
			$props['customer_id'] = Contact_Helper::create_customer();
		}

		if( empty( $props['category_id'] ) ){
			$props['category_id'] = Category_Helper::create_category(true, ['type' => 'income', 'name' => 'Income']);
		}

		if ( $save ) {
			return eaccounting_insert_revenue( $props );
		}

		return $props;
	}
}
