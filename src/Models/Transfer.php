<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transfer.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Transfer extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_transfers';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'transfer';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_transfers';


	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'payment_id'   => null,
		'expense_id'   => null,
		'creator_id'   => null,
		'date_created' => null,
	);

	/**
	 * Extra data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $extra_data = array(
		'date'            => null,
		'from_account_id' => null,
		'amount'          => null,
		'to_account_id'   => null,
		'payment_method'  => null,
		'reference'       => null,
		'note'            => null,
		'creator_id'      => null,
		'updated_at'      => null,
		'created_at'      => null,
	);

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data.
	|
	*/
	/**
	 * Payment ID.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_payment_id( $context = 'edit' ) {
		return $this->get_prop( 'payment_id', $context );
	}

	/**
	 * Set payment ID.
	 *
	 * @param int $value Payment ID.
	 *
	 * @since 1.0.2
	 */
	public function set_payment_id( $value ) {
		$this->set_prop( 'payment_id', absint( $value ) );
	}

	/**
	 * Expense ID.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_expense_id( $context = 'edit' ) {
		return $this->get_prop( 'expense_id', $context );
	}

	/**
	 * Set expense ID.
	 *
	 * @param int $value Expense ID.
	 *
	 * @since 1.0.2
	 */
	public function set_expense_id( $value ) {
		$this->set_prop( 'expense_id', absint( $value ) );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_from_account_id( $context = 'edit' ) {
		return $this->get_prop( 'from_account_id', $context );
	}

	/**
	 * Set from account ID.
	 *
	 * @param int $value From account ID.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function set_from_account_id( $value ) {
		$this->set_prop( 'from_account_id', absint( $value ) );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_to_account_id( $context = 'edit' ) {
		return $this->get_prop( 'to_account_id', $context );
	}

	/**
	 * Set to account ID.
	 *
	 * @param int $value To account ID.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function set_to_account_id( $value ) {
		$this->set_prop( 'to_account_id', absint( $value ) );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_amount( $context = 'edit' ) {
		return $this->get_prop( 'amount', $context );
	}

	/**
	 * Set amount.
	 *
	 * @param float $value Amount.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function set_amount( $value ) {
		$this->set_prop( 'amount', (float) $value );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return \EverAccounting\DateTime
	 */
	public function get_date( $context = 'edit' ) {
		return $this->get_prop( 'date', $context );
	}

	/**
	 * Set date.
	 *
	 * @param string $value Date.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function set_date( $value ) {
		$this->set_date_prop( 'date', $value, get_option( 'date_format' ) );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_payment_method( $context = 'edit' ) {
		return $this->get_prop( 'payment_method', $context );
	}

	/**
	 * Set payment method.
	 *
	 * @param string $value Expense method.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function set_payment_method( $value ) {
		$this->set_prop( 'payment_method', $value );
	}

	/**
	 * Description.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_reference( $context = 'edit' ) {
		return $this->get_prop( 'reference', $context );
	}

	/**
	 * Set reference.
	 *
	 * @param string $value Reference.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function set_reference( $value ) {
		$this->set_prop( 'reference', $value );
	}

	/**
	 * Description.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_note( $context = 'edit' ) {
		return $this->get_prop( 'note', $context );
	}

	/**
	 * Set description.
	 *
	 * @param string $value Description.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function set_note( $value ) {
		$this->set_prop( 'note', $value );
	}

	/**
	 * get currency.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_currency( $context = 'edit' ) {
		return $this->get_prop( 'currency', $context );
	}

	/**
	 * Get transfer category.
	 *
	 * @since 1.1.0
	 *
	 * @return integer
	 */
	public function get_category_id() {
		return $this->get_prop( 'category_id' );
	}

	/**
	 * Set transfer category.
	 *
	 * @param integer $category_id Category ID.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', absint( $category_id ) );
	}

	/**
	 * Get the creator id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return int
	 */
	public function get_creator_id( $context = 'edit' ) {
		return $this->get_prop( 'creator_id', $context );
	}

	/**
	 * Set the creator id.
	 *
	 * @param int $creator_id creator id.
	 */
	public function set_creator_id( $creator_id ) {
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}

	/**
	 * Get the date updated.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_updated_at( $context = 'edit' ) {
		return $this->get_prop( 'updated_at', $context );
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $updated_at date updated.
	 */
	public function set_updated_at( $updated_at ) {
		$this->set_date_prop( 'updated_at', $updated_at );
	}

	/**
	 * Get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_created_at( $context = 'edit' ) {
		return $this->get_prop( 'created_at', $context );
	}

	/**
	 * Set the date created.
	 *
	 * @param string $created_at date created.
	 */
	public function set_created_at( $created_at ) {
		$this->set_date_prop( 'created_at', $created_at );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete documents from the database.
	| Written in abstract fashion so that the way documents are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/
	/**
	 * Prepare join query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_join_query( $clauses, $args = array() ) {
		global $wpdb;
		$clauses          = parent::prepare_join_query( $clauses, $args );
		$clauses['join'] .= " LEFT JOIN {$wpdb->prefix}ea_transactions AS payment ON {$this->table_alias}.payment_id = payment.id";
		$clauses['join'] .= " LEFT JOIN {$wpdb->prefix}ea_transactions AS expense ON {$this->table_alias}.expense_id = expense.id";

		// If selected all fields, then select all fields from payment and expense tables.
		if ( strpos( $clauses['select'], '*' ) !== false ) {
			$clauses['select'] .= ', payment.account_id AS from_account_id';
			$clauses['select'] .= ', expense.account_id AS to_account_id, expense.amount, expense.payment_date date, expense.payment_method, expense.payment_note, expense.reference';
		}

		return $clauses;
	}


	/**
	 * Retrieve the object from database instance.
	 *
	 * @since 1.0.0
	 *
	 * @return object|false Object, false otherwise.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	protected function read() {
		$transfer = parent::read();
		if ( $transfer ) {
			$payment = Payment::get( $this->get_payment_id() );
			$expense = Expense::get( $this->get_expense_id() );
			if ( $payment && $expense ) {
				$this->data['from_account_id'] = $payment->get_account_id();
				$this->data['to_account_id']   = $expense->get_account_id();
				$this->data['amount']          = eac_format_decimal( $expense->get_amount() );
				$this->data['date']            = $expense->get_payment_date();
				$this->data['payment_method']  = $expense->get_payment_method();
				$this->data['note']            = $expense->get_payment_note();
				$this->data['reference']       = $expense->get_reference();
			}
		}

		return $transfer;
	}

	/**
	 * Deletes the object from database.
	 *
	 * @since 1.0.0
	 * @return array|false true on success, false on failure.
	 */
	public function delete() {
		$payment = Payment::get( $this->get_payment_id() );
		$expense = Expense::get( $this->get_expense_id() );
		$deleted = parent::delete();
		if ( $deleted && $payment && $expense ) {
			$payment->delete();
			$expense->delete();
		} else {
			$deleted = false;
		}

		return $deleted;
	}

	/**
	 * Saves an object in the database.
	 *
	 * @throws \Exception When the object cannot be saved.
	 * @since 1.0.0
	 *
	 * @return static|\WP_Error Object instance on success, WP_Error on failure.
	 */
	public function save() {
		global $wpdb;
		// Required fields are date, amount, from_account_id, to_account_id, payment_method.
		if ( ! $this->get_date() ) {
			return new \WP_Error( 'missing_required', __( 'Transfer date is required.', 'wp-ever-accounting' ) );
		}

		if ( ! $this->get_amount() ) {
			return new \WP_Error( 'missing_required', __( 'Transfer amount is required.', 'wp-ever-accounting' ) );
		}

		if ( ! $this->get_from_account_id() ) {
			return new \WP_Error( 'missing_required', __( 'Transfer from account is required.', 'wp-ever-accounting' ) );
		}

		if ( ! $this->get_to_account_id() ) {
			return new \WP_Error( 'missing_required', __( 'Transfer to account is required.', 'wp-ever-accounting' ) );
		}

		// Check if from account and to account is same.
		if ( $this->get_from_account_id() === $this->get_to_account_id() ) {
			return new \WP_Error( 'invalid_data', __( 'Transfer from account and to account can not be same.', 'wp-ever-accounting' ) );
		}
		// Creator ID.
		if ( empty( $this->get_creator_id() ) && ! $this->exists() && is_user_logged_in() ) {
			$this->set_creator_id( get_current_user_id() );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_updated_at( current_time( 'mysql' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_created_at() ) ) {
			$this->set_created_at( current_time( 'mysql' ) );
		}

		try {
			$wpdb->query( 'START TRANSACTION' );
			$from_account = Account::get( $this->get_from_account_id() );
			$to_account   = Account::get( $this->get_to_account_id() );
			if ( ! $from_account ) {
				return new \WP_Error( 'invalid_data', __( 'Transfer from account does not exists.', 'wp-ever-accounting' ) );
			}
			if ( ! $to_account ) {
				return new \WP_Error( 'invalid_data', __( 'Transfer to account does not exists.', 'wp-ever-accounting' ) );
			}
			$expense = new Expense( $this->get_expense_id() );
			$expense->set_props(
				array(
					'account_id'     => $this->get_from_account_id(),
					'payment_date'   => $this->get_date(),
					'amount'         => $this->get_amount(),
					'description'    => $this->get_note(),
					'payment_method' => $this->get_payment_method(),
					'reference'      => $this->get_reference(),
				)
			);
			$expense_saved = $expense->save();
			if ( is_wp_error( $expense_saved ) ) {
				/* translators: %s: error message */
				throw new \Exception( sprintf( __( 'Transfer expense could not be saved. %s', 'wp-ever-accounting' ), $expense_saved->get_error_message() ) );
			}
			$this->set_expense_id( $expense->get_id() );

			$amount = $this->get_amount();
			if ( $from_account->get_currency() !== $to_account->get_currency() ) {
				$expense_currency_rate = eac_get_currency_rate( $from_account->get_currency() );
				$payment_currency_rate = eac_get_currency_rate( $to_account->get_currency() );
				$amount                = eac_convert_price( $amount, $from_account->get_currency(), $to_account->get_currency(), $expense_currency_rate, $payment_currency_rate );
			}

			$payment = new Payment( $this->get_payment_id() );
			$payment->set_props(
				array(
					'account_id'     => $this->get_to_account_id(),
					'payment_date'   => $this->get_date(),
					'amount'         => $amount,
					'description'    => $this->get_note(),
					'payment_method' => $this->get_payment_method(),
					'reference'      => $this->get_reference(),
				)
			);
			$payment_saved = $payment->save();
			if ( is_wp_error( $payment_saved ) ) {
				/* translators: %s: error message */
				throw new \Exception( sprintf( __( 'Transfer payment could not be saved. %s', 'wp-ever-accounting' ), $payment_saved->get_error_message() ) );
			}
			$this->set_payment_id( $payment->get_id() );

			$saved = parent::save();
			if ( is_wp_error( $saved ) ) {
				throw new \Exception( $saved->get_error_message() );
			}
			$wpdb->query( 'COMMIT' );
		} catch ( \Exception $e ) {
			$wpdb->query( 'ROLLBACK' );
			$saved = new \WP_Error( 'invalid_data', $e->getMessage() );
		}

		return $saved;
	}

	/*
	|--------------------------------------------------------------------------
	| Extra
	|--------------------------------------------------------------------------
	*/
	/**
	 * Get formatted transaction amount.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_formatted_amount() {
		return eac_format_money( $this->get_amount(), $this->get_currency() );
	}
}
