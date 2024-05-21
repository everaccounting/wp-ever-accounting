<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\BelongsTo;

defined( 'ABSPATH' ) || exit;

/**
 * Transfer model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the item.
 * @property int    $payment_id Payment ID of the item.
 * @property int    $expense_id Expense ID of the transfer.
 * @property double $amount Amount of the transfer.
 * @property string $uuid UUID of the transfer.
 * @property int    $creator_id Creator ID of the transfer.
 * @property string $date_created Date the transfer was created.
 * @property string $date_updated Date the transfer was last updated.
 *
 * @property int    $from_account_id From account ID of the transfer.
 * @property int    $to_account_id To account ID of the transfer.
 * @property string $currency_code Currency code of the transfer.
 * @property float  $exchange_rate Exchange rate of the transfer.
 * @property string $date Date of the transfer.
 * @property string $payment_method Payment method of the transfer.
 * @property string $reference Reference of the transfer.
 */
class Transfer extends Model {
	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_transfers';

	/**
	 * The table columns of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'revenue_id',
		'expense_id',
		'uuid',
		'author_id',
	);

	/**
	 * The model's data properties.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $props = array(
		'from_account_id' => null,
		'to_account_id'   => null,
		'amount'          => 0.00,
		'currency_code'   => null,
		'exchange_rate'   => 1,
		'date'            => null,
		'payment_method'  => '',
		'reference'       => '',
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'expense_id'      => 'int',
		'revenue_id'      => 'int',
		'from_account_id' => 'int',
		'to_account_id'   => 'int',
		'amount'          => 'float',
		'exchange_rate'   => 'float',
		'date'            => 'datetime',
	);

	/**
	 * Searchable attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'from_account_id',
		'to_account_id',
		'amount',
		'date',
		'payment_method',
	);

	/**
	 * The properties that aren't mass assignable.
	 *
	 * @since 1.0.0
	 * @var string[]|bool
	 */
	protected $guarded = array(
		'currency_code',
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
	 * Revenue relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function revenue() {
		return $this->belongs_to( Revenue::class, 'revenue_id' );
	}

	/**
	 * Expense relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function expense() {
		return $this->belongs_to( Expense::class, 'expense_id' );
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
		if ( empty( $this->from_account_id ) ) {
			return new \WP_Error( 'missing_required', __( 'From account is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->to_account_id ) ) {
			return new \WP_Error( 'missing_required', __( 'To account is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->amount ) ) {
			return new \WP_Error( 'missing_required', __( 'Transfer amount is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->date ) ) {
			return new \WP_Error( 'missing_required', __( 'Transfer date is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->payment_method ) ) {
			return new \WP_Error( 'missing_required', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}
		// Check if from account and to account is same.
		if ( $this->from_account_id === $this->to_account_id ) {
			return new \WP_Error( 'invalid_data', __( 'From and to account cannot be the same.', 'wp-ever-accounting' ) );
		}
		$from_account = Account::find( $this->from_account_id );
		$to_account   = Account::find( $this->to_account_id );

		if ( ! $from_account ) {
			return new \WP_Error( 'invalid_data', __( 'Transfer from account does not exists.', 'wp-ever-accounting' ) );
		}

		if ( ! $to_account ) {
			return new \WP_Error( 'invalid_data', __( 'Transfer to account does not exists.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->uuid ) ) {
			$this->uuid = wp_generate_uuid4();
		}

		$expense = $this->expense()->insert(
			array(
				'account_id'     => $this->from_account_id,
				'date'           => $this->date,
				'amount'         => $this->amount,
				'currency_code'  => $this->currency_code,
				'payment_method' => $this->payment_method,
				'reference'      => $this->reference,
			)
		);
		if ( is_wp_error( $expense ) ) {
			return $expense;
		}

		$amount = $this->amount;
		// If from and to account currency is different, then we have to convert the amount.
		if ( $from_account->currency_code !== $to_account->currency_code ) {
			$amount = eac_convert_currency( $this->amount, $this->currency_code, $to_account->currency_code );
		}

		$revenue = $this->revenue()->insert(
			array(
				'account_id'     => $this->to_account_id,
				'date'           => $this->date,
				'amount'         => $amount,
				'currency_code'  => $to_account->currency_code,
				'payment_method' => $this->payment_method,
				'reference'      => $this->reference,
			)
		);

		if ( is_wp_error( $revenue ) ) {
			return $revenue;
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
