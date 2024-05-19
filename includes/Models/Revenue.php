<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Revenue model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $customer_id ID of the customer.
 */
class Revenue extends Transaction {
	/**
	 * Object type in singular form.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'revenue';

	/**
	 * The properties that have aliases.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $aliases = array(
		'customer_id' => 'contact_id',
	);

	/**
	 * Properties that have transition effects when changed.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $transitionable = array(
		'status',
	);


	/**
	 * Create a new model instance.
	 *
	 * @param string|int|array $attributes Attributes.
	 *
	 * @throws \InvalidArgumentException If table name or object type is not set.
	 * @return void
	 */
	public function __construct( $attributes = null ) {
		$this->data['type']       = $this->get_object_type();
		$this->query_args['type'] = $this->get_object_type();
		parent::__construct( $attributes );
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

	/**
	 * Validate data before saving.
	 *
	 * @since 1.0.0
	 * @return void|\WP_Error Return WP_Error if data is not valid or void.
	 */
	public function validate_save_data() {
		if ( empty( $this->date ) ) {
			return new \WP_Error( 'missing_required', __( 'Transaction date is required.', 'wp-ever-accounting' ) );
		}
	}


	/*
	|--------------------------------------------------------------------------
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/
}
