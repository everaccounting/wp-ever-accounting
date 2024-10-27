<?php
// phpcs:disable

namespace EverAccounting\Tests\TestCase;

use EverAccounting\Tests\Factory\Factory;

class AccountingTestCase extends \Codeception\TestCase\WPTestCase {

	protected static function factory() {
		static $factory = null;
		if ( ! $factory ) {
			$factory = new Factory();
		}

		return $factory;
	}

	/**
	 * Get a random element from an array.
	 *
	 * @param array $things The array.
	 *
	 * @return mixed The random element.
	 */
	public function pickOne( $things ) {
		$keys = array_keys( $things );
		$key  = array_rand( $keys );

		return $keys[ $key ];
	}
}
