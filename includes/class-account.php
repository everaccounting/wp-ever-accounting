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
 * @property string $currency_code
 * @property string $name
 * @property string $number
 * @property float $opening_balance
 * @property string $bank_name
 * @property string $bank_phone
 * @property string $bank_address
 * @property int $thumbnail_id
 * @property boolean $enabled
 * @property int $creator_id
 * @property string $date_created
 */
class Account {
	/**
	 * Account data container.
	 *
	 * @since 1.2.1
	 * @var \stdClass
	 */
	public $data;

	/**
	 * Account id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Account balance
	 *
	 * @since 1.2.1
	 * @var float
	 */
	protected $balance = null;

	/**
	 * Account constructor.
	 *
	 * @param object $account Account Object
	 *
	 * @return void
	 * @since 1.2.1
	 */
	public function __construct( $account ) {
		if ( $account instanceof self ) {
			$this->id = absint( $account->id );
		} elseif ( is_object( $account ) && ! empty( $account->id ) ) {
			$this->id = absint( $account->id );
		} elseif ( is_array( $account ) && ! empty( $account['id'] ) ) {
			$this->id = absint( $account['id'] );
		} else {
			$this->id = absint( $account );
		}

		if ( $this->id > 0 ) {
			$data = self::load( $this->id );
			if ( ! $data ) {
				$this->id = null;

				return;
			}
			$this->data = $data;
			$this->id   = (int) $data->id;
		}
	}

	/**
	 * Return only the main account fields
	 *
	 * @param int $id The id of the account
	 *
	 * @return object|false Raw account object
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.2.1
	 */
	public static function load( $id ) {
		global $wpdb;

		if ( ! absint( $id ) ) {
			return false;
		}

		$data = wp_cache_get( $id, 'ea_accounts' );
		if ( $data ) {
			return $data;
		}

		$_data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}ea_accounts WHERE id = %d LIMIT 1",
				$id
			)
		);

		if ( ! $_data ) {
			return false;
		}

		$data = eaccounting_sanitize_account( $_data, 'raw' );

		eaccounting_set_cache( 'ea_accounts', $data );

		return $data;
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
		if ( isset( $this->data->$key ) ) {
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
			$this->data->$key = $value;
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
			$value = $this->data->$key;
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
		if ( isset( $this->data->$key ) ) {
			unset( $this->data->$key );
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
			return self::load( $this->id );
		}

		$this->data = eaccounting_sanitize_account( $this->data, $filter );

		return $this;
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
		return get_object_vars( $this->data );
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
		$balance           = $this->opening_balance + $transaction_total;
		$this->set_balance( $balance );
		return $balance;
	}

	/**
	 * Set balance.
	 *
	 * @param string $balance Account balance
	 * @since 1.1.0
	 */
	protected function set_balance( string $balance ) {
		$this->balance = $balance;
	}
}
