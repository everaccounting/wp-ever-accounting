<?php

namespace EverAccounting\Controllers;

use EverAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit;

/**
 * Currencies controller.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Controllers
 */
class Currencies {

	/**
	 * Get a currency.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.0
	 * @return Currency|null Currency object on success, null on failure.
	 */
	public function get( $currency = null ) {
		if ( empty( $currency ) ) {
			$currency = eac_get_base_currency();
		}

		return Currency::find( $currency );
	}

	/**
	 * Insert a currency.
	 *
	 * @param array $data Currency data.
	 * @param bool  $wp_error Whether to return false or WP_Error on failure.
	 *
	 * @return int|\WP_Error|Currency|bool The value 0 or WP_Error on failure. The Currency object on success.
	 * @since 1.1.0
	 */
	public function insert( $data = array(), $wp_error = true ) {
		return Currency::insert( $data, $wp_error );
	}

	/**
	 * Get currency items.
	 *
	 * @param array $args Query arguments.
	 * @param bool  $count Whether to return the count of items.
	 *
	 * @return int|array|Currency[]
	 * @since 1.1.0
	 */
	public function query( $args = array(), $count = false ) {
		if ( $count ) {
			return Currency::count( $args );
		}

		return Currency::results( $args );
	}

	/**
	 * Delete a currency.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function delete( $currency ) {
		$currency = Currency::find( $currency );

		if ( ! $currency ) {
			return false;
		}

		return $currency->delete();
	}

	/**
	 * Get the symbol of a currency.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_symbol( $currency = null ) {
		$currency = $this->get( $currency );

		if ( ! $currency ) {
			return '$';
		}

		return $currency->symbol;
	}

	/**
	 * Get the exchange rate of a currency.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_exchange_rate( $currency = null ) {
		$currency = $this->get( $currency );

		if ( ! $currency ) {
			return 1;
		}

		return $currency->exchange_rate;
	}

	/**
	 * Get the code of a currency.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_code( $currency = null ) {
		$currency = $this->get( $currency );

		if ( ! $currency ) {
			return 'USD';
		}

		return $currency->code;
	}

	/**
	 * Get the decimal places of a currency.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_decimals( $currency = null ) {
		$currency = $this->get( $currency );

		if ( ! $currency ) {
			return 2;
		}

		return $currency->decimals;
	}

	/**
	 * Get the thousands separator of a currency.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_thousands_separator( $currency = null ) {
		$currency = $this->get( $currency );

		if ( ! $currency ) {
			return ',';
		}

		return $currency->thousand_separator;
	}

	/**
	 * Get the decimal separator of a currency.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_decimal_separator( $currency = null ) {
		$currency = $this->get( $currency );

		if ( ! $currency ) {
			return '.';
		}

		return $currency->decimal_separator;
	}

	/**
	 * Get the subunit of a currency.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_subunit( $currency = null ) {
		$currency = $this->get( $currency );

		if ( ! $currency ) {
			return 100;
		}

		return $currency->subunit;
	}

	/**
	 * Get the position of a currency.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_position( $currency = null ) {
		$currency = $this->get( $currency );

		if ( ! $currency ) {
			return 'before';
		}

		return $currency->position;
	}

	/**
	 * Determine if a currency is default.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_default( $currency = null ) {
		$currency = $this->get( $currency );

		if ( ! $currency ) {
			return false;
		}

		return $currency->is_default;
	}
}
