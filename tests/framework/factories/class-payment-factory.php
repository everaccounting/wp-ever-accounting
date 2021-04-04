<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Payment;
use EverAccounting\Tests\Framework\Helpers\Account_Helper;
use EverAccounting\Tests\Framework\Helpers\Category_Helper;


class Payment_Factory extends \WP_UnitTest_Factory_For_Thing {
	function __construct( $factory = null ) {
		parent::__construct( $factory );

		$payment_date   = mt_rand( 1000, date( "Y" ) ) . '-' . mt_rand( 1, 12 ) . '-' . mt_rand( 1, 31 );
		$account        = Account_Helper::create_account();
		$category       = Category_Helper::create_category( true, array( 'name' => 'Expense Factory' ) );
		$payment_method = array_keys( eaccounting_get_payment_methods() );
		$payment_method = array_rand( $payment_method );

		$this->default_generation_definitions = array(
			'type'           => 'expense',
			'payment_date'   => $payment_date,
			'account_id'     => $account->get_id(),
			'category_id'    => $category->get_id(),
			'payment_method' => $payment_method[0],
			'amount'         => new \WP_UnitTest_Generator_Sequence( '%d' ),
		);
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null $generation_definitions
	 *
	 * @return Payment |false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @param $args
	 *
	 * @return bool|Payment|int|\WP_Error
	 */
	function create_object( $args ) {
		return eaccounting_insert_payment( $args );
	}

	/**
	 * @param $payment_id
	 * @param $fields
	 *
	 * @return bool|Payment|int|\WP_Error
	 */
	function update_object( $payment_id, $fields ) {
		return eaccounting_insert_payment( array_merge( [ 'id' => $payment_id ], $fields ) );
	}

	/**
	 * @param $payment_id
	 */
	public function delete( $payment_id ) {
		eaccounting_delete_payment( $payment_id );
	}

	/**
	 * @param $payments
	 */
	public function delete_many( $payments ) {
		foreach ( $payments as $payment ) {
			$this->delete( $payment );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $payment_id Payment ID.
	 *
	 * @return Payment|false
	 */
	function get_object_by_id( $payment_id ) {
		return eaccounting_get_payment( $payment_id );
	}
}
