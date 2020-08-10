<?php
/**
 * Handle the account object
 *
 * @class       Account
 * @version     1.0.0
 * @package     EverAccounting/Classes
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Base_Object;

defined( 'ABSPATH' ) || exit();

/**
 * Class Account
 * @since 1.0.2
 */
class Account extends Base_Object {

	/**
	 * A group must be set to to enable caching.
	 *
	 * @var string
	 * @since 1.0.2
	 */
	protected $cache_group = 'accounts';

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 * @since 1.0.2
	 */
	public $object_type = 'account';

	/**
	 * @var
	 * @since 1.0.2
	 */
	protected $balance = 0;

	/**
	 * Account Data array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'name'            => '',
		'number'          => '',
		'opening_balance' => 0.0000,
		'currency_code'   => '',
		'bank_name'       => null,
		'bank_phone'      => null,
		'bank_address'    => null,
		'enabled'         => 1,
		'company_id'      => null,
		'creator_id'      => null,
		'date_created'    => null,
	);

	/**
	 * EAccounting_Account constructor.
	 *
	 * @param int $data
	 */
	public function __construct( $data ) {
		parent::__construct( $data );

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} else {
			$this->set_id( 0 );
		}

		if ( $this->get_id() > 0 ) {
			$this->read( $this->get_id() );
		}
	}

	/**
	 * Method to validate before inserting and updating EverAccounting object.
	 *
	 * @throws \Exception
	 * @since 1.0.2
	 */
	public function validate_props() {
		global $wpdb;

		if ( ! $this->get_date_created( 'edit' ) ) {
			$this->set_date_created( time() );
		}

		if ( ! $this->get_company_id( 'edit' ) ) {
			$this->set_company_id( 1 );
		}

		if ( ! $this->get_prop( 'creator_id' ) ) {
			$this->set_prop( 'creator_id', eaccounting_get_current_user_id() );
		}

		if ( empty( $this->get_name( 'edit' ) ) ) {
			throw new \Exception( __( 'Account Name is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_number( 'edit' ) ) ) {
			throw new \Exception( __( 'Account Number is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_currency_code( 'edit' ) ) ) {
			throw new \Exception( __( 'Currency code is required', 'wp-ever-accounting' ) );
		}

		$currency = eaccounting_get_currency_by_code( $this->get_currency_code( 'edit' ) );
		if ( ! $currency || ! $currency->exists() ) {
			throw new Exception( 'invalid-currency', __( 'Account associated currency does not exist.', 'wp-ever-accounting' ) );
		}

		if ( $existing_id = $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_accounts where number=%s", $this->get_number( 'edit' ) ) ) ) {
			if ( ! empty( $existing_id ) && absint( $existing_id ) !== $this->get_id() ) {
				throw new \Exception( __( 'Duplicate account number.', 'wp-ever-accounting' ) );
			}
		}

	}

	/**
	 * Method to read a record. Creates a new EAccounting_Object based object.
	 *
	 * @param int $id ID of the object to read.
	 *
	 * @throws \Exception
	 * @since 1.0.2
	 */
	public function read( $id ) {
		$this->set_defaults();

		// Get from cache if available.
		$item = 0 < $id ? wp_cache_get( $this->object_type . '-item-' . $id, $this->cache_group ) : false;

		if ( false === $item ) {
			$item = Query_Account::init()->find( $id );

			if ( 0 < $item->id ) {
				wp_cache_set( $this->object_type . '-item-' . $item->id, $item, $this->cache_group );
			}
		}

		if ( ! $item || ! $item->id ) {
			throw new \Exception( __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		$this->populate( $item );
	}

	/**
	 * Create a new account in the database.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function create() {
		$this->validate_props();
		global $wpdb;
		$account_data = array(
			'name'            => $this->get_name( 'edit' ),
			'number'          => $this->get_number( 'edit' ),
			'opening_balance' => $this->get_opening_balance( 'edit' ),
			'currency_code'   => $this->get_currency_code( 'edit' ),
			'bank_name'       => $this->get_bank_name( 'edit' ),
			'bank_phone'      => $this->get_bank_phone( 'edit' ),
			'bank_address'    => $this->get_bank_address( 'edit' ),
			'enabled'         => $this->get_enabled( 'edit' ),
			'company_id'      => $this->get_company_id( 'edit' ),
			'creator_id'      => $this->get_prop( 'creator_id' ),
			'date_created'    => $this->get_date_created( 'edit' )->format( 'Y-m-d H:i:s' ),
		);

		do_action( 'eaccounting_pre_insert_account', $this->get_id(), $this );

		$data = wp_unslash( apply_filters( 'eaccounting_new_account_data', $account_data ) );
		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_accounts', $data ) ) {
			throw new \Exception( $wpdb->last_error );
		}

		do_action( 'eaccounting_insert_account', $this->get_id(), $this );

		$this->set_id( $wpdb->insert_id );
		$this->apply_changes();
		$this->set_object_read( true );
	}

	/**
	 * Update a account in the database.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 *
	 */
	public function update() {
		global $wpdb;

		$this->validate_props();
		$changes = $this->get_changes();
		if ( ! empty( $changes ) ) {
			do_action( 'eaccounting_pre_update_account', $this->get_id(), $changes );

			try {
				$wpdb->update( $wpdb->prefix . 'ea_accounts', $changes, array( 'id' => $this->get_id() ) );
			} catch ( \Exception $e ) {
				throw new \Exception( __( 'Could not update account.', 'wp-ever-accounting' ) );
			}

			do_action( 'eaccounting_update_account', $this->get_id(), $changes, $this->data );

			$this->apply_changes();
			$this->set_object_read( true );
			wp_cache_delete( 'transaction-item-' . $this->get_id(), 'transactions' );
		}
	}

	/**
	 * Remove an account from the database.
	 *
	 * @param array $args
	 *
	 * @since 1.0.
	 */
	public function delete( $args = array() ) {
		if ( $this->get_id() ) {
			global $wpdb;
			do_action( 'eaccounting_pre_delete_account', $this->get_id() );
			$wpdb->delete( $wpdb->prefix . 'ea_accounts', array( 'id' => $this->get_id() ) );
			do_action( 'eaccounting_delete_account', $this->get_id() );
			$this->set_id( 0 );
		}
	}


	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the account name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.0.2
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Returns the account number.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_number( $context = 'view' ) {
		return $this->get_prop( 'number', $context );
	}

	/**
	 * Returns account opening balance.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_opening_balance( $context = 'view' ) {
		return $this->get_prop( 'opening_balance', $context );
	}

	/**
	 * Returns account currency code.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_currency_code( $context = 'view' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Return account bank name.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_bank_name( $context = 'view' ) {
		return $this->get_prop( 'bank_name', $context );
	}

	/**
	 * Return account bank phone number.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_bank_phone( $context = 'view' ) {
		return $this->get_prop( 'bank_phone', $context );
	}

	/**
	 * Return account bank address.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_bank_address( $context = 'view' ) {
		return $this->get_prop( 'bank_address', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set account name.
	 *
	 * @param string $name Account name.
	 *
	 * @since 1.0.2
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', $name );
	}

	/**
	 * Set the account number.
	 *
	 * @param string $number bank account number
	 *
	 * @since 1.0.2
	 */
	public function set_number( $number ) {
		$this->set_prop( 'number', $number );
	}

	/**
	 * Returns account opening balance.
	 *
	 * @param string $opening_balance opening balance of the account.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_opening_balance( $opening_balance ) {
		$this->set_prop( 'opening_balance', $opening_balance );
	}

	/**
	 * Set account currency code.
	 *
	 * @param string $currency_code Bank currency code
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_currency_code( $currency_code ) {
		$this->set_prop( 'currency_code', $currency_code );
	}

	/**
	 * Set account bank name.
	 *
	 * @param string $bank_name name of the bank
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_bank_name( $bank_name ) {
		$this->set_prop( 'bank_name', $bank_name );
	}

	/**
	 * Set account bank phone number.
	 *
	 * @param string $bank_phone Bank phone number.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_bank_phone( $bank_phone ) {
		$this->set_prop( 'bank_phone', $bank_phone );
	}

	/**
	 * Set account bank address.
	 *
	 * @param string $bank_address Bank physical address
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_bank_address( $bank_address ) {
		$this->set_prop( 'bank_address', $bank_address );
	}

	/*
	|--------------------------------------------------------------------------
	| Extra
	|--------------------------------------------------------------------------
	*/

	/**
	 * @param bool $format
	 *
	 * @return float|string
	 * @since 1.0.2
	 *
	 */
	public function get_balance( $format = false ) {
		if ( $format ) {
			return eaccounting_get_money( $this->get_opening_balance(), $this->get_currency_code( 'edit' ), true )->format();
		}

		return eaccounting_get_money( $this->get_opening_balance(), $this->get_currency_code( 'edit' ), true )->getValue();
	}

	/**
	 * Set balance.
	 *
	 * @param $balance
	 *
	 * @since 1.0.2
	 */
	protected function set_balance( $balance ) {
		$this->balance = $balance;
	}

}
