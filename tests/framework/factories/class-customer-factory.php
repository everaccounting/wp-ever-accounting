<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Customer;
use EverAccounting\Tests\Framework\Helpers\Currency_Helper;

class Customer_Factory extends \WP_UnitTest_Factory_For_Thing {
	function __construct( $factory = null ) {
		parent::__construct( $factory );

		$birth_date = mt_rand( 1900, date( "Y" ) ) . '-' . mt_rand( 1, 12 ) . '-' . mt_rand( 1, 31 );
		$country    = array_keys( eaccounting_get_countries() );
		array_rand( $country );
		$currency                             = Currency_Helper::create_currency( array( 'code' => 'USD' ) );
		$this->default_generation_definitions = array(
			'name'          => new \WP_UnitTest_Generator_Sequence( 'Customer %s' ),
			'email'         => new \WP_UnitTest_Generator_Sequence( 'customer%d@email.com' ),
			'phone'         => new \WP_UnitTest_Generator_Sequence( '%d' ),
			'birth_date'    => $birth_date,
			'city'       => new \WP_UnitTest_Generator_Sequence( '%s' ),
			'state'       => new \WP_UnitTest_Generator_Sequence( '%s' ),
			'postcode'       => new \WP_UnitTest_Generator_Sequence( '%d' ),
			'country'       => $country[0],
			'website'       => new \WP_UnitTest_Generator_Sequence( 'Customer%s.test.com' ),
			'vat_number'    => new \WP_UnitTest_Generator_Sequence( 'Vat-%d' ),
			'currency_code' => $currency->get_code(),

		);
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null $generation_definitions
	 *
	 * @return Customer |false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @param $args
	 *
	 * @return bool|Customer|int|\WP_Error
	 */
	function create_object( $args ) {
		return eaccounting_insert_customer( $args );
	}

	/**
	 * @param $customer_id
	 * @param $fields
	 *
	 * @return bool|Customer|int|\WP_Error
	 */
	function update_object( $customer_id, $fields ) {
		return eaccounting_insert_customer( array_merge( [ 'id' => $customer_id ], $fields ) );
	}

	/**
	 * @param $customer_id
	 */
	public function delete( $customer_id ) {
		eaccounting_delete_customer( $customer_id );
	}

	/**
	 * @param $customers
	 */
	public function delete_many( $customers ) {
		foreach ( $customers as $customer ) {
			$this->delete( $customer );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $customer_id Customer ID.
	 *
	 * @return Customer|false
	 */
	function get_object_by_id( $customer_id ) {
		return eaccounting_get_customer( $customer_id );
	}
}
