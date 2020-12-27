<?php
/**
 * Currency Trait
 */

namespace EverAccounting\Traits;

defined( 'ABSPATH' ) || exit;

trait CurrencyTrait {

	/**
	 * Get currency object.
	 *
	 * @since 1.1.0
	 * @return \EverAccounting\Models\Currency
	 */
	public function get_currency() {
		$currency = false;
		if ( array_key_exists( 'currency_code', $this->data ) ) {
			$code     = $this->get_currency_code() ? $this->get_currency_code() : eaccounting()->settings->get( 'default_currency' );
			$currency = eaccounting_get_currency( $code );
		}

		return $currency;
	}

	/**
	 * Get currency rate.
	 *
	 * @since 1.1.0
	 * @return int|string
	 */
	public function get_currency_rate( $context = 'edit' ) {
		if ( $this->get_currency() ) {
			return $this->get_currency()->get_rate( $context );
		}

		return 1;
	}

	/**
	 * Get currency rate.
	 *
	 * @since 1.1.0
	 * @return int|string
	 */
	public function get_currency_precision() {
		if ( $this->get_currency() ) {
			return $this->get_currency()->get_precision();
		}

		return 2;
	}

	/**
	 * @since 1.1.0
	 * @return string
	 */
	public function get_currency_symbol() {
		if ( $this->get_currency() ) {
			return $this->get_currency()->get_symbol();
		}

		return '$';
	}

	/**
	 * @since 1.1.0
	 * @return string
	 */
	public function get_currency_subunit() {
		if ( $this->get_currency() ) {
			return $this->get_currency()->get_subunit();
		}

		return 2;
	}

	/**
	 * @since 1.1.0
	 * @return string
	 */
	public function get_currency_position() {
		if ( $this->get_currency() ) {
			return $this->get_currency()->get_position();
		}

		return 'before';
	}

	/**
	 * Get currency rate.
	 *
	 * @since 1.1.0
	 * @return int|string
	 */
	public function get_currency_decimal_separator() {
		if ( $this->get_currency() ) {
			return $this->get_currency()->get_decimal_separator();
		}

		return '.';
	}

	/**
	 * Get currency rate.
	 *
	 * @since 1.1.0
	 * @return int|string
	 */
	public function get_currency_thousand_separator() {
		if ( $this->get_currency() ) {
			return $this->get_currency()->get_thousand_separator();
		}

		return ',';
	}

	/**
	 * Format amount.
	 *
	 * @since 1.1.0
	 *
	 * @param $amount
	 *
	 * @return string
	 */
	public function format_amount( $amount ) {
		return eaccounting_price( $amount, $this->get_currency_code() );
	}

	/**
	 * Get converted amount.
	 *
	 * @since 1.1.0
	 *
	 * @param      $code
	 * @param null $rate
	 * @param      $amount
	 */
	public function get_converted_amount( $amount, $code, $rate = null ) {
		//todo complete
	}

}
