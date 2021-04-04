<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Item;
use EverAccounting\Tests\Framework\Helpers\Category_Helper;

class Item_Factory extends \WP_UnitTest_Factory_For_Thing {
	function __construct( $factory = null ) {
		parent::__construct( $factory );

		$category                             = Category_Helper::create_category( true, array( 'name' => 'Item', 'type' => 'item' ) );
		$this->default_generation_definitions = array(
			'name'           => new \WP_UnitTest_Generator_Sequence( 'Item %s' ),
			'sku'            => new \WP_UnitTest_Generator_Sequence( '%s-%d' ),
			'category_id'    => $category->get_id(),
			'description'    => new \WP_UnitTest_Generator_Sequence( 'Item Description %s' ),
			'sale_price'     => new \WP_UnitTest_Generator_Sequence( '%d' ),
			'purchase_price' => new \WP_UnitTest_Generator_Sequence( '%d' ),
			'quantity'       => 1,
			'sales_tax'      => new \WP_UnitTest_Generator_Sequence( '%d' ),
			'purchase_tax'   => new \WP_UnitTest_Generator_Sequence( '%d' ),
		);
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null $generation_definitions
	 *
	 * @return Item |false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @param $args
	 *
	 * @return bool|Item|int|\WP_Error
	 */
	function create_object( $args ) {
		return eaccounting_insert_item( $args );
	}

	/**
	 * @param $item_id
	 * @param $fields
	 *
	 * @return bool|Item|int|\WP_Error
	 */
	function update_object( $item_id, $fields ) {
		return eaccounting_insert_item( array_merge( [ 'id' => $item_id ], $fields ) );
	}

	/**
	 * @param $item_id
	 */
	public function delete( $item_id ) {
		eaccounting_delete_item( $item_id );
	}

	/**
	 * @param $items
	 */
	public function delete_many( $items ) {
		foreach ( $items as $item ) {
			$this->delete( $item );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $item_id Item ID.
	 *
	 * @return Item|false
	 */
	function get_object_by_id( $item_id ) {
		return eaccounting_get_item( $item_id );
	}
}
