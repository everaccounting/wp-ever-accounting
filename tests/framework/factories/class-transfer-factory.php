<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Transfer;
use EverAccounting\Tests\Framework\Helpers\Account_Helper;

class Transfer_Factory extends \WP_UnitTest_Factory_For_Thing {
	function __construct( $factory = null ) {
		parent::__construct( $factory );

		$from_account  = Account_Helper::create_account( true, [ 'name' => 'Bank Asia', 'number' => rand() ] );
		$to_account    = Account_Helper::create_account( true, [ 'name' => 'City bank', 'number' => rand() ] );
		$transfer_date = mt_rand( 2010, date( "Y" ) ) . '-' . mt_rand( 1, 12 ) . '-' . mt_rand( 1, 31 );

		$payment_method = array_keys( eaccounting_get_payment_methods() );
		array_rand( $payment_method );

		$this->default_generation_definitions = array(
			'date'            => $transfer_date,
			'from_account_id' => $from_account->get_id(),
			'to_account_id'   => $to_account->get_id(),
			'amount'          => 100,
			'payment_method'  => $payment_method[0],
			'reference'       => new \WP_UnitTest_Generator_Sequence( 'Ref %s' ),
			'description'     => new \WP_UnitTest_Generator_Sequence( 'Desc %s' ),
		);
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null $generation_definitions
	 *
	 * @return Transfer |false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @param $args
	 *
	 * @return bool|Transfer|int|\WP_Error
	 */
	function create_object( $args ) {
		return eaccounting_insert_transfer( $args );
	}

	/**
	 * @param $transfer_id
	 * @param $fields
	 *
	 * @return bool|Transfer|int|\WP_Error
	 */
	function update_object( $transfer_id, $fields ) {
		return eaccounting_insert_transfer( array_merge( [ 'id' => $transfer_id ], $fields ) );
	}

	/**
	 * @param $transfers
	 */
	public function delete_many( $transfers ) {
		foreach ( $transfers as $transfer ) {
			$this->delete( $transfer );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $transfer_id Transfer ID.
	 *
	 * @return Transfer|false
	 */
	function get_object_by_id( $transfer_id ) {
		return eaccounting_get_transfer( $transfer_id );
	}

}