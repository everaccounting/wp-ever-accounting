<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Money.
 *
 * @since   1.1.6
 * @package EverAccounting
 */
class Money {
	/**
	 * Currency code.
	 *
	 * @since 1.1.6
	 * @var string
	 */
	protected $code = '';

	/**
	 * Currency symbol.
	 *
	 * @since 1.1.6
	 * @var string
	 */
	protected $symbol = '';

	/**
	 * Currency rate.
	 *
	 * @since 1.1.6
	 * @var float
	 */
	protected $rate = 1;

	/**
	 * Currency precision.
	 *
	 * @since 1.1.6
	 * @var int
	 */
	protected $precision = 0;

	/**
	 * Currency position.
	 *
	 * @since 1.1.6
	 * @var string
	 */
	protected $position = 'before';

	/**
	 * Currency thousands separator.
	 *
	 * @since 1.1.6
	 * @var string
	 */
	protected $thousand_separator = ',';

	/**
	 * Currency decimal separator.
	 *
	 * @since 1.1.6
	 * @var string
	 */
	protected $decimal_separator = '.';

	/**
	 * Money constructor.
	 *
	 * @param string $code Currency code.
	 *
	 * @since 1.1.6
	 */
	public function __construct( $code = '' ) {
		$currencies = eac_get_currencies();
		if ( ! empty( $currencies[ $code ] ) ) {
			$currency                 = $currencies[ $code ];
			$this->code               = $currency['code'];
			$this->symbol             = $currency['symbol'];
			$this->rate               = $currency['rate'];
			$this->precision          = $currency['precision'];
			$this->position           = $currency['position'];
			$this->thousand_separator = $currency['thousand_separator'];
			$this->decimal_separator  = $currency['decimal_separator'];
		}
	}

	/**
	 * Get currency code.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * Get currency symbol.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_symbol() {
		return $this->symbol;
	}

	/**
	 * Get currency rate.
	 *
	 * @since 1.1.6
	 *
	 * @return float
	 */
	public function get_rate() {
		return $this->rate;
	}

	/**
	 * Get currency precision.
	 *
	 * @since 1.1.6
	 *
	 * @return float
	 */
	public function get_precision() {
		return $this->precision;
	}

	/**
	 * Get currency position.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_position() {
		return $this->position;
	}

	/**
	 * Get currency thousands separator.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_thousand_separator() {
		return $this->thousand_separator;
	}

	/**
	 * Get currency decimal separator.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public function get_decimal_separator() {
		return $this->decimal_separator;
	}

	/**
	 * Format money.
	 *
	 * @param float  $amount Money amount.
	 * @param string $code Currency code.
	 *
	 * @since 1.1.6
	 *
	 * @return string
	 */
	public static function format( $amount, $code = '' ) {
		if ( empty( $code ) ) {
			$code = eac_get_base_currency();
		}
		var_dump($code);
		$money = new Money( $code );
		var_dump( $money );
	}
}
