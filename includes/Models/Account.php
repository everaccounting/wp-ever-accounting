<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\BelongsTo;
use ByteKit\Models\Relations\HasMany;

defined( 'ABSPATH' ) || exit;

/**
 * Account model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int                $id ID of the category.
 * @property string             $name Name of the category.
 * @property string             $type Type of the account.
 * @property string             $number Account number.
 * @property float              $opening_balance Opening balance.
 * @property string             $bank_name Bank name.
 * @property string             $bank_phone Bank phone.
 * @property string             $bank_address Bank address.
 * @property string             $currency_code Currency code.
 * @property int                $creator_id Author ID.
 * @property int                $thumbnail_id Thumbnail ID.
 * @property string             $status Status of the account.
 * @property string             $created_at Date created.
 * @property string             $updated_at Date updated.
 *
 * @property-read string        $formatted_name Formatted name.
 * @property-read float         $balance Balance.
 * @property-read string        $formatted_balance Formatted balance.
 * @property-read Currency      $currency Currency relation.
 * @property-read Transaction[] $transactions Transaction relation.
 * @property-read Payment[]     $payments Payments relation.
 * @property-read Expense[]     $expenses Expenses relation.
 */
class Account extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_accounts';

	/**
	 * The table columns of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'type',
		'name',
		'number',
		'opening_balance',
		'bank_name',
		'bank_phone',
		'bank_address',
		'currency_code',
		'creator_id',
		'thumbnail_id',
		'status'
	);

	/**
	 * The attributes of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'type'            => 'bank',
		'opening_balance' => 0,
		'status'          => 'active',
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'              => 'int',
		'opening_balance' => 'float',
		'creator_id'      => 'int',
		'thumbnail_id'    => 'int',
	);

	/**
	 * The attributes that have aliases.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $aliases = array(
		'opening' => 'opening_balance',
	);

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'balance',
		'formatted_name',
		'formatted_balance',
	);

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $hidden = array(
		'opening_balance',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;


	/**
	 * The attributes that are searchable.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'bank_name',
		'bank_phone',
		'bank_address',
		'currency_code',
		'number',
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

	/**
	 * Get account types.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public static function get_types() {
		$account_types = array(
			'bank' => __( 'Bank', 'wp-ever-accounting' ),
			'card' => __( 'Card', 'wp-ever-accounting' ),
		);

		return apply_filters( 'ever_accounting_account_types', $account_types );
	}

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators and Relationship Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting attributes (accessors
	| and mutators) as well as defining relationships between models.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get balance.
	 *
	 * @since 1.1.0
	 * @since 1.0.2
	 *
	 * @return float|string
	 */
	public function get_balance() {
		global $wpdb;
		static $balance;
		if ( is_null( $balance ) ) {
			$transaction_total = (float) $wpdb->get_var(
				$wpdb->prepare( "SELECT SUM(CASE WHEN type='income' then amount WHEN type='expense' then - amount END) as total from {$this->get_db()->prefix}ea_transactions WHERE account_id=%d", $this->id )
			);
			$balance           = $this->opening_balance + $transaction_total;
		}

		return $balance;
	}

	/**
	 * Get formatted balance.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_balance() {
		return eac_format_amount( $this->balance, $this->currency_code );
	}

	/**
	 * Get formatted name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_name() {
		$name   = sprintf( '%s (%s)', $this->name, $this->currency_code );
		$number = $this->number;

		return $number ? sprintf( '%s - %s', $number, $name ) : $name;
	}

	/**
	 * Get the currency.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function currency() {
		return $this->belongs_to( Currency::class, 'currency_code', 'code' );
	}

	/**
	 * Transaction relation.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function transactions() {
		return $this->has_many( Transaction::class )->set( 'limit', 1 );
	}

	/**
	 * Revenue relation.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function revenues() {
		return $this->has_many( Revenue::class );
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
			return new \WP_Error( 'missing_required', __( 'Account name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->number ) ) {
			return new \WP_Error( 'missing_required', __( 'Account number rate is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->currency_code ) ) {
			return new \WP_Error( 'missing_required', __( 'Currency code is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->creator_id ) && is_user_logged_in() ) {
			$this->creator_id = get_current_user_id();
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
