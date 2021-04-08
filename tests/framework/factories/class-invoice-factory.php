<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Invoice;
use EverAccounting\Tests\Framework\Helpers\Category_Helper;
use EverAccounting\Tests\Framework\Helpers\Currency_Helper;
use EverAccounting\Tests\Framework\Helpers\Customer_Helper;

class Invoice_Factory extends \WP_UnitTest_Factory_For_Thing {
	function __construct( $factory = null ) {
		parent::__construct( $factory );
		$currency = Currency_Helper::create_currency( true, array( 'name' => 'Indian Rupee', 'code' => 'INR', 'rate' => 63 ) );
		$category = Category_Helper::create_category( true, array( 'name' => 'Invoice-' . rand( 0, 10000 ) ) );
		$customer = Customer_Helper::create_customer( true, array( 'name' => 'Invoice Customer' ) );
		$date     = date( "Y-m-d" );// current date
		$due_date = strtotime( date( "Y-m-d", strtotime( $date ) ) . " +2 week" );

		$this->default_generation_definitions = array(
			'invoice_number' => '',
			'order_number'   => new \WP_UnitTest_Generator_Sequence( 'order-%d' ),
			'status'         => 'draft',
			'issue_date'     => $date,
			'due_date'       => date( 'Y-m-d', $due_date ),
			'payment_date'   => null,
			'category_id'    => $category->get_id(),
			'contact_id'     => $customer->get_id(),
			'currency_code'  => $currency->get_code(),
			'currency_rate'  => $currency->get_rate(),
			'discount'       => new \WP_UnitTest_Generator_Sequence( '%d' ),
			'discount_type'  => 'percentage',
			'total_tax'      => 5,
			'note'           => new \WP_UnitTest_Generator_Sequence( 'Invoice Notes-%s' ),
			'terms'          => new \WP_UnitTest_Generator_Sequence( 'Invoice terms-%d' ),
		);
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null $generation_definitions
	 *
	 * @return Invoice |false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @param $args
	 *
	 * @return bool|Invoice|int|\WP_Error
	 */
	function create_object( $args ) {
		return eaccounting_insert_invoice( $args );
	}

	/**
	 * @param $invoice_id
	 * @param $fields
	 *
	 * @return bool|Invoice|int|\WP_Error
	 */
	function update_object( $invoice_id, $fields ) {
		return eaccounting_insert_invoice( array_merge( [ 'id' => $invoice_id ], $fields ) );
	}

	/**
	 * @param $invoice_id
	 */
	public function delete( $invoice_id ) {
		eaccounting_delete_invoice( $invoice_id );
	}

	/**
	 * @param $invoices
	 */
	public function delete_many( $invoices ) {
		foreach ( $invoices as $invoice ) {
			$this->delete( $invoice );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $invoice_id Invoice ID.
	 *
	 * @return Invoice|false
	 */
	function get_object_by_id( $invoice_id ) {
		return eaccounting_get_invoice( $invoice_id );
	}
}
