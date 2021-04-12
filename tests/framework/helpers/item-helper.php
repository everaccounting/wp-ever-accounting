<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Item_Helper {

	public static function create_item( $save = true, $props = array() ) {
		$default = array(
			'name'           => 'Apple Mac Book Pro',
			'sku'            => 'mac-pro',
			'thumbnail_id'   => null,
			'description'    => 'Apple Mac Book Pro with m1 chip',
			'sale_price'     => '1299',
			'purchase_price' => '1199',
			'quantity'       => '1',
			'category_id'    => null,
			'sales_tax'      => '5',
			'purchase_tax'   => '3',
			'enabled'        => true,
			'date_created'   => date( 'Y-m-d' ),
		);

		$category               = self::create_category();
		$default['category_id'] = $category->get_id();

		$props = array_merge( $default, $props );

		if ( $save ) {
			return eaccounting_insert_item( $props, false );
		}

		return $props;
	}

	/**
	 * Create a items category
	 */
	public static function create_category() {
		return eaccounting_insert_category( array( 'name' => 'Electronics', 'type' => 'item', 'color' => '#e6e6e6' ) );
	}
}
