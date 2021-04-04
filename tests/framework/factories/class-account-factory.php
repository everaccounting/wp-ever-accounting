<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Customer;

class Customer_Factory extends \WP_UnitTest_Factory_For_Thing{
	function __construct( $factory = null ) {
		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'name'  => new \WP_UnitTest_Generator_Sequence( 'User %s' ),
			'email' => new \WP_UnitTest_Generator_Sequence( 'user%d@local.test' ),
		);
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null  $generation_definitions
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
		return eaccounting_insert_customer( array_merge(['id' => $customer_id], $fields) );
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
