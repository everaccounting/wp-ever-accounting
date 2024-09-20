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
	 * Get a currency from the database.
	 *
	 * @param mixed $currency Currency ID or object.
	 *
	 * @since 1.1.6
	 * @return Currency|null Currency object if found, otherwise null.
	 */
	public function get( $currency ) {
		return Currency::find( $currency );
	}

	/**
	 * Insert a new currency into the database.
	 *
	 * @param array $data Currency data.
	 * @param bool  $wp_error Optional. Whether to return a WP_Error on failure. Default false.
	 *
	 * @since 1.1.0
	 * @return Currency|false|\WP_Error Currency object on success, false or WP_Error on failure.
	 */
	public function insert( $data, $wp_error = true ) {
		return Currency::insert( $data, $wp_error );
	}

	/**
	 * Delete a currency from the database.
	 *
	 * @param int $id Currency ID.
	 *
	 * @since 1.1.0
	 * @return bool True on success, false on failure.
	 */
	public function delete( $id ) {
		$currency = $this->get( $id );
		if ( ! $currency ) {
			return false;
		}

		return $currency->delete();
	}

	/**
	 * Get query results for currencies.
	 *
	 * @param array $args Query arguments.
	 * @param bool  $count Optional. Whether to return only the total found currencies for the query.
	 *
	 * @since 1.1.0
	 * @return array|int|Currency[] Array of currency objects, the total found currencies for the query, or the total found currencies for the query as int when `$count` is true.
	 */
	public function query( $args = array(), $count = false ) {
		if ( $count ) {
			return Currency::count( $args );
		}

		return Currency::results( $args );
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
	public function get_rate( $currency = null ) {
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
	public function is_base( $currency = null ) {
		$currency = $this->get( $currency );

		if ( ! $currency ) {
			return false;
		}

		return $currency->is_default;
	}
}
