<?php
/**
 * Price Helper class.
 *
 * @version     1.1.4
 * @package     Ever_Accounting
 * @class       Price
 */

namespace Ever_Accounting\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Price class
 */
class Price {
	/**
	 * Instance of money class.
	 *
	 * For formatting with currency code
	 * eaccounting_money( 100000, 'USD', true )->format()
	 * For inserting into database
	 * eaccounting_money( "$100,000", "USD", false )->getAmount()
	 *
	 * @param string $code
	 * @param bool $convert
	 *
	 * @param mixed $amount
	 *
	 * @since 1.0.2
	 *
	 * @return \Ever_Accounting\Money|\WP_Error
	 */
	public static function money( $amount, $code = 'USD', $convert = false ) {
		try {
			return new \Ever_Accounting\Money( $amount, $code, $convert );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'invalid_action', $e->getMessage() );
		}
	}

	/**
	 * Get default currency.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public static function get_default_currency() {
		$currency = eaccounting()->settings->get( 'default_currency', 'USD' );

		return apply_filters( 'ever_accounting_default_currency', $currency );
	}


	/**
	 * Format price with currency code & number format
	 *
	 * @param string $amount
	 *
	 * @param string $code If not passed will be used default currency.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public static function format_price( $amount, $code = null ) {
		if ( is_null( $code ) ) {
			$code = self::get_default_currency();
		}

		$amount = self::money( $amount, $code, true );
		if ( is_wp_error( $amount ) ) {
			/* translators: %s currency code */
			eaccounting_logger()->log_alert( sprintf( __( 'invalid currency code %s', 'wp-ever-accounting' ), $code ) );

			return '00.00';
		}

		return $amount->format();
	}

	/**
	 * Sanitize price for inserting into database
	 *
	 * @param string $amount
	 *
	 * @param string $code If not passed will be used default currency.
	 *
	 * @since 1.0.2
	 *
	 * @return float|int
	 */
	public static function sanitize_price( $amount, $code = null ) {
		$amount = self::money( $amount, $code, false );
		if ( is_wp_error( $amount ) ) {
			/* translators: %s currency code */
			eaccounting_logger()->log_alert( sprintf( __( 'invalid currency code %s', 'wp-ever-accounting' ), $code ) );

			return 0;
		}

		return $amount->getAmount();
	}

	/**
	 * Wrapper for sanitize and formatting.
	 * If needs formatting with symbol $get_value = false otherwise true.
	 *
	 * @param null $code
	 * @param false $get_value
	 * @param string $amount
	 *
	 * @since 1.1.0
	 *
	 * @return float|int|string
	 */
	public static function price( $amount, $code = null, $get_value = false ) {
		if ( $get_value ) {
			return self::sanitize_price( $amount, $code );
		}

		return self::format_price( $amount, $code );
	}

	/**
	 * Convert price from default to any other currency.
	 *
	 * @param string $amount
	 *
	 * @param string $to
	 * @param string $to_rate
	 *
	 * @since 1.0.2
	 *
	 * @return float|int|string
	 */
	public static function price_from_default( $amount, $to, $to_rate ) {
		$default = self::get_default_currency();
		$money   = self::money( $amount, $to );
		// No need to convert same currency
		if ( $default === $to ) {
			return $money->getAmount();
		}

		try {
			$money = $money->multiply( (float) $to_rate );
		} catch ( \Exception $e ) {
			return 0;
		}

		return $money->getAmount();
	}

	/**
	 * Convert price from other currency to default currency.
	 *
	 * @param      $amount
	 *
	 * @param      $from
	 * @param      $from_rate
	 *
	 * @since 1.0.2
	 *
	 * @return float|int|string
	 */
	public static function price_to_default( $amount, $from, $from_rate ) {
		$default = self::get_default_currency();
		$money   = self::money( $amount, $from );
		// No need to convert same currency
		if ( $default === $from ) {
			return $money->getAmount();
		}

		try {
			$money = $money->divide( (float) $from_rate );
		} catch ( \Exception $e ) {
			return 0;
		}

		return $money->getAmount();
	}

	/**
	 * Convert price convert between currency.
	 *
	 * @param      $from
	 * @param null $to
	 * @param null $from_rate
	 * @param null $to_rate
	 * @param      $amount
	 *
	 * @since 1.1.0
	 *
	 * @return float|int|string
	 */
	public static function price_convert( $amount, $from, $to = null, $from_rate = null, $to_rate = null ) {
		$default = self::get_default_currency();
		if ( is_null( $to ) ) {
			$to = $default;
		}

		if ( is_null( $from_rate ) ) {
			$from      = \Ever_Accounting\Currencies::get( $from );
			$from_rate = $from->get_rate();
		}
		if ( is_null( $to_rate ) ) {
			$to      = \Ever_Accounting\Currencies::get( $to );
			$to_rate = $to->get_rate();
		}

		if ( $from !== $default ) {
			$amount = self::price_to_default( $amount, $from, $from_rate );
		}

		return self::price_from_default( $amount, $to, $to_rate );
	}
}
