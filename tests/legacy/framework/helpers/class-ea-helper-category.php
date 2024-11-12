<?php

/**
 * Class EverAccounting_Helper_Category.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class EverAccounting_Helper_Category {
	/**
	 * Creates a account in the tests DB.
	 */
	public static function create_category( $name = 'Test Category', $type = 'expense' ) {
		return $category = eaccounting_insert_category(
			array(
				'name'  => $name,
				'type'  => $type,
				'color' => 'red',
			)
		);
	}
}
