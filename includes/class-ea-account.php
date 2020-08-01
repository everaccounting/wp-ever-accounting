<?php
/**
 * Handle the account object
 *
 * @class       EAccounting_Account
 * @version     1.0.0
 * @package     EverAccounting/Classes
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Account
 * @since 1.0.2
 */
class EAccounting_Account extends EAccounting_Object {

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
		'currency_code'   => 'USD',
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
	 * @throws Exception
	 * @since 1.0.2
	 */
	public function validate_props() {
		// TODO: Implement validate_props() method.
	}

	/**
	 * Method to read a record. Creates a new EAccounting_Object based object.
	 *
	 * @param int $id ID of the object to read.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	public function read( $id ) {
		$this->set_defaults();

		// Get from cache if available.
		$item = 0 < $id ? wp_cache_get( $this->object_type.'-item-' . $id, $this->cache_group ) : false;

		if ( false === $item ) {
			$item = eaccounting()->accounts->find($id);

			if ( 0 < $item->id ) {
				wp_cache_set( $this->object_type.'-item-' . $item->id, $item, $this->cache_group );
			}
		}

		if ( ! $item || ! $item->id ) {
			throw new Exception( __( 'Invalid account.', 'wp-ever-accounting' ) );
		}

		$this->populate($item);
	}

	/**
	 * Method to create a new record of an EverAccounting object.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	public function create() {
		// TODO: Implement create() method.
	}

	/**
	 * Updates a record in the database.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	public function update() {
		// TODO: Implement update() method.
	}

	/**
	 * Deletes a record from the database.
	 *
	 * @return bool result
	 * @since 1.0.2
	 */
	public function delete() {
		// TODO: Implement delete() method.
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
		$this->error('invalid', 'Custom Error message');
//		$this->set_prop( 'number', $number );
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
			return eaccounting_get_money( $this->balance, $this->get_currency_code( 'edit' ), true )->format();
		}

		return eaccounting_get_money( $this->balance, $this->get_currency_code( 'edit' ), true )->getValue();
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
