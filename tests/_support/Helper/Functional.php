<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module {
	/**
	 * Get a random array key.
	 *
	 * @param array $array
	 *
	 * @return mixed
	 */
	public function getRandomElement( $array ) {
		$keys = array_keys( $array );
		$key  = array_rand( $keys );

		return $keys[ $key ];
	}
}
