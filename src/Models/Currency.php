<?php
/**
 * Handle the currency object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.0.2
 */

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Currency
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Currency {

	/**
	 * Item Data array.
	 *
	 * @since 1.0.4
	 *
	 * @var array
	 */
	protected $data = array(
		'name'               => '',
		'code'               => '',
		'rate'               => 1,
		'precision'          => 2,
		'symbol'             => '',
		'subunit'            => 2,
		'position'           => 'before',
		'decimal_separator'  => '.',
		'thousand_separator' => ',',
	);

	/**
	 * @param string $code Item object to read.
	 */
	public function __construct( $code ) {
		$currencies = eaccounting_get_currencies();
		$codes      = eaccounting_get_data( 'currencies' );
		$default    = array_key_exists( $code, $codes ) ? $codes[ $code ] : array();
		$currency   = array_key_exists( $code, $currencies ) ? $currencies[ $code ] : array();
		$this->data = wp_parse_args( $currency, $default);
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
	 * Get currency name.
	 *
	 * @since 1.0.2
	 *
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->data['name'];
	}

	/**
	 * Get currency code.
	 *
	 * @since 1.0.2
	 *
	 *
	 * @return string
	 */
	public function get_code() {
		return $this->data['code'];
	}

	/**
	 * Get currency rate.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_rate() {
		return $this->data['rate'];
	}

	/**
	 * Get number of decimal points.
	 *
	 * @since 1.0.2
	 *
	 *
	 * @return string
	 */
	public function get_precision() {
		return $this->data['precision'];
	}

	/**
	 * Get currency symbol.
	 *
	 * @since 1.0.2
	 *
	 *
	 * @return string
	 */
	public function get_symbol() {
		return $this->data['symbol'];
	}

	/**
	 * Get symbol position.
	 *
	 * @since 1.0.2
	 *
	 *
	 * @return string
	 */
	public function get_position() {
		return $this->data['position'];
	}

	/**
	 * Get decimal separator.
	 *
	 * @since 1.0.2
	 *
	 *
	 * @return string
	 */
	public function get_decimal_separator() {
		return $this->data['decimal_separator'];
	}

	/**
	 * Get thousand separator.
	 *
	 * @since 1.0.2
	 *
	 *
	 * @return string
	 */
	public function get_thousand_separator() {
		return $this->data['thousand_separator'];
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
	 * getSubunit.
	 *
	 * @since 1.0.2
	 *
	 * @return int
	 */
	public function get_subunit() {
		return $this->data['subunit'];
	}

	/**
	 * getPrefix.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_prefix() {
		if ( ! $this->is_symbol_first() ) {
			return '';
		}

		return $this->get_symbol();
	}

	/**
	 * getSuffix.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_suffix() {
		if ( $this->is_symbol_first() ) {
			return '';
		}

		return ' ' . $this->get_symbol();
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	|
	| Checks if a condition is true or false.
	|
	*/

	/**
	 * equals.
	 *
	 * @since 1.0.2
	 *
	 * @param Currency $currency
	 *
	 * @return bool
	 */
	public function equals( self $currency ) {
		return $this->get_code() === $currency->get_code();
	}

	/**
	 * is_symbol_first.
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_symbol_first() {
		return 'before' === $this->get_position();
	}
}
