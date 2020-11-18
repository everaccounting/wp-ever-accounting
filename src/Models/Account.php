<?php
/**
 * Handle the account object.
 *
 * @package     EverAccounting\Models
 * @class       Account
 * @version     1.0.2
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Repositories\Accounts;

defined( 'ABSPATH' ) || exit;

/**
 * Class Account
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Account extends ResourceModel {

	/**
	 * Get the account if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.0.2
	 *
	 * @param int|object|Account $data object to read.
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, Accounts::instance() );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the account name.
	 *
	 * @since  1.0.2
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Returns the account number.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_number( $context = 'edit' ) {
		return $this->get_prop( 'number', $context );
	}

	/**
	 * Returns account opening balance.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_opening_balance( $context = 'edit' ) {
		return $this->get_prop( 'opening_balance', $context );
	}

	/**
	 * Returns account currency code.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Return account bank name.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_bank_name( $context = 'edit' ) {
		return $this->get_prop( 'bank_name', $context );
	}

	/**
	 * Return account bank phone number.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_bank_phone( $context = 'edit' ) {
		return $this->get_prop( 'bank_phone', $context );
	}

	/**
	 * Return account bank address.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
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
	 * @since 1.0.2
	 *
	 * @param string $name Account name.
	 *
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * Set the account number.
	 *
	 * @since 1.0.2
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
	 * @since 1.0.2
	 *
	 * @param string $opening_balance opening balance of the account.
	 *
	 */
	public function set_opening_balance( $opening_balance ) {
		$code = empty( $this->get_currency_code() ) ? 'USD' : $this->get_currency_code();
		$this->set_prop( 'opening_balance', eaccounting_sanitize_price( $opening_balance, $code ) );
	}

	/**
	 * Set account currency code.
	 *
	 * @since 1.0.2
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
	 * @since 1.0.2
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
	 * @since 1.0.2
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
	 * @since 1.0.2
	 *
	 * @param string $bank_address Bank physical address
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
	 * @since 1.0.2
	 *
	 * @param bool $format
	 *
	 * @return float|string
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
	 * @since 1.0.2
	 *
	 * @param $balance
	 *
	 */
	protected function set_balance( $balance ) {
		$this->set_prop( 'balance', eaccounting_sanitize_price( $balance, $this->get_currency_code() ) );
	}
}
