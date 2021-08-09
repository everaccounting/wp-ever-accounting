<?php
/**
 * Handle the Transfer object.
 *
 * @package     EverAccounting
 * @class       Note
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transfer
 *
 * @package EverAccounting
 *
 * @property string $payment_date
 * @property int $from_account_id
 * @property float $amount
 * @property int $to_account_id
 * @property int $income_id
 * @property int $expense_id
 * @property string $payment_method
 * @property string $reference
 * @property string $description
 * @property int $creator_id
 * @property string $date_created
 */
class Transfer extends Data {
	/**
	 * Transfer data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'payment_date'    => null,
		'from_account_id' => null,
		'amount'          => null,
		'to_account_id'   => null,
		'income_id'       => null,
		'expense_id'      => null,
		'payment_method'  => null,
		'reference'       => null,
		'description'     => null,
		'creator_id'      => null,
		'date_created'    => null,
		'default_amount'  => 0,
		'currency_code'   => '', // protected
		'currency_rate'   => 0.00, // protected
	);

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data_type = array(
		'id'           => '%d',
		'income_id'    => '%d',
		'expense_id'   => '%d',
		'creator_id'   => '%d',
		'date_created' => '%s',
	);

	/**
	 * Transfer category id
	 *
	 * @var int
	 */
	protected $category_id;

