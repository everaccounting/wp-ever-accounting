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
 * @property int    $creator_id Creator ID of the transfer.
 * @property string $created_at Date the transfer was created.
 * @property string $updated_at Date the transfer was last updated.
 *
 * @property int    $from_account_id From account ID of the transfer.
 * @property int    $to_account_id To account ID of the transfer.
 * @property string $currency Currency code of the transfer.
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
		'date',
		'amount',
		'currency',
		'reference',
		'payment_id',
		'expense_id',
		'creator_id',
		'from_account_id',
		'to_account_id',
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'date'            => 'date',
		'amount'          => 'double',
		'exchange_rate'   => 'double',
		'reference'       => 'string',
		'expense_id'      => 'int',
		'revenue_id'      => 'int',
		'from_account_id' => 'int',
		'to_account_id'   => 'int',
	);

	/**
	 * Default query variables passed to Query class.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $query_vars = array(
		'search_columns' => array(
			'from_account_id',
			'to_account_id',
			'amount',
			'date',
			'payment_method',
		),
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $has_timestamps = true;

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
	| Accessors, Mutators and Relationship
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting properties (accessors
	| and mutators) as well as defining relationships between models.
	|--------------------------------------------------------------------------
	*/
	/**
	 * Payment relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function payment() {
		return $this->belongs_to( Payment::class, 'payment_id' );
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

	/**
	 * From account relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function from_account() {
		return $this->belongs_to( Account::class, 'from_account_id' );
	}

	/**
	 * To account relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function to_account() {
		return $this->belongs_to( Account::class, 'to_account_id' );
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
	 *
	 * @throws \Exception If there is an error saving the transfer.
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

		try {
			$this->get_db()->query( 'START TRANSACTION' );

			// Create a payment and expense for the transfer.
			$payment = $this->payment()->insert(
				array(
					'account_id'     => $this->to_account_id,
					'date'           => $this->date,
					'amount'         => $this->amount,
					'currency'       => $to_account->currency,
					'payment_method' => $this->payment_method,
					'reference'      => $this->reference,
				)
			);

			if ( is_wp_error( $payment ) ) {
				throw new \Exception( $payment->get_error_message() );
			}

			$amount = $this->amount;
			// If from and to account currency is different, then we have to convert the amount.
			if ( $from_account->currency !== $to_account->currency ) {
				$amount = eac_convert_currency( $this->amount, $this->currency, $to_account->currency );
			}

			$expense = $this->expense()->insert(
				array(
					'account_id'     => $this->from_account_id,
					'date'           => $this->date,
					'amount'         => $amount,
					'currency'       => $this->currency,
					'payment_method' => $this->payment_method,
					'reference'      => $this->reference,
				)
			);

			if ( is_wp_error( $expense ) ) {
				throw new \Exception( $expense->get_error_message() );
			}

			$this->get_db()->query( 'COMMIT' );

			$this->set( 'payment_id', $payment->id );
			$this->set( 'expense_id', $expense->id );
			$this->set( 'currency', $to_account->currency );
			$this->set( 'from_account_id', $from_account->id );
			$this->set( 'to_account_id', $to_account->id );
			$this->set( 'reference', $this->reference );
			$this->set( 'creator_id', get_current_user_id() );

			return parent::save();

		} catch ( \Exception $e ) {
			$this->get_db()->query( 'ROLLBACK' );

			return new \WP_Error( 'db_error', $e->getMessage() );
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
