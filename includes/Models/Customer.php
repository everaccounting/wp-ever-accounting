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
	 * @param object|array|null $data The data to initialize the model.
	 *
	 * @throws \InvalidArgumentException If table name or object type is not set.
	 */
	public function __construct( $data = null ) {
		$this->data['type']       = $this->get_object_type();
		$this->query_args['type'] = $this->get_object_type();
		parent::__construct( $data );
	}


	/*
	|--------------------------------------------------------------------------
	| Prop Definition Methods
	|--------------------------------------------------------------------------
	| This section contains methods that define and provide specific prop values
	| related to the model, such as statuses or types. These methods can be accessed
	| without instantiating the model.
	|--------------------------------------------------------------------------
	*/

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators, Relationship and Validation Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting properties (accessors
	| and mutators) as well as defining relationships between models. It also includes
	| a data validation method that ensures data integrity before saving.
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
