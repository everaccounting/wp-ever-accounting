<?php
/**
 * Handle the account object.
 *
 * @since       1.0.2
 *
 * @package     EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Base_Object;

defined( 'ABSPATH' ) || exit();

/**
 * Class Account
 *
 * @since 1.0.2
 */
class Account extends Base_Object {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $object_type = 'account';

	/***
	 * Object table name.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $table = 'ea_accounts';

	/**
	 * Account Data array.
	 *
	 * @since 1.0.2
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
		'enabled'         => 1,
		'company_id'      => null,
		'creator_id'      => null,
		'date_created'    => null,
	);

	/**
	 * @since 1.0.2
	 * @var int[]
	 */
	protected $extra_data = array(
		'balance' => 0
	);

	/**
	 * Get the account if ID is passed, otherwise the account is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_account function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param int|object|Category $data object to read.
	 *
	 * @since 1.0.2
	 */
	public function __construct( $data = 0 ) {
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

		if ( $this->get_id() > 0 && ! $this->object_read ) {
			$this->read();
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
	 * @since  1.0.2
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Returns the account number.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_number( $context = 'edit' ) {
		return $this->get_prop( 'number', $context );
	}

	/**
	 * Returns account opening balance.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_opening_balance( $context = 'edit' ) {
		return $this->get_prop( 'opening_balance', $context );
	}

	/**
	 * Returns account currency code.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Return account bank name.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_bank_name( $context = 'edit' ) {
		return $this->get_prop( 'bank_name', $context );
	}

	/**
	 * Return account bank phone number.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_bank_phone( $context = 'edit' ) {
		return $this->get_prop( 'bank_phone', $context );
	}

	/**
	 * Return account bank address.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_bank_address( $context = 'edit' ) {
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
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * Set the account number.
	 *
	 * @param string $number bank account number
	 *
	 * @since 1.0.2
	 */
	public function set_number( $number ) {
		$this->set_prop( 'number', eaccounting_clean( $number ) );
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
		//$this->set_prop( 'opening_balance', eaccounting_sanitize_price( $opening_balance, $this->get_currency_code() ) );
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
		$this->set_prop( 'currency_code', strtoupper( $currency_code ) );
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
		$this->set_prop( 'bank_name', eaccounting_clean( $bank_name ) );
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
		$this->set_prop( 'bank_phone', eaccounting_clean( $bank_phone ) );
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
		$this->set_prop( 'bank_address', sanitize_textarea_field( $bank_address ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Extra
	|--------------------------------------------------------------------------
	*/

	/**
	 * @param bool $format
	 *
	 * @since 1.0.2
	 *
	 * @return float|string
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
		$this->set_prop( 'balance', eaccounting_sanitize_price( $balance, $this->get_currency_code() ) );
	}

}
