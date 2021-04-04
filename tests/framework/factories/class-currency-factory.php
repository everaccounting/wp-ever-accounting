<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Currency;

class Currency_Factory extends \WP_UnitTest_Factory_For_Thing {
	function __construct( $factory = null ) {
		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'code'               => 'USD',
			'rate'               => new \WP_UnitTest_Generator_Sequence( '%d' ),
			'symbol'             => new \WP_UnitTest_Generator_Sequence( '%s' ),
			'position'           => 'before',
			'precision'          => new \WP_UnitTest_Generator_Sequence( '%d' ),
			'decimal_separator'  => '.',
			'thousand_separator' => ','
		);
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null $generation_definitions
	 *
	 * @return Currency |false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @param $args
	 *
	 * @return bool|Currency|int|\WP_Error
	 */
	function create_object( $args ) {
		return eaccounting_insert_currency( $args );
	}

	/**
	 * @param $currency_id
	 * @param $fields
	 *
	 * @return bool|Currency|int|\WP_Error
	 */
	function update_object( $currency_id, $fields ) {
		return eaccounting_insert_currency( array_merge( [ 'id' => $currency_id ], $fields ) );
	}

	/**
	 * @param $currency_id
	 */
	public function delete( $currency_id ) {
		eaccounting_delete_currency( $currency_id );
	}

	/**
	 * @param $currencies
	 */
	public function delete_many( $currencies ) {
		foreach ( $currencies as $currency ) {
			$this->delete( $currency );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $currency_id Currency ID.
	 *
	 * @return Currency|false
	 */
	function get_object_by_id( $currency_id ) {
		return eaccounting_get_currency( $currency_id );
	}
}
