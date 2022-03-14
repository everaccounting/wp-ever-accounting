<?php
/**
 * Tax Helper class.
 *
 * @version     1.1.4
 * @package     Ever_Accounting
 * @class       Tax
 */

namespace Ever_Accounting\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Tax class
 */
class Tax {
	/**
	 * Is tax enabled.
	 *
	 * @since 1.1.4
	 * @return bool
	 */
	public static function tax_enabled() {
		return apply_filters( 'ever_accounting_tax_enabled', ever_accounting_get_option( 'tax_enabled', 'no' ) === 'yes' );
	}

	/**
	 * Are prices inclusive of tax?
	 *
	 * @return bool
	 * @since 1.1.4
	 */
	public static function prices_include_tax() {
		return self::tax_enabled() && apply_filters( 'ever_accounting_prices_include_tax', ever_accounting_get_option( 'prices_include_tax' ) === 'yes' );
	}

	/**
	 * Calculate tax
	 *
	 * @param      $amount
	 * @param      $rate
	 * @param bool $inclusive
	 *
	 * @return float|int
	 * @since 1.1.0
	 */
	public static function calculate_tax( $amount, $rate, $inclusive = false ) {
		$tax = 0.00;

		if ( $amount > 0 ) {

			if ( $inclusive ) {
				$pre_tax = ( $amount / ( 1 + $rate ) );
				$tax     = $amount - $pre_tax;
			} else {
				$tax = $amount * $rate;
			}
		}

		return $tax;
	}

}
