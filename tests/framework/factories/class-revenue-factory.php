<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Revenue;
use EverAccounting\Tests\Framework\Helpers\Account_Helper;
use EverAccounting\Tests\Framework\Helpers\Category_Helper;


class Revenue_Factory extends \WP_UnitTest_Factory_For_Thing {
	function __construct( $factory = null ) {
		parent::__construct( $factory );

		$payment_date   = mt_rand( 1000, date( "Y" ) ) . '-' . mt_rand( 1, 12 ) . '-' . mt_rand( 1, 31 );
		$account        = Account_Helper::create_account();
		$category       = Category_Helper::create_category( true, array( 'name' => 'Income Factory' ) );
		$payment_method = array_keys( eaccounting_get_payment_methods() );
		$payment_method = array_rand( $payment_method );

		$this->default_generation_definitions = array(
			'type'           => 'income',
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
	 * @return Revenue |false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @param $args
	 *
	 * @return bool|Revenue|int|\WP_Error
	 */
	function create_object( $args ) {
		return eaccounting_insert_revenue( $args );
	}

	/**
	 * @param $revenue_id
	 * @param $fields
	 *
	 * @return bool|Revenue|int|\WP_Error
	 */
	function update_object( $revenue_id, $fields ) {
		return eaccounting_insert_revenue( array_merge( [ 'id' => $revenue_id ], $fields ) );
	}

	/**
	 * @param $revenue_id
	 */
	public function delete( $revenue_id ) {
		eaccounting_delete_revenue( $revenue_id );
	}

	/**
	 * @param $revenues
	 */
	public function delete_many( $revenues ) {
		foreach ( $revenues as $revenue ) {
			$this->delete( $revenue );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $revenue_id Revenue ID.
	 *
	 * @return Revenue|false
	 */
	function get_object_by_id( $revenue_id ) {
		return eaccounting_get_revenue( $revenue_id );
	}
}
