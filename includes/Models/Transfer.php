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
 * @property int          $id ID of the item.
 * @property int          $payment_id Payment ID of the item.
 * @property int          $expense_id Expense ID of the transfer.
 * @property int          $creator_id Creator ID of the transfer.
 * @property string       $created_at Date the transfer was created.
 * @property string       $updated_at Date the transfer was last updated.
 *
 * @property-read Payment $payment Payment object.
 * @property-read Expense $expense Expense object.
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
		'payment_id',
		'expense_id'
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'date'               => 'date',
		'payment_id'         => 'int',
		'expense_id'         => 'int',
		'amount'             => 'double',
		'from_account_id'    => 'int',
		'to_account_id'      => 'int',
		'from_exchange_rate' => 'double',
		'to_exchange_rate'   => 'double',
		'reference'          => 'string',
		'payment_method'     => 'string',
		'note'               => 'string',
	);

	/**
	 * Default query variables passed to Query class.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $query_vars = array(
		'search_columns' => array(
			'amount',
			'date',
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

//	/**
//	 * Date attribute.
//	 *
//	 * @since 1.0.0
//	 * @return string
//	 */
//	public function get_date() {
//		return $this->payment ? $this->payment->date : $this->expense->date;
//	}
//
//	/**
//	 * From account.
//	 *
//	 * @since 1.0.0
//	 * @return Account|null Account object or null.
//	 */
//	public function from_account() {
//		if ( $this->payment && $this->payment->account ) {
//			return $this->payment->account;
//		}
//		return null;
//	}
//
//	/**
//	 * To account.
//	 *
//	 * @since 1.0.0
//	 * @return Account|null Account object or null.
//	 */
//	public function to_account() {
//		return $this->expense && $this->expense->account ? $this->expense->account : null;
//	}
//
//	/**
//	 * Payment mode attribute.
//	 *
//	 * @since 1.0.0
//	 * @return string
//	 */
//	public function get_payment_method() {
//		return $this->payment ? $this->payment->payment_method : '';
//	}
//
//	/**
//	 * Reference attribute.
//	 *
//	 * @since 1.0.0
//	 * @return string
//	 */
//	public function get_reference() {
//		return $this->payment ? $this->payment->reference : '';
//	}
//
//	/**
//	 * Amount attribute.
//	 *
//	 * @since 1.0.0
//	 * @return double
//	 */
//	public function get_amount() {
//		return $this->payment ? $this->payment->amount : $this->expense->amount;
//	}
//
//	/**
//	 * Formatted amount attribute.
//	 *
//	 * @since 1.0.0
//	 * @return string
//	 */
//	public function get_formatted_amount() {
//		return $this->payment ? $this->payment->formatted_amount : 0;
//	}

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
		global $wpdb;

		// from and to accounts cannot be the same.
		if ( $this->from_account_id === $this->to_account_id ) {
			return new \WP_Error( 'same_account', __( 'From and to accounts cannot be the same.', 'wp-ever-accounting' ) );
		}

		// Prepare vars.
//		$from_account       = Account::find( $this->from_account_id );
//		$to_account         = Account::find( $this->to_account_id );
//		$expense            = Expense::make( $this->expense_id );
//		$payment            = Payment::make( $this->payment_id );
		$from_account_id    = $this->from_account_id ? $this->from_account_id : ( $this->expense && $this->expense->account_id ? $this->expense->account_id : 0 );
		$to_account_id      = $this->to_account_id ? $this->to_account_id : ( $this->payment && $this->payment->account_id ? $this->payment->account_id : 0 );
		$date               = $this->date ? $this->date : ( $this->expense && $this->expense->date ? $this->expense->date : date( 'Y-m-d' ) );
		$amount             = $this->amount ? $this->amount : ( $this->expense && $this->expense->amount ? $this->expense->amount : 0 );
		$payment_method     = $this->payment_method ? $this->payment_method : ( $this->expense && $this->expense->payment_method ? $this->expense->payment_method : '' );
		$from_exchange_rate = $this->from_exchange_rate ? $this->from_exchange_rate : ( $this->expense && $this->expense->exchange_rate ? $this->expense->exchange_rate : 1 );
		$to_exchange_rate   = $this->to_exchange_rate ? $this->to_exchange_rate : ( $this->payment && $this->payment->exchange_rate ? $this->payment->exchange_rate : 1 );
		$reference          = $this->reference ? $this->reference : ( $this->expense && $this->expense->reference ? $this->expense->reference : '' );
		$note               = $this->note ? $this->note : ( $this->expense && $this->expense->note ? $this->expense->note : '' );

		$wpdb->query( 'START TRANSACTION' );
		$expense->fill(
			array(
				'date'           => $date,
				'account_id'     => $this->from_account_id,
				'payment_date'   => $this->date,
				'amount'         => $this->amount,
				'exchange_rate'  => $this->from_exchange_rate,
				'note'           => $this->note,
				'payment_method' => $this->payment_method,
				'reference'      => $this->reference,
			)
		);
		$ret_val1 = $expense->save();
		if ( is_wp_error( $ret_val1 ) ) {
			$wpdb->query( 'ROLLBACK' );

			return $ret_val1;
		}

		$this->expense_id = $expense->id;

		$amount = $this->amount;
		if ( $from_account->currency !== $to_account->currency ) {
			$amount = eac_convert_currency( $amount, $this->from_exchange_rate, $this->to_exchange_rate );
		}

		$payment->fill(
			array(
				'date'           => $this->date,
				'account_id'     => $this->to_account_id,
				'payment_date'   => $this->date,
				'amount'         => $amount,
				'exchange_rate'  => $this->to_exchange_rate,
				'note'           => $this->note,
				'payment_method' => $this->payment_method,
				'reference'      => $this->reference,
			)
		);

		$ret_val2 = $payment->save();
		if ( is_wp_error( $ret_val2 ) ) {
			$wpdb->query( 'ROLLBACK' );

			return $ret_val2;
		}

		$this->payment_id = $payment->id;

		$ret_val = parent::save();

		if ( is_wp_error( $ret_val ) ) {
			$wpdb->query( 'ROLLBACK' );

			return $ret_val;
		}

		$wpdb->query( 'COMMIT' );

		return $ret_val;
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
	 * Get edit URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_edit_url() {
		return admin_url( 'admin.php?page=eac-banking&tab=transfers&action=edit&id=' . $this->id );
	}

	/**
	 * Get view URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_view_url() {
		return admin_url( 'admin.php?page=eac-banking&tab=transfers&action=view&id=' . $this->id );
	}
}
