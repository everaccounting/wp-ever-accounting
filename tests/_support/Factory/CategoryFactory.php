<?php

namespace Factory;

/**
 * Class CategoryFactory
 *
 * This class is responsible for generating and managing Category objects in a WordPress environment.
 * It extends the WP_UnitTest_Factory_For_Thing class to provide custom functionality for category creation.
 */
class CategoryFactory extends \WP_UnitTest_Factory_For_Thing {

	/**
	 * CategoryFactory constructor.
	 *
	 * Initializes the factory with default generation definitions for categories.
	 *
	 * @param null|\WP_UnitTest_Factory $factory The factory instance to use for creation.
	 */
	public function __construct( $factory = null ) {
		parent::__construct( $factory );
		$this->default_generation_definitions = array(
			'name'        => new \WP_UnitTest_Generator_Sequence( 'Category %s' ),
			'description' => new \WP_UnitTest_Generator_Sequence( 'Category description %s' ),
			'type'        => 'income',
		);
	}

	/**
	 * Create a new category object.
	 *
	 * Inserts a new category into the database using the provided arguments.
	 *
	 * @param array $args Arguments for creating the category.
	 *
	 * @return mixed The created category object or false on failure.
	 */
	public function create_object( $args ) {
		return EAC()->categories->insert( $args );
	}

	/**
	 * Update an existing category object.
	 *
	 * Modifies the specified fields of the category object and saves the changes.
	 *
	 * @param object $object The category object to update.
	 * @param array  $fields An associative array of fields and their new values.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function update_object( $object, $fields ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.objectFound
		foreach ( $fields as $field => $field_value ) {
			$object->$field = $field_value;
		}

		return $object->save();
	}

	/**
	 * Retrieve a category object by its ID.
	 *
	 * Fetches the category object corresponding to the given ID from the database.
	 *
	 * @param int $object_id The ID of the category to retrieve.
	 *
	 * @return mixed The category object or null if not found.
	 */
	public function get_object_by_id( $object_id ) {
		if ( is_numeric( $object_id ) ) {
			$object_id = EAC()->categories->get( $object_id );
		}

		return $object_id;
	}
}
