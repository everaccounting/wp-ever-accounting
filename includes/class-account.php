<?php
/**
 * Handle the Account object.
 *
 * @package     EverAccounting
 * @class       Account
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;
use EverAccounting\Traits\CurrencyTrait;
use EverAccounting\Traits\Attachment;

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
class Account extends Data {
	use CurrencyTrait;
	use Attachment;

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data = array(
		'name'            => '',
		'number'          => '',
		'currency_code'   => '',
		'opening_balance' => 0.0000,
		'bank_name'       => null,
		'bank_phone'      => null,
		'bank_address'    => null,
		'thumbnail_id'    => null,
		'enabled'         => 1,
		'creator_id'      => null,
		'date_created'    => null,
	);

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data_type = array(
		'id'              => '%d',
		'currency_code'   => '%s',
		'name'            => '%s',
		'number'          => '%s',
		'opening_balance' => '%.4f',
		'bank_name'       => '%s',
		'bank_phone'      => '%s',
		'bank_address'    => '%s',
		'thumbnail_id'    => '%d',
		'enabled'         => '%d',
		'creator_id'      => '%d',
		'date_created'    => '%s',
	);

	/**
	 * Account constructor.
	 *
	 * Get the account if ID is passed, otherwise the account is new and empty.
	 *
	 * @param int|object|Account $account object to read.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $account = 0 ) {
		parent::__construct();
		if ( $account instanceof self ) {
			$this->set_id( $account->get_id() );
		} elseif ( is_object( $account ) && ! empty( $account->id ) ) {
			$this->set_id( $account->id );
		} elseif ( is_array( $account ) && ! empty( $account['id'] ) ) {
			$this->set_props( $account );
		} elseif ( is_numeric( $account ) ) {
			$this->set_id( $account );
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
	 * @param int $account_id Object id.
	 * @param string $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_raw( $account_id, $field = 'id' ) {
		global $wpdb;

		$account_id = (int) $account_id;
		if ( ! $account_id ) {
			return false;
		}

		$account = wp_cache_get( $account_id, 'ea_accounts' );

		if ( false === $account ) {
			$account = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_accounts WHERE id = %d LIMIT 1", $account_id ) );

			if ( ! $account ) {
				return false;
			}

			wp_cache_add( $account_id, $account, 'ea_accounts' );
		}

		return apply_filters( 'eaccounting_account_item', $account );
	}

	/**
	 *  Insert an account in the database.
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

		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before an account is inserted in the database.
		 *
		 * @param array $data Account data to be inserted.
		 * @param string $data_arr Sanitized account data.
		 * @param Account $account Account object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_account', $data, $data_arr, $this );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_accounts', $data, $format ) ) {
			return new \WP_Error( 'db_insert_error', __( 'Could not insert account into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );

		/**
		 * Fires immediately after an account is inserted in the database.
		 *
		 * @param int $account_id Account id.
		 * @param array $data Account data to be inserted.
		 * @param string $data_arr Sanitized account data.
		 * @param Account $account Account object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_account', $this->id, $data, $data_arr, $this );

		return true;
	}

	/**
	 *  Update an account in the database.
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
		global $wpdb;
		$changes = $this->get_changes();
		$data    = wp_array_slice_assoc( $changes, array_keys( $this->data_type ) );
		$format  = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data    = wp_unslash( $data );
		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before an existing account is updated in the database.
		 *
		 * @param int $account_id Account id.
		 * @param array $data Account data.
		 * @param array $changes The data will be updated.
		 * @param Account $account Account object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_account', $this->get_id(), $this->to_array(), $changes, $this );

		if ( false === $wpdb->update( $wpdb->prefix . 'ea_accounts', $data, [ 'id' => $this->get_id() ], $format, [ 'id' => '%d' ] ) ) {
			return new \WP_Error( 'db_update_error', __( 'Could not update account in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing account is updated in the database.
		 *
		 * @param int $account_id Account id.
		 * @param array $data Account data.
		 * @param array $changes The data will be updated.
		 * @param Account $account Account object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_account', $this->get_id(), $this->to_array(), $changes, $this );

		return true;
	}

	/**
	 * Saves an account in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	public function save() {
		$user_id = get_current_user_id();
		// check if the name is available or not
		if ( empty( $this->get_prop( 'name' ) ) ) {
			return new \WP_Error( 'invalid_account_name', esc_html__( 'Account name is required', 'wp-ever-accounting' ) );
		}
		// check if the currency code or not
		if ( empty( $this->get_prop( 'currency_code' ) ) ) {
			return new \WP_Error( 'invalid_account_currency_code', esc_html__( 'Account currency is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'date_created' ) ) || '0000-00-00 00:00:00' === $this->get_prop( 'date_created' ) ) {
			$this->set_date_prop( 'date_created', current_time( 'mysql' ) );
		}

		if ( empty( $this->get_prop( 'creator_id' ) ) ) {
			$this->set_prop( 'creator_id', $user_id );
		}

		if ( $this->exists() ) {
			$is_error = $this->update();
		} else {
			$is_error = $this->insert();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), 'ea_accounts' );
		wp_cache_set( 'last_changed', microtime(), 'ea_accounts' );

		/**
		 * Fires immediately after an account is inserted or updated in the database.
		 *
		 * @param int $account_id Account id.
		 * @param Item $account Account object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_saved_account', $this->get_id(), $this );

		return $this->get_id();
	}


	/**
	 * Deletes the account from database.
	 *
	 * @return array|false true on success, false on failure.
	 * @since 1.1.0
	 */
	public function delete() {
		global $wpdb;
		if ( ! $this->exists() ) {
			return false;
		}

		$data = $this->to_array();

		/**
		 * Filters whether an account delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $account_id Account id.
		 * @param array $data Account data array.
		 * @param Account $account Transaction object.
		 *
		 * @since 1.2.1
		 */
		$check = apply_filters( 'eaccounting_check_delete_account', null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before an account is deleted.
		 *
		 * @param int $account_id Account id.
		 * @param array $data Account data array.
		 * @param Account $account Account object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_delete_account', $this->get_id(), $data, $this );

		$result = $wpdb->delete( $wpdb->prefix . 'ea_accounts', array( 'id' => $this->get_id() ) );
		if ( ! $result ) {
			return false;
		}

		/**
		 * Fires after an account is deleted.
		 *
		 * @param int $account_id Account id.
		 * @param array $data Account data array.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_delete_account', $this->get_id(), $data );

		// Clear object.
		wp_cache_delete( $this->get_id(), 'ea_accounts' );
		wp_cache_set( 'last_changed', microtime(), 'ea_accounts' );
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
	 * Return the account name.
	 *
	 * @return string
	 *
	 * @since  1.1.0
	 */
	public function get_name() {
		return $this->get_prop( 'name' );
	}

	/**
	 * Returns the account number.
	 *
	 * @return mixed|null
	 *
	 * @since 1.1.0
	 */
	public function get_number() {
		return $this->get_prop( 'number' );
	}

	/**
	 * Returns account opening balance.
	 *
	 * @return mixed|null
	 *
	 * @since 1.1.0
	 */
	public function get_opening_balance() {
		return $this->get_prop( 'opening_balance' );
	}

	/**
	 * Returns account currency code.
	 *
	 * @return mixed|null
	 *
	 * @since 1.1.0
	 */
	public function get_currency_code() {
		return $this->get_prop( 'currency_code' );
	}

	/**
	 * Return account bank name.
	 *
	 * @return mixed|null
	 *
	 * @since 1.1.0
	 */
	public function get_bank_name() {
		return $this->get_prop( 'bank_name' );
	}

	/**
	 * Return account bank phone number.
	 *
	 * @return mixed|null
	 *
	 * @since 1.1.0
	 */
	public function get_bank_phone() {
		return $this->get_prop( 'bank_phone' );
	}

	/**
	 * Return account bank address.
	 *
	 * @return mixed|null
	 *
	 * @since 1.1.0
	 */
	public function get_bank_address() {
		return $this->get_prop( 'bank_address' );
	}

	/**
	 * Get the thumbnail id.
	 *
	 * @return int
	 * @since 1.1.0
	 */
	public function get_thumbnail_id() {
		return $this->get_prop( 'thumbnail_id' );
	}

	/**
	 * get object status
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function get_enabled() {
		return $this->get_prop( 'enabled' );
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

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting item data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * Set account name.
	 *
	 * @param string $name Account name.
	 *
	 * @since 1.1.0
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * Set the account number.
	 *
	 * @param string $number bank account number
	 *
	 * @since 1.1.0
	 */
	public function set_number( $number ) {
		$this->set_prop( 'number', eaccounting_clean( $number ) );
	}

	/**
	 * Returns account opening balance.
	 *
	 * @param string $amount opening balance of the account.
	 *
	 * @since 1.1.0
	 */
	public function set_opening_balance( $amount ) {
		$this->set_prop( 'opening_balance', (float) $amount );
	}

	/**
	 * Set account currency code.
	 *
	 * @param string $currency_code Bank currency code
	 *
	 * @since 1.1.0
	 */
	public function set_currency_code( $currency_code ) {
		$this->set_prop( 'currency_code', strtoupper( $currency_code ) );
	}

	/**
	 * Set account bank name.
	 *
	 * @param string $bank_name name of the bank
	 *
	 * @since 1.1.0
	 */
	public function set_bank_name( $bank_name ) {
		$this->set_prop( 'bank_name', eaccounting_clean( $bank_name ) );
	}

	/**
	 * Set account bank phone number.
	 *
	 * @param string $bank_phone Bank phone number.
	 *
	 * @since 1.1.0
	 */
	public function set_bank_phone( $bank_phone ) {
		$this->set_prop( 'bank_phone', eaccounting_clean( $bank_phone ) );
	}

	/**
	 * Set account bank address.
	 *
	 * @param string $bank_address Bank physical address
	 *
	 * @since 1.1.0
	 */
	public function set_bank_address( $bank_address ) {
		$this->set_prop( 'bank_address', sanitize_textarea_field( $bank_address ) );
	}

	/**
	 * Set object status.
	 *
	 * @param int $enabled Account enabled or not
	 *
	 * @since 1.0.2
	 */
	public function set_enabled( $enabled ) {
		$this->set_prop( 'enabled', (int) $enabled );
	}

	/**
	 * Set the thumbnail id.
	 *
	 * @param int $thumbnail_id Thumbnail ID
	 *
	 * @since 1.1.0
	 */
	public function set_thumbnail_id( $thumbnail_id ) {
		$this->set_prop( 'thumbnail_id', absint( $thumbnail_id ) );
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
	 * @param string $date Created date
	 *
	 * @since 1.0.2
	 */
	public function set_date_created( $date = null ) {
		if ( null === $date ) {
			$date = current_time( 'mysql' );
		}
		$this->set_date_prop( 'date_created', $date );
	}

	/*
	|--------------------------------------------------------------------------
	| Helper
	|--------------------------------------------------------------------------
	*/
	/**
	 * Return account current balance.
	 *
	 * @return mixed|null
	 *
	 * @since 1.1.0
	 */
	public function get_balance() {
		$balance = $this->get_prop( 'bank_phone' );
		if ( is_null( $balance ) ) {

		}

		return floatval( $balance );
	}


	/*
	|--------------------------------------------------------------------------
	| Conditional
	|--------------------------------------------------------------------------
	*/

	/**
	 * Alias self::get_enabled()
	 *
	 * @return bool
	 * @since 1.0.2
	 *
	 */
	public function is_enabled() {
		return eaccounting_string_to_bool( $this->get_prop( 'enabled' ) );
	}

}
