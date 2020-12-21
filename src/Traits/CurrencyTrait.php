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
		if ( is_callable( $this, 'get_currency_code' ) && ! empty( $this->get_currency_code() ) ) {
			$currency = eaccounting_get_currency( $this->get_currency_code() );
		}
		return $currency;
	}

	/**
	 * Get currency rate.
	 *
	 * @since 1.1.0
	 * @return int|string
	 */
	public function get_currency_rate() {
		if ( $this->get_currency() ) {
			return $this->get_currency()->get_rate();
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
	 * @param $amount
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function format_amount( $amount ) {
		return eaccounting_price( $amount, $this->get_currency() );
	}

	/**
	 * Get converted amount.
	 *
	 * @param      $amount
	 * @param      $code
	 * @param null $rate
	 * @since 1.1.0
	 */
	public function get_converted_amount( $amount, $code, $rate = null ) {
		//todo complete
	}
}
