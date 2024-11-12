<?php
// phpcs:disable

namespace EverAccounting\Tests\Factory;

class CategoryFactory extends \WP_UnitTest_Factory_For_Thing {

	function create_object( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'type'        => 'income',
				'name'        => wp_generate_password( 12, false ),
				'description' => wp_generate_password( 20, false ),
			)
		);

		return EAC()->categories->insert( $args );
	}

	function update_object( $object, $fields ) {
		// TODO: Implement update_object() method.
	}

	function get_object_by_id( $object_id ) {
		// TODO: Implement get_object_by_id() method.
	}
}
