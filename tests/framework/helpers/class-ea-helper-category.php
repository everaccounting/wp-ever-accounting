<?php

/**
 * Class EAccounting_Helper_Category.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class EAccounting_Helper_Category {
	/**
	 * Creates a account in the tests DB.
	 */
	public static function create_category( $name = 'Test Category', $type = 'expense' ) {
		$category = new EAccounting_Category();
		$category->set_name( $name );
		$category->set_type( $type );
		$category->save();

		return $category;
	}

}
