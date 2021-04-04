<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Account;
use EverAccounting\Tests\Framework\Helpers\Currency_Helper;

class Account_Factory extends \WP_UnitTest_Factory_For_Thing {
	function __construct( $factory = null ) {
		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'name'            => new \WP_UnitTest_Generator_Sequence( 'Account %s' ),
			'number'          => new \WP_UnitTest_Generator_Sequence( 'acc-%s-%d' ),
			'currency_code'   => Currency_Helper::create_currency(),
			'opening_balance' => new \WP_UnitTest_Generator_Sequence( '%d' ),
			'bank_name'       => new \WP_UnitTest_Generator_Sequence( 'Bank %s' ),
			'bank_phone'      => new \WP_UnitTest_Generator_Sequence( 'Bank %d' ),
			'bank_address'    => new \WP_UnitTest_Generator_Sequence( 'Bank %s' ),
			'thumbnail_id'    => new \WP_UnitTest_Generator_Sequence( '%d' ),
		);
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null $generation_definitions
	 *
	 * @return Account |false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @param $args
	 *
	 * @return bool|Account|int|\WP_Error
	 */
	function create_object( $args ) {
		return eaccounting_insert_account( $args );
	}

	/**
	 * @param $account_id
	 * @param $fields
	 *
	 * @return bool|Account|int|\WP_Error
	 */
	function update_object( $account_id, $fields ) {
		return eaccounting_insert_account( array_merge( [ 'id' => $account_id ], $fields ) );
	}

	/**
	 * @param $account_id
	 */
	public function delete( $account_id ) {
		eaccounting_delete_account( $account_id );
	}

	/**
	 * @param $accounts
	 */
	public function delete_many( $accounts ) {
		foreach ( $accounts as $account ) {
			$this->delete( $account );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $account_id Account ID.
	 *
	 * @return Account|false
	 */
	function get_object_by_id( $account_id ) {
		return eaccounting_get_account( $account_id );
	}
}
