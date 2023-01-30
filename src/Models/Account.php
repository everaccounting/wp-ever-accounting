<?php

namespace EverAccounting\Models;

use EverAccounting\Traits\Attachment;
use EverAccounting\Traits\CurrencyTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Class Account.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Account extends Model {
	use CurrencyTrait;
	use Attachment;

	/**
	 * Table name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table_name = 'ea_accounts';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'account';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'name'            => '',
		'number'          => '',
		'currency_code'   => '',
		'opening_balance' => 0.0000,
		'bank_name'       => null,
		'bank_phone'      => null,
		'bank_address'    => null,
		'thumbnail_id'    => null,
		'status'          => 'active',
		'creator_id'      => null,
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
		$code = empty( $this->get_currency_code() ) ? 'USD' : $this->get_currency_code();
		$this->set_prop( 'opening_balance', eaccounting_format_decimal( $amount, 4 ) );
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
	 * @param string $currency_code Bank currency code.
	 *
	 * @since 1.1.0
	 */
	public function set_currency_code( $currency_code ) {
		$this->set_prop( 'currency_code', strtoupper( $currency_code ) );
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
	 * Get the thumbnail id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return int
	 */
	public function get_thumbnail_id( $context = 'edit' ) {
		return $this->get_prop( 'thumbnail_id', $context );
	}

	/**
	 * Set the thumbnail id.
	 *
	 * @param int $thumbnail_id Thumbnail id.
	 *
	 * @since 1.1.0
	 */
	public function set_thumbnail_id( $thumbnail_id ) {
		$this->set_prop( 'thumbnail_id', absint( $thumbnail_id ) );
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
	 * Set the category enabled status.
	 *
	 * @param string $value Category enabled status.
	 *
	 * @since 1.0.2
	 */
	public function set_enabled( $value ) {
		$this->set_prop( 'enabled', $this->string_to_int( $value ) );
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
	 * get the creator id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return int
	 */
	public function get_creator_id( $context = 'edit' ) {
		return $this->get_prop( 'creator_id', $context );
	}

	/**
	 * set the creator id.
	 *
	 * @param int $creator_id creator id.
	 */
	public function set_creator_id( $creator_id ) {
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}

	/**
	 * get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * set the date created.
	 *
	 * @param string $date_created date created.
	 */
	public function set_date_created( $date_created ) {
		$this->set_date_prop( 'date_created', $date_created );
	}

	/**
	 * get balance.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_balance( $context = 'edit' ) {
		if ( is_null( $this->get_prop( 'balance' ) ) ) {
			global $wpdb;
			$transaction_total = (float) $wpdb->get_var(
				$wpdb->prepare( "SELECT SUM(CASE WHEN type='income' then amount WHEN type='expense' then - amount END) as total from {$wpdb->prefix}ea_transactions WHERE account_id=%d", $this->get_id() )
			);
			$balance           = $this->get_opening_balance() + $transaction_total;
			$this->set_balance( $balance );
		}

		return $this->get_prop( 'balance', $context );
	}

	/**
	 * set balance.
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
	| Helpers
	|--------------------------------------------------------------------------
	|
	| Helper methods.
	|
	*/
	/**
	 * Sanitizes the data.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true
	 */
	protected function sanitize_data() {
		// Required fields check.
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'missing-required', __( 'Account name is required.', 'wp-ever-accounting' ) );
		}

		// Number fields check.
		if ( empty( $this->get_number() ) ) {
			return new \WP_Error( 'missing-required', __( 'Account number is required.', 'wp-ever-accounting' ) );
		}

		// Currency fields check.
		if ( empty( $this->get_currency() ) ) {
			return new \WP_Error( 'missing-required', __( 'Account currency is required.', 'wp-ever-accounting' ) );
		}

		// Duplicate account number check.
		$account = $this->get( $this->get_number(), 'number' );
		if ( ! empty( $account ) && $account->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate-account-number', __( 'Account number already exists.', 'wp-ever-accounting' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_date_created() ) ) {
			$this->set_date_created( current_time( 'mysql' ) );
		}

		// Creator ID.
		if ( empty( $this->get_creator_id() ) && ! $this->exists() && is_user_logged_in() ) {
			$this->set_creator_id( get_current_user_id() );
		}

		return parent::sanitize_data();
	}
}
