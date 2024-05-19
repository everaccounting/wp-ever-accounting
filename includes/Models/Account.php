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
 * @property int                $author_id Author ID.
 * @property int                $thumbnail_id Thumbnail ID.
 * @property string             $status Status of the account.
 * @property string             $uuid UUID of the account.
 * @property string             $date_created Date created.
 * @property string             $date_updated Date updated.
 *
 * @property-read string        $formatted_name Formatted name.
 * @property-read float         $balance Balance.
 * @property-read string        $formatted_balance Formatted balance.
 * @property-read Currency      $currency Currency relation.
 * @property-read Transaction[] $transactions Transaction relation.
 * @property-read Revenue[]     $revenues Revenue relation.
 * @property-read Expense[]     $expenses Expense relation.
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
		'author_id',
		'thumbnail_id',
		'status',
		'uuid',
	);

	/**
	 * Data properties of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'status' => 'active',
	);

	/**
	 * The properties that should be cast to native types.
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
	 * The properties that have aliases.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $aliases = array(
		'opening' => 'opening_balance',
	);

	/**
	 * The properties that should be hidden for arrays.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $hidden = array(
		'opening_balance',
	);

	/**
	 * The properties that should be appended to the model's array form.
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
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;

	/**
	 * Searchable attributes.
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
	| Prop Definition Methods
	|--------------------------------------------------------------------------
	| This section contains methods that define and provide specific prop values
	| related to the model, such as statuses or types. These methods can be accessed
	| without instantiating the model.
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
	| Accessors, Mutators, Relationship and Validation Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting properties (accessors
	| and mutators) as well as defining relationships between models. It also includes
	| a data validation method that ensures data integrity before saving.
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
	public function get_balance_prop() {
		static $balance;
		if ( is_null( $balance ) ) {
			$transaction_total = (float) $this->get_db()->get_var(
				$this->get_db()->prepare( "SELECT SUM(CASE WHEN type='income' then amount WHEN type='expense' then - amount END) as total from {$this->get_db()->prefix}ea_transactions WHERE account_id=%d", $this->id )
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
	public function get_formatted_balance_prop() {
		return eac_format_amount( $this->balance, $this->currency_code );
	}

	/**
	 * Get formatted name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_name_prop() {
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
		return $this->has_many( Transaction::class );
	}

	/**
	 * Sanitize data before saving.
	 *
	 * @since 1.0.0
	 * @return void|\WP_Error Return WP_Error if data is not valid or void.
	 */
	public function validate_save_data() {
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
