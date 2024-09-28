<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customer
 *
 * @since   1.1.0
 * @package EAccounting\Models
 */
class Customer extends Contact {
	/**
	 * The type of the object. Used for actions and filters.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'customer';

	/**
	 * Create a new model instance.
	 *
	 * @param string|array|object $attributes The model attributes.
	 */
	public function __construct( $attributes = array() ) {
		$this->attributes['type'] = $this->get_object_type();
		$this->query_vars['type'] = $this->get_object_type();
		parent::__construct( $attributes );
	}

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators and Relationship Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting attributes (accessors
	| and mutators) as well as defining relationships between models.
	|--------------------------------------------------------------------------
	*/

	/*
	|--------------------------------------------------------------------------
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/
}
