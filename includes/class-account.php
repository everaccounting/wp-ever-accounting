<?php
/**
 * Handle the Account object.
 *
 * @package     EverAccounting
 * @class       Account
 * @version     1.2.1
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Account object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 */
class Account {
	/**
	 * Account id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;


	/**
	 * Account name
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $name = '';
	/**
	 * Account number.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $number = '';
	/**
	 * Account opening balance.
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $opening_balance = 0.00;
	/**
	 * Account currency code.
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $currency_code = 0.00;
	/**
	 * Account bank name.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $bank_name = '';
	/**
	 * Account bank phone.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $bank_phone = '';
	/**
	 * Account Address.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $bank_address = '';
	/**
	 * Account thumbnail id.
	 *
	 * @since 1.2.1
	 * @var null
	 */
	public $thumbnail_id = null;

	/**
	 * Account status
	 *
	 * @since 1.2.1
	 * @var bool
	 */
	public $enabled = true;

	/**
	 * Account creator user id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $creator_id = 0;

	/**
	 * Account created date.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $date_created = '0000-00-00 00:00:00';

	/**
	 * Retrieve Account instance.
	 *
	 * @param int $account_id Account id.
	 *
	 * @return Account|false Account object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	public static function get_instance( $account_id ) {
		global $wpdb;

		$account_id = (int) $account_id;
		if ( ! $account_id ) {
			return false;
		}

		$_item = wp_cache_get( $account_id, 'ea_accounts' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_accounts WHERE id = %d LIMIT 1", $account_id ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_account( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_accounts' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_account( $_item, 'raw' );
		}

		return new Account( $_item );
	}

	/**
	 * Account constructor.
	 *
	 * @param $account
	 *
	 * @since 1.2.1
	 */
	public function __construct( $account ) {
		foreach ( get_object_vars( $account ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Account field to check if set.
	 *
	 * @return bool Whether the given Account field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		if ( isset( $this->$key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic method for setting account fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Account key.
	 * @param mixed $value Account value.
	 *
	 * @since 1.2.1
	 */
	public function __set( $key, $value ) {
		if ( is_callable( array( $this, 'set_' . $key ) ) ) {
			$this->$key( $value );
		} else {
			$this->$key = $value;
		}
	}

	/**
	 * Magic method for accessing custom fields.
	 *
	 * @param string $key Account field to retrieve.
	 *
	 * @return mixed Value of the given Account field (if set).
	 * @since 1.2.1
	 */
	public function __get( $key ) {

		if ( is_callable( array( $this, 'get_' . $key ) ) ) {
			$value = $this->$key();
		} else {
			$value = $this->$key;
		}

		return $value;
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key Account key to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		if ( isset( $this->$key ) ) {
			unset( $this->$key );
		}
	}

	/**
	 * Filter account object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Account|Object
	 * @since 1.2.1
	 *
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::get_instance( $this->id );
		}

		return eaccounting_sanitize_account( $this, $filter );
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * Consults the accounts.
	 *
	 * @param string $key Property
	 *
	 * @return bool
	 * @since 1.2.1
	 */
	public function has_prop( string $key ) {
		return $this->__isset( $key );
	}

	/**
	 * Determine whether the account exists in the database.
	 *
	 * @return bool True if account exists in the database, false if not.
	 * @since 1.2.1
	 */
	public function exists() {
		return ! empty( $this->id );
	}

	/**
	 * Return an array representation.
	 *
	 * @return array Array representation.
	 * @since 1.2.1
	 */
	public function to_array() {
		return get_object_vars( $this );
	}


	/**
	 * Get account balance
	 *
	 * @return float|string
	 * @since 1.0.2
	 */
	public function get_calculated_balance() {
		if ( null !== $this->balance ) {
			return $this->balance;
		}
		global $wpdb;
		$transaction_total = (float) $wpdb->get_var(
			$wpdb->prepare( "SELECT SUM(CASE WHEN type='income' then amount WHEN type='expense' then - amount END) as total from {$wpdb->prefix}ea_transactions WHERE account_id=%d", $this->id )
		);
		$this->balance     = $this->opening_balance + $transaction_total;

		return $this->balance;
	}
}
