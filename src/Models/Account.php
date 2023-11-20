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
	public $table_name = 'ea_accounts';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'account';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'id'              => null,
		'name'            => '', // Unique and required.
		'type'            => 'bank', //
		'number'          => '',
		'opening_balance' => 0.0000,
		'bank_name'       => null,
		'bank_phone'      => null,
		'bank_address'    => null,
		'currency_code'   => 'USD',
		'status'          => 'active',
		'uuid'            => null,
		'creator_id'      => null,
		'date_updated'    => null,
		'date_created'    => null,
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
		$this->core_data['currency_code'] = eac_get_base_currency();
		$this->core_data['uuid']          = wp_generate_uuid4();
		$this->core_data['creator_id']    = get_current_user_id();
		$this->core_data['date_created']  = current_time( 'mysql' );
		parent::__construct( $data );
	}

	/**
	 * When the object is cloned, make sure meta is duplicated correctly.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		parent::__clone();
		$this->set_number( '' );
		$this->set_name( $this->get_name() . ' ' . __( '(Copy)', 'wp-ever-accounting' ) );
		$this->set_uuid( wp_generate_uuid4() );
		$this->set_date_updated( null );
		$this->set_date_created( current_time( 'mysql' ) );
	}

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
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 * @since 1.0.0
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
		$account = $this->find(
			[
				'number' => $this->get_number(),
			]
		);
		if ( ! empty( $account ) && $account->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate_account_number', __( 'Account number already exists.', 'wp-ever-accounting' ) );
		}

		// Creator ID.
		if ( empty( $this->get_creator_id() ) && ! $this->exists() && is_user_logged_in() ) {
			$this->set_creator_id( get_current_user_id() );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_date_updated( current_time( 'mysql' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_date_created() ) ) {
			$this->set_date_created( current_time( 'mysql' ) );
		}
		// If uuid is not set, set it to now.
		if ( empty( $this->get_uuid() ) ) {
			$this->set_uuid( wp_generate_uuid4() );
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	| Methods for getting and setting data.
	|
	*/

	/**
	 * Get id.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function get_id() {
		return (int) $this->get_prop( 'id' );
	}

	/**
	 * Set id.
	 *
	 * @param int $id
	 *
	 * @since 1.0.0
	 */
	public function set_id( $id ) {
		$this->set_prop( 'id', absint( $id ) );
	}

	/**
	 * Return the account type.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
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
	 * Return the account name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
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
	 * Returns the account number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return mixed|null
	 * @since 1.1.0
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
	 * @return mixed|null
	 * @since 1.1.0
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
	 * @return mixed|null
	 * @since 1.1.0
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
	 * @return mixed|null
	 * @since 1.1.0
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
	 * @return mixed|null
	 * @since 1.1.0
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
	 * @return mixed|null
	 * @since 1.1.0
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
	 * @return string
	 * @since 1.0.2
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
	 * Get the unique_hash.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_uuid( $context = 'edit' ) {
		return $this->get_prop( 'uuid', $context );
	}

	/**
	 * Set the uuid.
	 *
	 * @param string $key uuid.
	 */
	public function set_uuid( $key ) {
		$this->set_prop( 'uuid', sanitize_key( $key ) );
	}

	/**
	 * Get created via.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_created_via( $context = 'view' ) {
		return $this->get_prop( 'created_via', $context );
	}

	/**
	 * Set created via.
	 *
	 * @param string $value Created via.
	 */
	public function set_created_via( $value ) {
		$this->set_prop( 'created_via', $value );
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
	public function get_date_updated( $context = 'edit' ) {
		return $this->get_prop( 'date_updated', $context );
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $date date updated.
	 */
	public function set_date_updated( $date ) {
		$this->set_date_prop( 'date_updated', $date );
	}

	/**
	 * Get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Set the date created.
	 *
	 * @param string $date date created.
	 */
	public function set_date_created( $date ) {
		$this->set_date_prop( 'date_created', $date );
	}

	/*
	|--------------------------------------------------------------------------
	| Extra data methods
	|--------------------------------------------------------------------------
	| These methods are used to retrieve and update extra data.
	*/

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
				$wpdb->prepare( "SELECT SUM(CASE WHEN type='payment' then amount WHEN type='expense' then - amount END) as total from $table WHERE account_id=%d AND currency_code=%s", $this->get_id(), $this->get_currency_code() )
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
	| Conditionals methods
	|--------------------------------------------------------------------------
	| Methods that check an object's status, typically based on internal or meta data.
	*/

	/**
	 * Is the category active?
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function is_active() {
		return 'active' === $this->get_status();
	}

	/**
	 * Check if order has been created via admin, checkout, or in another way.
	 *
	 * @param string $modes Created via.
	 *
	 * @since 1.5.6
	 * @return bool
	 */
	public function is_created_via( $modes ) {
		return $modes === $this->get_created_via();
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

	/**
	 * Get currency
	 *
	 * @return Currency
	 * @since 1.0.0
	 */
	public function get_currency() {
		return Currency::get( $this->get_currency_code() );
	}

	/**
	 * Get currency rate.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_exchange_rate() {
		$currency = $this->get_currency();
		if ( $currency ) {
			return $currency->get_exchange_rate();
		}

		return 1;
	}

	/**
	 * Get formatted balance.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_balance() {
		return eac_format_money( $this->get_balance(), $this->get_currency_code() );
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
