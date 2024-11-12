<?php

namespace TestCase;

use Factory\Factory;

class WPTestCase extends \Codeception\TestCase\WPTestCase {

	/**
	 * Get the factory.
	 *
	 * @return Factory The factory.
	 */
	protected static function factory() {
		static $factory = null;
		if ( ! $factory ) {
			$factory = new Factory();
		}

		return $factory;
	}

	/**
	 * Debug and die.
	 *
	 * @param mixed $data The data to debug.
	 */
	protected function dd( $data ) {
		\Codeception\Util\Debug::debug( $data );
		die();
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
