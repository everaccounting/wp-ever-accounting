<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Vendor;

class Vendor_Factory extends \WP_UnitTest_Factory_For_Thing{
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
	 * @return Vendor |false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @param $args
	 *
	 * @return bool|Vendor|int|\WP_Error
	 */
	function create_object( $args ) {
		return eaccounting_insert_vendor( $args );
	}

	/**
	 * @param $vendor_id
	 * @param $fields
	 *
	 * @return bool|Vendor|int|\WP_Error
	 */
	function update_object( $vendor_id, $fields ) {
		return eaccounting_insert_vendor( array_merge(['id' => $vendor_id], $fields) );
	}

	/**
	 * @param $vendor_id
	 */
	public function delete( $vendor_id ) {
		eaccounting_delete_vendor( $vendor_id );
	}

	/**
	 * @param $vendors
	 */
	public function delete_many( $vendors ) {
		foreach ( $vendors as $vendor ) {
			$this->delete( $vendor );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $vendor_id Vendor ID.
	 *
	 * @return Vendor|false
	 */
	function get_object_by_id( $vendor_id ) {
		return eaccounting_get_vendor( $vendor_id );
	}
}
