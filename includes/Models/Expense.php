<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Expense model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 */
class Expense extends Transaction {
	/**
	 * Object type in singular form.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'expense';

	/**
	 * Create a new model instance.
	 *
	 * @param string|int|array $attributes Attributes.
	 *
	 * @throws \InvalidArgumentException If table name or object type is not set.
	 * @return void
	 */
	public function __construct( $attributes = null ) {
		$this->attributes['type'] = $this->get_object_type();
		$this->query_args['type'] = $this->get_object_type();
		parent::__construct( $attributes );
	}

	/*
	|--------------------------------------------------------------------------
	| Attributes & Relations
	|--------------------------------------------------------------------------
	| Define the attributes and relations of the model.
	*/


	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for saving, updating, and deleting objects.
	*/
	/**
	 * Load the object from the database.
	 *
	 * @param string|int $id ID of the object.
	 *
	 * @since 1.0.0
	 * @return $this
	 */
	protected function load( $id ) {
		parent::load( $id );
		if ( $this->get_object_type() !== $this->attributes['type'] ) {
			$this->apply_defaults();
		}

		return $this;
	}


	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( empty( $this->date ) ) {
			return new \WP_Error( 'missing_required', __( 'Transaction date is required.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}



	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/
}
