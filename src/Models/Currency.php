<?php
/**
 * Handle the currency object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.0.2
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Repositories\Currencies;

defined( 'ABSPATH' ) || exit;

/**
 * Class Currency
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Currency extends ResourceModel {

	/**
	 * Get an instance of Currency object.
	 *
	 * @since 1.0.2
	 *
	 * @param int|object|array|Currency $data object to read.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, Currencies::instance() );

		if ( is_string( $data ) && ! $this->get_object_read() ) {
			$currency = Currencies::instance()->get_by( 'code', $data );
			if ( $currency ) {
				$code       = $currency->code;
				$currencies = eaccounting_get_global_currencies();
				$defaults   = array_key_exists( $code, $currencies ) ? $currencies[ $code ] : array();
				$this->set_props( array_merge( $defaults, (array) $currency ) );
				$this->set_object_read( true );
			} else {
				$this->set_id( 0 );
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get currency name.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get currency code.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_code( $context = 'edit' ) {
		return $this->get_prop( 'code', $context );
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
	public function get_rate( $context = 'edit' ) {
		return $this->get_prop( 'rate', $context );
	}

	/**
	 * Get number of decimal points.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_precision( $context = 'edit' ) {
		return $this->get_prop( 'precision', $context );
	}

	/**
	 * Get currency symbol.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_symbol( $context = 'edit' ) {
		return $this->get_prop( 'symbol', $context );
	}

	/**
	 * Get symbol position.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_position( $context = 'edit' ) {
		return $this->get_prop( 'position', $context );
	}

	/**
	 * Get decimal separator.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_decimal_separator( $context = 'edit' ) {
		return $this->get_prop( 'decimal_separator', $context );
	}

	/**
	 * Get thousand separator.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_thousand_separator( $context = 'edit' ) {
		return $this->get_prop( 'thousand_separator', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set the currency name.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 */
	public function set_name( $value ) {
		$this->set_prop( 'name', eaccounting_clean( $value ) );
	}

	/**
	 * Set the code.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 */
	public function set_code( $value ) {
		if ( eaccounting_get_currency_code( $value ) ) {
			$this->set_prop( 'code', $value );
		}
	}

	/**
	 * Set the code.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 */
	public function set_rate( $value ) {
		$this->set_prop( 'rate', eaccounting_sanitize_number( $value, true ) );
	}

	/**
	 * Set precision.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 */
	public function set_precision( $value ) {
		$this->set_prop( 'precision', eaccounting_sanitize_number( $value ) );
	}

	/**
	 * Set symbol.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 */
	public function set_symbol( $value ) {
		$this->set_prop( 'symbol', eaccounting_clean( $value ) );
	}

	/**
	 * Set symbol position.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 */
	public function set_position( $value ) {
		$this->set_prop( 'position', eaccounting_clean( $value ) );
	}

	/**
	 * Set decimal separator.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 */
	public function set_decimal_separator( $value ) {
		$this->set_prop( 'decimal_separator', eaccounting_clean( $value ) );
	}

	/**
	 * Set thousand separator.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 */
	public function set_thousand_separator( $value ) {
		$this->set_prop( 'thousand_separator', eaccounting_clean( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Extra
	|--------------------------------------------------------------------------
	*/

	/**
	 * getSubunit.
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function get_subunit() {
		return $this->get_prop( 'subunit' );
	}

	/**
	 * Set subunit.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 */
	public function set_subunit( $value ) {
		$this->set_prop( 'subunit', absint( $value ) );
	}

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
		return $this->get_code( 'edit' ) === $currency->get_code( 'edit' );
	}

	/**
	 * is_symbol_first.
	 *
	 * @since 1.0.2
	 * @return bool
	 */
	public function is_symbol_first() {
		return 'before' === $this->get_position( 'edit' );
	}

	/**
	 * getPrefix.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_prefix() {
		if ( ! $this->is_symbol_first() ) {
			return '';
		}

		return $this->get_symbol( 'edit' );
	}

	/**
	 * getSuffix.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_suffix() {
		if ( $this->is_symbol_first() ) {
			return '';
		}

		return ' ' . $this->get_symbol( 'edit' );
	}

	/**
	 * __toString.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function __toString() {
		return $this->get_code( 'edit' ) . ' (' . $this->get_name( 'edit' ) . ')';
	}

	/**
	 * __callStatic.
	 *
	 * @since 1.0.2
	 *
	 * @param array  $arguments
	 *
	 * @param string $method
	 *
	 * @return Currency
	 */
	public static function __callStatic( $method, array $arguments ) {
		return new static( $method, $arguments );
	}
}
