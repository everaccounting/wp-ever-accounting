<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Currency
 *
 * @package EverAccounting\Models
 */
class Currency {
	/**
	 * Currency code.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $code;

	/**
	 * Currency name.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $name;

	/**
	 * Currency symbol.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $symbol;

	/**
	 * Currency position.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $position;

	/**
	 * Currency decimal separator.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $decimal_sep;

	/**
	 * Currency thousand separator.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $thousand_sep;

	/**
	 * Currency precision.
	 *
	 * @var int
	 * @since 1.0.0
	 */
	public $precision;

	/**
	 * Exchange rate.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $rate;

	/**
	 * Currency constructor.
	 *
	 * @param array $data Currency data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = array() ) {
		foreach ( $data as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->$key = $value;
			}
		}
	}
}
