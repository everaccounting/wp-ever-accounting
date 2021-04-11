<?php

namespace EverAccounting\Tests\Framework;

class UnitTest_Utils {

	/**
	 * Generates a random date.
	 *
	 * @return string
	 */
	public static function get_random_date() {
		return mt_rand( 1000, date( "Y" ) ) . '-' . mt_rand( 1, 12 ) . '-' . mt_rand( 1, 31 );
	}
}
