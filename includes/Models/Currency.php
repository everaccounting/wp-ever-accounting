<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Currency.
 *
 * @since 1.0.0
 * @package EverAccounting\Models
 */
class Currency {
	/**
	 * Currency code.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $code;

	/**
	 * Currency name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $name;

	/**
	 * Currency rate.
	 *
	 * @since 1.0.0
	 * @var float
	 */
	public $rate;

	/**
	 * Currency symbol.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $symbol;

	/**
	 * Currency precision.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $precision;

	/**
	 * Currency decimal separator.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $decimal_separator;

	/**
	 * Currency thousand separator.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $thousand_separator;

	/**
	 * Currency constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Currency attributes.
	 */
	public function __construct( $attributes = [] ) {
		foreach ( $attributes as $key => $value ) {
			$this->{$key} = $value;
		}
	}
}
