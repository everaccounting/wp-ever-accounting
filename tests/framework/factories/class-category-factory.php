<?php

namespace EverAccounting\Tests\Framework\Factories;

use EverAccounting\Models\Category;

class Category_Factory extends \WP_UnitTest_Factory_For_Thing {
	function __construct( $factory = null ) {
		parent::__construct( $factory );

		$this->default_generation_definitions = array(
			'name' => new \WP_UnitTest_Generator_Sequence( 'Category %s' ),
			'type' => array_rand( eaccounting_get_category_types(), 1 ),
		);
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null $generation_definitions
	 *
	 * @return Category |false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @param $args
	 *
	 * @return bool|Category|int|\WP_Error
	 */
	function create_object( $args ) {
		return eaccounting_insert_category( $args );
	}

	/**
	 * @param $category_id
	 * @param $fields
	 *
	 * @return bool|Category|int|\WP_Error
	 */
	function update_object( $category_id, $fields ) {
		return eaccounting_insert_category( array_merge( [ 'id' => $category_id ], $fields ) );
	}

	/**
	 * @param $category_id
	 */
	public function delete( $category_id ) {
		eaccounting_delete_category( $category_id );
	}

	/**
	 * @param $categorys
	 */
	public function delete_many( $categorys ) {
		foreach ( $categorys as $category ) {
			$this->delete( $category );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $category_id Category ID.
	 *
	 * @return Category|false
	 */
	function get_object_by_id( $category_id ) {
		return eaccounting_get_category( $category_id );
	}
}
