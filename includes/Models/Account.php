<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relation;

defined( 'ABSPATH' ) || exit;

/**
 * Account model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the category.
 * @property string $name Name of the category.
 * @property string $type Type of the account.
 * @property string $number Account number.
 * @property float  $opening_balance Opening balance.
 * @property string $bank_name Bank name.
 * @property string $bank_phone Bank phone.
 * @property string $bank_address Bank address.
 * @property string $currency_code Currency code.
 * @property int    $author_id Author ID.
 * @property int    $thumbnail_id Thumbnail ID.
 * @property string $status Status of the account.
 * @property string $uuid UUID of the account.
 * @property string $date_created Date created.
 * @property string $date_updated Date updated.
 *
 * @property string $formatted_name Formatted name.
 * @property float  $balance Balance.
 * @property string $formatted_balance Formatted balance.
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
	 * Table columns.
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
		'author_id',
		'thumbnail_id',
		'status',
		'uuid',
	);

	/**
	 * The model's attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'status' => 'active',
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
		'author_id'       => 'int',
		'thumbnail_id'    => 'int',
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
	 * Searchable attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'bank_name',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;

	/*
	|--------------------------------------------------------------------------
	| Attributes & Relations
	|--------------------------------------------------------------------------
	| Define the attributes and relations of the model.
	*/

	/**
	 * Get balance.
	 *
	 * @since 1.1.0
	 * @since 1.0.2
	 *
	 * @return float|string
	 */
	public function get_balance_attribute() {
		static $balance;
		if ( is_null( $balance ) ) {
			$transaction_total = (float) $this->wpdb()->get_var(
				$this->wpdb()->prepare( "SELECT SUM(CASE WHEN type='income' then amount WHEN type='expense' then - amount END) as total from {$this->wpdb()->prefix}ea_transactions WHERE account_id=%d", $this->id )
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
	public function get_formatted_balance_attribute() {
		return eac_format_amount( $this->balance, $this->currency_code );
	}

	/**
	 * Get formatted name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_name_attribute() {
		$name   = sprintf( '%s (%s)', $this->name, $this->currency_code );
		$number = $this->number;

		return $number ? sprintf( '%s - %s', $number, $name ) : $name;
	}

	/**
	 * Get the currency.
	 *
	 * @since 1.0.0
	 * @return Relation
	 */
	public function currency() {
		return $this->has_one( Currency::class, 'code', 'currency_code' );
	}

	/**
	 * Transaction relation.
	 *
	 * @since 1.0.0
	 * @return Relation
	 */
	public function transactions() {
		return $this->has_many( Transaction::class, 'account_id' );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for saving, updating, and deleting objects.
	*/

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
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

		if ( empty( $this->uuid ) ) {
			$this->uuid = wp_generate_uuid4();
		}

		if ( empty( $this->author_id ) && is_user_logged_in() ) {
			$this->author_id = get_current_user_id();
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
