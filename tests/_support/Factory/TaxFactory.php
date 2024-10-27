<?php

namespace Factory;

/**
 * Class CategoryFactory
 *
 * This class is responsible for generating and managing Category objects in a WordPress environment.
 * It extends the WP_UnitTest_Factory_For_Thing class to provide custom functionality for tax creation.
 */
class TaxFactory extends \WP_UnitTest_Factory_For_Thing {

	/**
	 * CategoryFactory constructor.
	 *
	 * Initializes the factory with default generation definitions for taxes.
	 *
	 * @param null|\WP_UnitTest_Factory $factory The factory instance to use for creation.
	 */
	public function __construct( $factory = null ) {
		parent::__construct( $factory );
		$this->default_generation_definitions = array(
			'name'     => new \WP_UnitTest_Generator_Sequence( 'Category %s' ),
			'rate'     => wp_rand( 1, 10 ),
			'compound' => wp_rand( 0, 1 ),
		);
	}

	/**
	 * Create a new tax object.
	 *
	 * Inserts a new tax into the database using the provided arguments.
	 *
	 * @param array $args Arguments for creating the tax.
	 *
	 * @return mixed The created tax object or false on failure.
	 */
	public function create_object( $args ) {
		return EAC()->taxes->insert( $args );
	}

	/**
	 * Update an existing tax object.
	 *
	 * Modifies the specified fields of the tax object and saves the changes.
	 *
	 * @param object $object The tax object to update.
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
	 * Retrieve a tax object by its ID.
	 *
	 * Fetches the tax object corresponding to the given ID from the database.
	 *
	 * @param int $object_id The ID of the tax to retrieve.
	 *
	 * @return mixed The tax object or null if not found.
	 */
	public function get_object_by_id( $object_id ) {
		if ( is_numeric( $object_id ) ) {
			return EAC()->taxes->get( $object_id );
		}

		return $object_id;
	}
}
