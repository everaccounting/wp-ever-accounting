<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transaction Tax.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int         $id ID of the tax.
 * @property string      $name Name of the tax.
 * @property double      $rate Rate of the tax.
 * @property bool        $compound Whether the tax is compound.
 * @property double      $amount Amount of the tax.
 * @property int         $transaction_id ID of the transaction.
 * @property int         $tax_id ID of the tax.
 *
 * @property-read string $formatted_name Formatted name of the tax.
 */
class TransactionTax extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_transaction_taxes';

	/**
	 * The table columns of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'name',
		'rate',
		'compound',
		'amount',
		'status',
		'transaction_id',
		'tax_id',
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'             => 'int',
		'rate'           => 'float',
		'compound'       => 'bool',
		'transaction_id' => 'int',
		'tax_id'         => 'int',
	);

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'formatted_name',
	);

	/*
	|--------------------------------------------------------------------------
	| Property Definition Methods
	|--------------------------------------------------------------------------
	| This section contains static methods that define and return specific
	| property values related to the model.
	| These methods are accessible without creating an instance of the model.
	|--------------------------------------------------------------------------
	*/

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators and Relationship Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting attributes (accessors
	| and mutators) as well as defining relationships between models.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get formatted name.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_formatted_name() {
		return sprintf( '%1$s (%2$d%%)', $this->name, $this->rate );
	}

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
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->rate ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax rate is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->transaction_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Transaction ID is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->tax_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax ID is required.', 'wp-ever-accounting' ) );
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
