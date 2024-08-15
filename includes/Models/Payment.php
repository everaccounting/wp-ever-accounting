<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Payment model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int $customer_id ID of the customer.
 */
class Payment extends Transaction {
	/**
	 * Object type in singular form.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'payment';

	/**
	 * The attributes that have aliases.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $aliases = array(
		'customer_id' => 'contact_id',
	);

	/**
	 * Default query variables passed to Query.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $query_vars = array(
		'type'           => 'payment',
		'search_columns' => array( 'id', 'contact_id', 'amount', 'status', 'date' ),
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
	 * @return void
	 */
	public function __construct( $attributes = null ) {
		$this->attributes['status'] = 'completed';
		$this->attributes['type']   = $this->get_object_type();
		$this->query_vars['type']   = $this->get_object_type();
		parent::__construct( $attributes );
	}

	/*
	|--------------------------------------------------------------------------
	| Property Definition Methods
	|--------------------------------------------------------------------------
	| This section contains static methods that define and return specific
	| property values related to the model.
	| These methods are accessible without creating an instance of the model.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get statuses.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public static function get_statuses() {
		return apply_filters(
			'ever_accounting_payment_statuses',
			array(
				'pending'   => esc_html__( 'Pending', 'wp-ever-accounting' ),
				'completed' => esc_html__( 'Completed', 'wp-ever-accounting' ),
				'refunded'  => esc_html__( 'Refunded', 'wp-ever-accounting' ),
				'cancelled' => esc_html__( 'Cancelled', 'wp-ever-accounting' ),
			)
		);
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
	| CRUD Methods
	|--------------------------------------------------------------------------
	| This section contains methods for creating, reading, updating, and deleting
	| objects in the database.
	|--------------------------------------------------------------------------
	*/
	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|static WP_Error on failure, or the object on success.
	 */
	public function save() {
		if ( empty( $this->date ) ) {
			return new \WP_Error( 'missing_required', __( 'Payment date is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->status ) ) {
			return new \WP_Error( 'missing_required', __( 'Payment status is required.', 'wp-ever-accounting' ) );
		}

		return parent::save();
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
