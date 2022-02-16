<?php
namespace Ever_Accounting\Tests\Factories;

class Category_Factory {
	/**
	 * Creates a currency in the tests DB.
	 */
	public static function create( $name = 'Test Category', $type = 'expense' ) {
		return \Ever_Accounting\Categories::insert( array(
			'name'  => $name,
			'type'  => $type,
			'color' => 'red',
		) );
	}

}
