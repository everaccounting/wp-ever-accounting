<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Account.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Account extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_accounts';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'account';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_accounts';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'name'            => '',
		'type'            => 'bank',
		'number'          => '',
		'opening_balance' => 0.0000,
		'bank_name'       => null,
		'bank_phone'      => null,
		'bank_address'    => null,
		'status'          => 'active',
		'currency_code'   => 'USD',
		'creator_id'      => null,
		'updated_at'      => null,
		'created_at'      => null,
	);

	/**
	 * Extra data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $extra_data = array(
		'balance' => null,
	);

	/**
	 * Model constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$this->core_data['currency_code'] = eac_get_default_currency();
		parent::__construct( $data );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data.
	|
	*/
	/**
	 * Return the account name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set account name.
	 *
	 * @param string $name Account name.
	 *
	 * @since 1.1.0
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', sanitize_text_field( $name ) );
	}

	/**
	 * Return the account type.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Set account type.
	 *
	 * @param string $type Account type.
	 *
	 * @since 1.1.0
	 */
	public function set_type( $type ) {
		$this->set_prop( 'type', sanitize_text_field( $type ) );
	}

	/**
	 * Returns the account number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_number( $context = 'edit' ) {
		return $this->get_prop( 'number', $context );
	}

	/**
	 * Set the account number.
	 *
	 * @param string $number bank account number.
	 *
	 * @since 1.1.0
	 */
	public function set_number( $number ) {
		$this->set_prop( 'number', sanitize_text_field( $number ) );
	}

	/**
	 * Returns account opening balance.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_opening_balance( $context = 'edit' ) {
		return $this->get_prop( 'opening_balance', $context );
	}

	/**
	 * Returns account opening balance.
	 *
	 * @param string $amount account opening balance.
	 *
	 * @since 1.1.0
	 */
	public function set_opening_balance( $amount ) {
		$this->set_prop( 'opening_balance', eac_format_decimal( $amount, 4 ) );
	}

	/**
	 * Returns account currency code.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Set account currency code.
	 *
	 * @param string $currency Bank currency code.
	 *
	 * @since 1.1.0
	 */
	public function set_currency_code( $currency ) {
		$this->set_prop( 'currency_code', strtoupper( $currency ) );
	}

	/**
	 * Return account bank name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_bank_name( $context = 'edit' ) {
		return $this->get_prop( 'bank_name', $context );
	}

	/**
	 * Set account bank name.
	 *
	 * @param string $bank_name name of the bank.
	 *
	 * @since 1.1.0
	 */
	public function set_bank_name( $bank_name ) {
		$this->set_prop( 'bank_name', sanitize_text_field( $bank_name ) );
	}

	/**
	 * Return account bank phone number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_bank_phone( $context = 'edit' ) {
		return $this->get_prop( 'bank_phone', $context );
	}

	/**
	 * Set account bank phone number.
	 *
	 * @param string $bank_phone Bank phone number.
	 *
	 * @since 1.1.0
	 */
	public function set_bank_phone( $bank_phone ) {
		$this->set_prop( 'bank_phone', sanitize_text_field( $bank_phone ) );
	}

	/**
	 * Return account bank address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_bank_address( $context = 'edit' ) {
		return $this->get_prop( 'bank_address', $context );
	}

	/**
	 * Set account bank address.
	 *
	 * @param string $bank_address Bank physical address.
	 *
	 * @since 1.1.0
	 */
	public function set_bank_address( $bank_address ) {
		$this->set_prop( 'bank_address', sanitize_textarea_field( $bank_address ) );
	}

	/**
	 * Get the category status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Set the category status.
	 *
	 * @param string $value Category status.
	 *
	 * @since 1.0.2
	 */
	public function set_status( $value ) {
		if ( in_array( $value, array( 'active', 'inactive' ), true ) ) {
			$this->set_prop( 'status', $value );
		}
	}

	/**
	 * Is the category enabled?
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return 'active' === $this->get_status();
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

	/**
	 * Get balance.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_balance( $context = 'edit' ) {
		if ( is_null( $this->get_prop( 'balance' ) ) ) {
			global $wpdb;
			$table             = $wpdb->prefix . 'ea_transactions';
			$transaction_total = (float) $wpdb->get_var(
				$wpdb->prepare( "SELECT SUM(CASE WHEN type='payment' then amount WHEN type='expense' then - amount END) as total from {$table} WHERE account_id=%d AND currency_code=%s", $this->get_id(), $this->get_currency_code() )
			);
			$balance           = $this->get_opening_balance() + $transaction_total;
			$this->set_balance( $balance );
		}

		return $this->get_prop( 'balance', $context );
	}

	/**
	 * Set balance.
	 *
	 * @param string $balance balance.
	 *
	 * @since 1.0.0
	 */
	public function set_balance( $balance ) {
		$this->set_prop( 'balance', $balance );
	}

	/*
	|--------------------------------------------------------------------------
	| Non-CRUD Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data which is not directly related to
	| the object's database row. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		// Required fields check.
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'missing_required', __( 'Account name is required.', 'wp-ever-accounting' ) );
		}

		// Number fields check.
		if ( empty( $this->get_number() ) ) {
			return new \WP_Error( 'missing_required', __( 'Account number is required.', 'wp-ever-accounting' ) );
		}

		// Currency fields check.
		if ( empty( $this->get_currency_code() ) ) {
			return new \WP_Error( 'missing_required', __( 'Account currency is required.', 'wp-ever-accounting' ) );
		}

		// Duplicate account number check.
		$account = $this->get( $this->get_number(), 'number' );
		if ( ! empty( $account ) && $account->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate_account_number', __( 'Account number already exists.', 'wp-ever-accounting' ) );
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

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

	/**
	 * Get formatted balance.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function get_formatted_balance() {
		return eac_format_price( $this->get_balance(), $this->get_currency_code() );
	}

	/**
	 * Get formatted name.
	 *
	 * @return string
	 * @since 1.1.6
	 */
	public function get_formatted_name() {
		$name   = sprintf( '%s (%s)', $this->get_name(), $this->get_currency_code() );
		$number = $this->get_number();

		return $number ? sprintf( '%s - %s', $number, $name ) : $name;
	}
}
