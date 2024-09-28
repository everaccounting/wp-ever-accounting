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
 * @property Invoice $invoice Related invoice.
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

		// if does exist or account id is dirty, then set the new currency code.
		if ( ! $this->exists() || $this->is_dirty( 'account_id' ) ) {
			$account = Account::find( $this->account_id );
			if ( ! $account ) {
				return new \WP_Error( 'invalid_account', __( 'Invalid account.', 'wp-ever-accounting' ) );
			}
			$this->currency = $account->currency;
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

	/**
	 * Set next available number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_next_number() {
		$max    = $this->get_max_number();
		$prefix = get_option( 'eac_payment_prefix', strtoupper( substr( $this->get_object_type(), 0, 3 ) ) . '-' );
		$number = str_pad( $max + 1, get_option( 'eac_payment_digits', 4 ), '0', STR_PAD_LEFT );

		return $prefix . $number;
	}
}
