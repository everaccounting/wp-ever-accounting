<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Currency {
	/**
	 * @var string
	 */
	protected $currency;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var int
	 */
	protected $code;

	/**
	 * @var int
	 */
	protected $precision;

	/**
	 * @var int
	 */
	protected $subunit;

	/**
	 * @var string
	 */
	protected $symbol;

	/**
	 * @var bool
	 */
	protected $symbolPosition;

	/**
	 * @var string
	 */
	protected $decimalMark;

	/**
	 * @var string
	 */
	protected $thousandsSeparator;

	/**
	 * @var array
	 */
	protected static $currencies;

	/**
	 * @param string $currency
	 *
	 * @since 1.${CARET}
	 * EAccounting_Currency constructor.
	 */
	public function __construct( $currency ) {
		$currency   = strtoupper( trim( $currency ) );
		$currencies = self::getCurrencies();

		if ( ! array_key_exists( $currency, $currencies ) ) {
			throw new OutOfBoundsException( 'Invalid currency "' . $currency . '"' );
		}

		$attributes               = $currencies[ $currency ];
		$this->currency           = $currency;
		$this->name               = (string) $attributes['name'];
		$this->code               = (int) $attributes['code'];
		$this->precision          = (int) $attributes['precision'];
		$this->subunit            = (int) $attributes['subunit'];
		$this->symbol             = (string) $attributes['symbol'];
		$this->symbolPosition        = (bool) $attributes['position'];
		$this->decimalMark        = (string) $attributes['decimalSeparator'];
		$this->thousandsSeparator = (string) $attributes['thousandSeparator'];
	}

	/**
	 * __callStatic.
	 *
	 * @param string $method
	 * @param array $arguments
	 *
	 * @return EAccounting_Currency
	 */
	public static function __callStatic( $method, array $arguments ) {
		return new static( $method, $arguments );
	}

	/**
	 * setCurrencies.
	 *
	 * @param array $currencies
	 *
	 * @return void
	 */
	public static function setCurrencies( array $currencies ) {
		static::$currencies = $currencies;
	}

	/**
	 * getCurrencies.
	 *
	 * @return array
	 */
	public static function getCurrencies() {
		if ( ! isset( static::$currencies ) ) {
			static::$currencies = eaccounting_get_currency_config();
		}

		return (array) static::$currencies;
	}

	/**
	 * equals.
	 *
	 * @param EAccounting_Currency $currency
	 *
	 * @return bool
	 */
	public function equals( self $currency ) {
		return $this->getCurrency() === $currency->getCurrency();
	}

	/**
	 * getCurrency.
	 *
	 * @return string
	 */
	public function getCurrency() {
		return $this->currency;
	}


	/**
	 * getName.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * getCode.
	 *
	 * @return int
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * getPrecision.
	 *
	 * @return int
	 */
	public function getPrecision() {
		return $this->precision;
	}

	/**
	 * getSubunit.
	 *
	 * @return int
	 */
	public function getSubunit() {
		return $this->subunit;
	}

	/**
	 * getSymbol.
	 *
	 * @return string
	 */
	public function getSymbol() {
		return $this->symbol;
	}

	/**
	 * isSymbolFirst.
	 *
	 * @return bool
	 */
	public function isSymbolFirst() {
		return 'before' == $this->symbolPosition;
	}

	/**
	 * getDecimalMark.
	 *
	 * @return string
	 */
	public function getDecimalMark() {
		return $this->decimalMark;
	}

	/**
	 * getThousandsSeparator.
	 *
	 * @return string
	 */
	public function getThousandsSeparator() {
		return $this->thousandsSeparator;
	}

	/**
	 * getPrefix.
	 *
	 * @return string
	 */
	public function getPrefix() {
		if ( ! $this->isSymbolFirst() ) {
			return '';
		}

		return $this->symbol;
	}

	/**
	 * getSuffix.
	 *
	 * @return string
	 */
	public function getSuffix() {
		if ( $this->isSymbolFirst() ) {
			return '';
		}

		return ' ' . $this->symbol;
	}

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 */
	public function toArray() {
		return [
			$this->currency => [
				'name'                => $this->name,
				'code'                => $this->code,
				'precision'           => $this->precision,
				'subunit'             => $this->subunit,
				'symbol'              => $this->symbol,
				'position'        => $this->symbolPosition,
				'decimalSeparator'        => $this->decimalMark,
				'thousandSeparator' => $this->thousandsSeparator,
				'prefix'              => $this->getPrefix(),
				'suffix'              => $this->getSuffix(),
			]
		];
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param int $options
	 *
	 * @return string
	 */
	public function toJson( $options = 0 ) {
		return json_encode( $this->toArray(), $options );
	}

	/**
	 * jsonSerialize.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->toArray();
	}

	/**
	 * Get the evaluated contents of the object.
	 *
	 * @return string
	 */
	public function render() {
		return $this->currency . ' (' . $this->name . ')';
	}

	/**
	 * __toString.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
}