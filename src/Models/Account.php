<?php
/**
 * Handle the account object.
 *
 * @package     EverAccounting\Models
 * @class       Account
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Core\Repositories;
use EverAccounting\Traits\CurrencyTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Class Account
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Account extends ResourceModel {
	use CurrencyTrait;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'account';

	/**
	 * @since 1.1.0
	 * 
	 * @var string
	 */
	public $cache_group = 'ea_accounts';

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
	 * Get the account if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 * 
	 * @param int|object|Account $data object to read.
	 *
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_array( $data ) ) {
			$this->set_props( $data );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( 'accounts' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		$this->required_props = array(
			'name'   => __( 'Account name', 'wp-ever-accounting' ),
			'number' => __( 'Account number', 'wp-ever-accounting' ),
		);
	}
	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Functions for getting item data. Getter methods wont change anything unless
	| just returning from the props.
	|
	*/

	/**
	 * Return the account name.
	 *
	 * @since  1.1.0
	 * 
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 *
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Returns the account number.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $context
	 *
	 * @return mixed|null
	 *
	 */
	public function get_number( $context = 'edit' ) {
		return $this->get_prop( 'number', $context );
	}

	/**
	 * Returns account opening balance.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $context
	 *
	 * @return mixed|null
	 *
	 */
	public function get_opening_balance( $context = 'edit' ) {
		return $this->get_prop( 'opening_balance', $context );
	}

	/**
	 * Returns account currency code.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $context
	 *
	 * @return mixed|null
	 *
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Return account bank name.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $context
	 *
	 * @return mixed|null
	 *
	 */
	public function get_bank_name( $context = 'edit' ) {
		return $this->get_prop( 'bank_name', $context );
	}

	/**
	 * Return account bank phone number.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $context
	 *
	 * @return mixed|null
	 *
	 */
	public function get_bank_phone( $context = 'edit' ) {
		return $this->get_prop( 'bank_phone', $context );
	}

	/**
	 * Return account bank address.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $context
	 *
	 * @return mixed|null
	 *
	 */
	public function get_bank_address( $context = 'edit' ) {
		return $this->get_prop( 'bank_address', $context );
	}

	/**
	 * Get the thumbnail id.
	 *
	 * @param string $context
	 * @since 1.1.0
	 *
	 * @return int
	 */
	public function get_thumbnail_id( $context = 'edit' ) {
		return $this->get_prop( 'thumbnail_id', $context );
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
	 * @since 1.1.0
	 * 
	 * @param string $name Account name.
	 *
	 *
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * Set the account number.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $number bank account number
	 *
	 */
	public function set_number( $number ) {
		$this->set_prop( 'number', eaccounting_clean( $number ) );
	}

	/**
	 * Returns account opening balance.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $opening_balance opening balance of the account.
	 *
	 */
	public function set_opening_balance( $amount ) {
		$code = empty( $this->get_currency_code() ) ? 'USD' : $this->get_currency_code();
		$this->set_prop( 'opening_balance', (float) eaccounting_sanitize_number( $amount, true ) );
	}

	/**
	 * Set account currency code.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $currency_code Bank currency code
	 *
	 */
	public function set_currency_code( $currency_code ) {
		$this->set_prop( 'currency_code', strtoupper( $currency_code ) );
	}

	/**
	 * Set account bank name.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $bank_name name of the bank
	 *
	 */
	public function set_bank_name( $bank_name ) {
		$this->set_prop( 'bank_name', eaccounting_clean( $bank_name ) );
	}

	/**
	 * Set account bank phone number.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $bank_phone Bank phone number.
	 * 
	 */
	public function set_bank_phone( $bank_phone ) {
		$this->set_prop( 'bank_phone', eaccounting_clean( $bank_phone ) );
	}

	/**
	 * Set account bank address.
	 *
	 * @since 1.1.0
	 * 
	 * @param string $bank_address Bank physical address
	 * 
	 */
	public function set_bank_address( $bank_address ) {
		$this->set_prop( 'bank_address', sanitize_textarea_field( $bank_address ) );
	}

	/**
	 * Set the thumbnail id.
	 *
	 * @param int $thumbnail_id
	 * @since 1.1.0
	 */
	public function set_thumbnail_id( $thumbnail_id ) {
		$this->set_prop( 'thumbnail_id', absint( $thumbnail_id ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Additional methods
	|--------------------------------------------------------------------------
	|
	| Does extra thing as helper functions.
	|
	*/

	/**
	 * Get currency object.
	 *
	 * @since 1.1.0
	 * 
	 * @return Currency|null
	 * 
	 */
	public function get_currency() {
		try {
			$currency = new Currency( $this->get_currency_code() );
			$this->set_prop( 'currency', $currency );

			return $currency;
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 * Set currency code from object.
	 *
	 * @since 1.1.0
	 *
	 * @param array|object $currency
	 * 
	 */
	public function set_currency( $currency ) {
		$this->set_object_prop( $currency, 'code', 'currency_code' );
	}

	/**
	 * @since 1.1.0
	 * @since 1.0.2
	 *
	 * @param bool $format
	 *
	 * @return float|string
	 *
	 */
	public function get_balance( $format = false ) {
		if ( $format ) {
			return eaccounting_get_money( $this->get_prop( 'balance' ), $this->get_currency_code( 'edit' ), true )->format();
		}

		return eaccounting_get_money( $this->get_prop( 'balance' ), $this->get_currency_code( 'edit' ), true )->getValue();
	}

	/**
	 * Set balance.
	 *
	 * @since 1.1.0
	 * 
	 * @param $balance
	 *
	 */
	protected function set_balance( $balance ) {
		$this->set_prop( 'balance', (float) eaccounting_sanitize_number( $balance, true ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	|
	| Checks if a condition is true or false.
	|
	*/


	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/


}
