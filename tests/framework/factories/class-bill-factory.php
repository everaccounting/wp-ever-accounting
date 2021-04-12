<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Bill;
use EverAccounting\Tests\Framework\Helpers\Category_Helper;
use EverAccounting\Tests\Framework\Helpers\Currency_Helper;
use EverAccounting\Tests\Framework\Helpers\Vendor_Helper;

class Bill_Factory extends \WP_UnitTest_Factory_For_Thing {
	function __construct( $factory = null ) {
		parent::__construct( $factory );
		$currency = Currency_Helper::create_currency();
		$category = Category_Helper::create_category( true, array( 'name' => 'Bill-' . rand( 0, 10000 ) ) );
		$vendor   = Vendor_Helper::create_vendor( true, array( 'name' => 'Bill Vendor' ) );
		$date     = date( "Y-m-d" );// current date
		$due_date = strtotime( date( "Y-m-d", strtotime( $date ) ) . " +2 week" );

		$this->default_generation_definitions = array(
			'bill_number'   => '',
			'order_number'  => new \WP_UnitTest_Generator_Sequence( 'Order-%d' ),
			'status'        => 'draft',
			'issue_date'    => $date,
			'due_date'      => date( 'Y-m-d', $due_date ),
			'payment_date'  => null,
			'category_id'   => $category->get_id(),
			'vendor_id'     => $vendor->get_id(),
			'currency_code' => $currency->get_code(),
			'discount'      => new \WP_UnitTest_Generator_Sequence( '%d' ),
			'discount_type' => 'percentage',
			'total_tax'     => wp_rand( 0, 15 ),
			'note'          => new \WP_UnitTest_Generator_Sequence( 'Bill Notes-%s' ),
			'terms'         => new \WP_UnitTest_Generator_Sequence( 'Bill terms-%d' ),
		);
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null $generation_definitions
	 *
	 * @return Bill |false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @param $args
	 *
	 * @return bool|Bill|int|\WP_Error
	 */
	function create_object( $args ) {
		return eaccounting_insert_bill( $args );
	}

	/**
	 * @param $bill_id
	 * @param $fields
	 *
	 * @return bool|Bill|int|\WP_Error
	 */
	function update_object( $bill_id, $fields ) {
		return eaccounting_insert_bill( array_merge( [ 'id' => $bill_id ], $fields ) );
	}

	/**
	 * @param $bill_id
	 */
	public function delete( $bill_id ) {
		eaccounting_delete_bill( $bill_id );
	}

	/**
	 * @param $bills
	 */
	public function delete_many( $bills ) {
		foreach ( $bills as $bill ) {
			$this->delete( $bill );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $bill_id Bill ID.
	 *
	 * @return Bill|false
	 */
	function get_object_by_id( $bill_id ) {
		return eaccounting_get_bill( $bill_id );
	}
}
