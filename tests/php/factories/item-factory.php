<?php

namespace Ever_Accounting\Tests\Factories;

class Item_Factory {
	/**
	 * Creates a item in the tests DB.
	 */
	public static function create( $name = 'Apple Mab Book Pro', $purchase_price = '123456', $sale_price = '150000' ) {
		return \Ever_Accounting\Items::insert( array(
			'name'           => $name,
			'purchase_price' => $purchase_price,
			'sale_price'     => $sale_price,
			'quantity'       => 1
		) );
	}

}
