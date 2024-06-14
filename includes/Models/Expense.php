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
 *
 * @property int    $vendor_id ID of the vendor.
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
	 * The attributes that have aliases.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $aliases = array(
		'vendor_id' => 'contact_id',
	);

	/**
	 * Default query variables passed to Query class when parsing.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $query_args = array(
		'update_meta_cache' => true,
	);

	/**
	 * Attributes that have transition effects when changed.
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
	 * @param string|array|object $attributes The model attributes.
	 */
	public function __construct( $attributes = array() ) {
		$this->attributes['status'] = 'pending';
		$this->attributes['type']   = $this->get_object_type();
		$this->query_args['type']   = $this->get_object_type();
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
			'ever_accounting_expense_statuses',
			array(
				'pending'   => esc_html__( 'Pending', 'wp-ever-accounting' ),
				'paid'      => esc_html__( 'paid', 'wp-ever-accounting' ),
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
			return new \WP_Error( 'missing_required', __( 'Transaction date is required.', 'wp-ever-accounting' ) );
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
