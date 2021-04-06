<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Revenue_Helper {

	public static function create_revenue( $save = true, $props = array() ) {
		$default = array(
			'type'           => 'income',
			'payment_date'   => date( 'Y-m-d' ),
			'amount'         => '100',
			'currency_code'  => null,
			'currency_rate'  => 0.00,
			'account_id'     => null,
			'document_id'    => 12,
			'contact_id'     => null,
			'category_id'    => null,
			'description'    => 'Get money from the customer',
			'payment_method' => 'cash',
			'reference'      => 'ref',
			'attachment_id'  => 1,
			'parent_id'      => 0,
			'reconciled'     => 0,
			'creator_id'     => 1,
			'date_created'   => date( 'Y-m-d' ),
		);
		if ( is_null( $default['currency_code'] ) ) {
			$currency                 = Currency_Helper::create_currency();
			$default['currency_code'] = $currency->get_code();
			$default['currency_rate'] = $currency->get_rate();

		}

		if ( is_null( $default['account_id'] ) ) {
			$account               = Account_Helper::create_account();
			$default['account_id'] = $account->get_id();
		}

		if ( is_null( $default['contact_id'] ) ) {
			$contact               = Customer_Helper::create_customer();
			$default['contact_id'] = $contact->get_id();
		}

		if ( is_null( $default['category_id'] ) ) {
			$category               = Category_Helper::create_category();
			$default['category_id'] = $category->get_id();
		}

		$props = array_merge( $default, $props );

		if ( $save ) {
			return eaccounting_insert_revenue( $props, false );
		}

		return $props;
	}
}