	/**
	 * Transfer constructor.
	 *
	 * Get the transfer if id is passed, otherwise the transfer is new and empty.
	 *
	 * @param int|object|array|Transfer $transfer object to read.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $transfer = 0 ) {
		parent::__construct();
		if ( $transfer instanceof self ) {
			$this->set_id( $transfer->get_id() );
		} elseif ( is_object( $transfer ) && ! empty( $transfer->id ) ) {
			$this->set_id( $transfer->id );
		} elseif ( is_array( $transfer ) && ! empty( $transfer['id'] ) ) {
			$this->set_props( $transfer );
		} elseif ( is_numeric( $transfer ) ) {
			$this->set_id( $transfer );
		} else {
			$this->set_object_read( true );
		}

		$data = self::get_raw( $this->get_id() );
		if ( $data ) {
			$this->set_props( $data );
			$this->set_object_read( true );
		} else {
			$this->set_id( 0 );
		}
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
	 * Retrieve the object from database instance.
	 *
	 * @param int $transfer_id Object id.
	 * @param string $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_raw( $transfer_id, $field = 'id' ) {
		global $wpdb;

		$transfer_id = (int) $transfer_id;
		if ( ! $transfer_id ) {
			return false;
		}

		$transfer = wp_cache_get( $transfer_id, 'ea_transfers' );

		if ( ! $transfer ) {
			$transfer = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT transfer.*,
					       income.account_id  to_account_id,
					       expense.amount,
					       expense.currency_code,
					       expense.currency_rate,
					       expense.account_id from_account_id,
					       expense.reference,
					       expense.payment_date,
					       expense.description,
					       expense.payment_method,
					       expense.amount/expense.currency_rate as default_amount
					FROM   {$wpdb->prefix}ea_transfers transfer
					       INNER JOIN {$wpdb->prefix}ea_transactions expense
					               ON ( expense.id = transfer.expense_id )
					       INNER JOIN {$wpdb->prefix}ea_transactions income
					               ON ( income.id = transfer.income_id )
					WHERE  transfer.id = %d
					LIMIT  1",
					$transfer_id
				)
			);

			if ( ! $transfer ) {
				return false;
			}
			wp_cache_add( $transfer->id, $transfer, 'ea_transfers' );
		}

		return apply_filters( 'eaccounting_transfer_result', $transfer );
	}

	/**
	 *  Insert an item in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function insert( $args = array() ) {
		global $wpdb;
		$data_arr = $this->to_array();
		$data     = wp_array_slice_assoc( $data_arr, array_keys( $this->data_type ) );
		$format   = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data     = wp_unslash( $data );

		$expense = array(
			[
				'type'           => 'transfer',
				'payment_date'   => $this->payment_date,
				'amount'         => $this->amount,
				'currency_code'  => $this->from_currency_code, // protected
				'currency_rate'  => $this->from_currency_rate, // protected
				'account_id'     => $this->from_account_id,
				'description'    => $this->description,
				'payment_method' => $this->payment_method,
				'reference'      => $this->reference,
				'creator_id'     => $this->creator_id,
				'date_created'   => $this->date_created,
			],
		);

		$amount = $this->amount;
		if ( $this->from_currency_code !== $this->to_currency_code ) {
			$amount = eaccounting_price_convert( $amount, $this->from_currency_code, $this->to_currency_code, $this->from_currency_rate, $this->to_currency_rate );
		}

		$income = array(
			[
				'type'           => 'transfer',
				'payment_date'   => $this->payment_date,
				'amount'         => $amount,
				'currency_code'  => $this->to_currency_code, // protected
				'currency_rate'  => $this->to_currency_rate, // protected
				'account_id'     => $this->from_account_id,
				'description'    => $this->description,
				'payment_method' => $this->payment_method,
				'reference'      => $this->reference,
				'creator_id'     => $this->creator_id,
				'date_created'   => $this->date_created,
			],
		);

		var_dump( $expense );
		var_dump( $income );

		return true;
	}

	/**
	 *  Update an object in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function update( $args = array() ) {

		return true;
	}

	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	public function save() {
		global $wpdb;
		$user_id = get_current_user_id();
		$fields  = array(
			'id'           => '%d',
			'income_id'    => '%d',
			'expense_id'   => '%d',
			'creator_id'   => '%d',
			'date_created' => '%s',
		);

		if ( empty( $this->get_prop( 'amount' ) ) ) {
			return new \WP_Error( 'empty_transfer_amount', esc_html__( 'Transfer amount is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'from_account_id' ) ) ) {
			return new \WP_Error( 'empty_transfer_from_account_id', esc_html__( 'Transfer from account is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'to_account_id' ) ) ) {
			return new \WP_Error( 'empty_transfer_to_account_id', esc_html__( 'Transfer to account is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'payment_method' ) ) ) {
			return new \WP_Error( 'empty_transfer_payment_method', esc_html__( 'Transfer method is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'payment_date' ) ) || '0000-00-00 00:00:00' === $this->get_prop( 'payment_date' ) ) {
			$this->set_date_prop( 'payment_date', current_time( 'mysql' ) );
		}

		if ( empty( $this->get_prop( 'date_created' ) ) || '0000-00-00 00:00:00' === $this->get_prop( 'date_created' ) ) {
			$this->set_date_prop( 'date_created', current_time( 'mysql' ) );
		}

		if ( empty( $this->get_prop( 'creator_id' ) ) ) {
			$this->set_prop( 'creator_id', $user_id );
		}

		$from_account = new Account( $this->from_account_id );
		if ( ! $from_account->exists() || empty( $from_account->currency_code ) ) {
			return new \WP_Error( 'invalid_transfer_account_id', esc_html__( 'Transfer associated from account does not exist', 'wp-ever-accounting' ) );
		}

		$from_currency = new Currency( $from_account->currency_code );
		if ( ! $from_currency->exists() || empty( $from_currency->rate ) ) {
			return new \WP_Error( 'invalid_transfer_account_currency', esc_html__( 'Transaction associated account currency does not exist', 'wp-ever-accounting' ) );
		}

		$this->set_prop( 'from_currency_code', $from_account->currency_code );
		$this->set_prop( 'from_currency_rate', $from_currency->rate );

		$to_account = new Account( $this->to_account_id );
		if ( ! $to_account->exists() || empty( $to_account->currency_code ) ) {
			return new \WP_Error( 'invalid_transfer_account_id', esc_html__( 'Transfer associated to account does not exist', 'wp-ever-accounting' ) );
		}

		$to_currency = new Currency( $to_account->currency_code );
		if ( ! $to_currency->exists() || empty( $to_currency->rate ) ) {
			return new \WP_Error( 'invalid_transfer_account_currency', esc_html__( 'Transaction associated account currency does not exist', 'wp-ever-accounting' ) );
		}

		$this->set_prop( 'to_currency_code', $to_account->currency_code );
		$this->set_prop( 'to_currency_rate', $to_currency->rate );

		$common_data = array(
			'payment_method' => $this->get_prop( 'payment_method' ),
			'description'    => $this->get_prop( 'description' ),
			'reference'      => $this->get_prop( 'reference' ),
			'payment_date'   => $this->get_prop( 'payment_date' ),
		);

		$wpdb->query( 'START TRANSACTION' );

		if ( $this->exists() ) {
			$is_error = $this->update( $fields );
		} else {
			$is_error = $this->insert( $fields );
		}

		$wpdb->query( 'ROLLBACK' );

		return true;

	}

	/**
	 * Deletes a transfer from database.
	 *
	 * @return array|false true on success, false on failure.
	 * @since 1.1.0
	 */
	public function delete() {
		global $wpdb;
		if ( ! $this->exists() || ! $this->income_id || ! $this->expense_id ) {
			return false;
		}

		$data = $this->to_array();

		/**
		 * Filters whether a transfer delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $transfer_id Transfer id.
		 * @param array $data Transfer data array.
		 * @param Transfer $transfer Transfer object.
		 *
		 * @since 1.2.1
		 */
		$check = apply_filters( 'eaccounting_check_delete_transfer', null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before a transfer is deleted.
		 *
		 * @param int $transfer_id Transfer id.
		 * @param array $data Transfer data array.
		 * @param Transfer $transfer Transfer object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_delete_transfer', $this->get_id(), $data, $this );

		$result = $wpdb->delete( $wpdb->prefix . 'ea_transfers', array( 'id' => $this->get_id() ) );
		if ( ! $result ) {
			return false;
		}

		$result = $wpdb->delete( $wpdb->prefix . 'ea_transactions', array( 'id' => $this->income_id ) );
		if ( ! $result ) {
			return false;
		}

		$result = $wpdb->delete( $wpdb->prefix . 'ea_transactions', array( 'id' => $this->expense_id ) );
		if ( ! $result ) {
			return false;
		}

		/**
		 * Fires after a transfer is deleted.
		 *
		 * @param int $transfer_id Transfer id.
		 * @param array $data Transfer data array.
		 * @param Transfer $transfer Transfer object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_delete_transfer', $this->get_id(), $data );

		// Clear object.
		wp_cache_delete( $this->get_id(), 'ea_transfers' );
		wp_cache_delete( $this->expense_id, 'ea_transactions' );
		wp_cache_delete( $this->income_id, 'ea_transactions' );
		// todo delete transaction metadata.
		wp_cache_set( 'last_changed', microtime(), 'ea_transactions' );
		wp_cache_set( 'last_changed', microtime(), 'ea_transactionmeta' );
		wp_cache_set( 'last_changed', microtime(), 'ea_transfers' );
		$this->set_id( 0 );
		$this->set_defaults();

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Functions for getting item data. Getter methods won't change anything unless
	| just returning from the props.
	|
	*/

	/**
	 * Transaction payment date.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_payment_date() {
		return $this->get_prop( 'payment_date' );
	}

	/**
	 * Transaction payment date.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_date() {
		return $this->get_payment_date();
	}

	/**
	 * Transaction payment methods.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_from_account_id() {
		return $this->get_prop( 'from_account_id' );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_amount() {
		return $this->get_prop( 'amount' );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_to_account_id() {
		return $this->get_prop( 'to_account_id' );
	}

	/**
	 * Income ID.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_income_id() {
		return $this->get_prop( 'income_id' );
	}

	/**
	 * Expense ID.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_expense_id() {
		return $this->get_prop( 'expense_id' );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_payment_method() {
		return $this->get_prop( 'payment_method' );
	}

	/**
	 * Description.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_reference() {
		return $this->get_prop( 'reference' );
	}

	/**
	 * Description.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_description() {
		return $this->get_prop( 'description' );
	}

	/**
	 * Return object created by.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_creator_id() {
		return $this->get_prop( 'creator_id' );
	}

	/**
	 * Get object created date.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_date_created() {
		return $this->get_prop( 'date_created' );
	}

	/**
	 * Get default amount.
	 *
	 * @return float
	 * @since 1.0.2
	 */
	public function get_default_amount() {
		return $this->get_prop( 'default_amount' );
	}

	/**
	 * get currency_code.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_currency_code() {
		return $this->get_prop( 'currency_code' );
	}

	/**
	 * get currency_rate.
	 *
	 * @return float
	 * @since 1.0.2
	 */
	public function get_currency_rate() {
		return $this->get_prop( 'currency_rate' );
	}

	/**
	 * Get transfer category.
	 *
	 * @return integer
	 * @since 1.1.0
	 */
	public function get_category_id() {
		return absint( $this->category_id );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set date.
	 *
	 * @param string $payment_date Payment date
	 *
	 * @since 1.0.2
	 */
	public function set_payment_date( $payment_date ) {
		$this->set_date_prop( 'payment_date', eaccounting_clean( $payment_date ) );
	}

	/**
	 * Set from account id.
	 *
	 * @param int $account_id From account id
	 *
	 * @since 1.0.2
	 */
	public function set_from_account_id( $account_id ) {
		$this->set_prop( 'from_account_id', absint( $account_id ) );
	}

	/**
	 * Set amount.
	 *
	 * @param string $amount Amount
	 *
	 * @since 1.0.2
	 */
	public function set_amount( $amount ) {
		$this->set_prop( 'amount', (float) $amount );
	}

	/**
	 * Set to account id.
	 *
	 * @param int $account_id To account id
	 *
	 * @since 1.0.2
	 */
	public function set_to_account_id( $account_id ) {
		$this->set_prop( 'to_account_id', absint( $account_id ) );
	}

	/**
	 * Set income id.
	 *
	 * @param int $value income_id.
	 *
	 * @since 1.0.2
	 */
	public function set_income_id( $value ) {
		$this->set_prop( 'income_id', absint( $value ) );
	}

	/**
	 * Set expense id.
	 *
	 * @param int $value expense_id.
	 *
	 * @since 1.0.2
	 */
	public function set_expense_id( $value ) {
		$this->set_prop( 'expense_id', absint( $value ) );
	}

	/**
	 * Set payment method.
	 *
	 * @param string $payment_method Payment method
	 *
	 * @since 1.0.2
	 */
	public function set_payment_method( $payment_method ) {
		$this->set_prop( 'payment_method', eaccounting_clean( $payment_method ) );
	}

	/**
	 * Set reference.
	 *
	 * @param string $value Reference
	 *
	 * @since 1.0.2
	 */
	public function set_reference( $value ) {
		$this->set_prop( 'reference', eaccounting_clean( $value ) );
	}

	/**
	 * Set description.
	 *
	 * @param string $value Transfer description
	 *
	 * @since 1.0.2
	 */
	public function set_description( $value ) {
		$this->set_prop( 'description', eaccounting_clean( $value ) );
	}

	/**
	 * Set object creator id.
	 *
	 * @param int $creator_id Creator id
	 *
	 * @since 1.0.2
	 */
	public function set_creator_id( $creator_id = null ) {
		if ( null === $creator_id ) {
			$creator_id = get_current_user_id();
		}
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}

	/**
	 * Set object created date.
	 *
	 * @param string $date Creation date
	 *
	 * @since 1.0.2
	 */
	public function set_date_created( $date = null ) {
		if ( null === $date ) {
			$date = current_time( 'mysql' );
		}
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Set default amount.
	 *
	 * @param string $amount Amount
	 *
	 * @since 1.2.1
	 */
	public function set_default_amount( $amount ) {
		$this->set_prop( 'default_amount', (float) $amount );
	}

	/**
	 * Set currency code.
	 *
	 * @param string $value Currency code
	 *
	 * @since 1.2.1
	 */
	public function set_currency_code( $value ) {
		$this->set_prop( 'currency_code', eaccounting_sanitize_currency_code( $value ) );
	}

	/**
	 * Set currency rate.
	 *
	 * @param float $value Currency rate
	 *
	 * @since 1.2.1
	 */
	public function set_currency_rate( $value ) {
		$this->set_prop( 'currency_rate', (float) $value );
	}
}
