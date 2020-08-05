<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Money{
	const ROUND_HALF_UP = PHP_ROUND_HALF_UP;
	const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
	const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
	const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;

	/**
	 * @var int|float
	 */
	protected $amount;

	/**
	 * @var EAccounting_Currency
	 */
	protected $currency;


	/**
	 * Create a new instance.
	 *
	 * @param mixed $amount
	 * @param EAccounting_Currency $currency
	 * @param bool $convert
	 *
	 * @throws \UnexpectedValueException
	 */
	public function __construct( $amount, EAccounting_Currency $currency, $convert = false ) {
		$this->currency = $currency;
		$this->amount   = $this->parseAmount( $amount, $convert );
	}

	/**
	 * parseAmount.
	 *
	 * @param mixed $amount
	 * @param bool $convert
	 *
	 * @return int|float
	 * @throws \UnexpectedValueException
	 *
	 */
	protected function parseAmount( $amount, $convert = false ) {
		$amount = $this->parseAmountFromString( $this->parseAmountFromCallable( $amount ) );

		if ( is_int( $amount ) ) {
			return (int) $this->convertAmount( $amount, $convert );
		}

		if ( is_float( $amount ) ) {
			return (float) round( $this->convertAmount( $amount, $convert ), $this->currency->get_precision() );
		}

		return 0;
	}

	/**
	 * parseAmountFromCallable.
	 *
	 * @param mixed $amount
	 *
	 * @return mixed
	 */
	protected function parseAmountFromCallable( $amount ) {
		if ( ! is_callable( $amount ) ) {
			return $amount;
		}

		return $amount();
	}

	/**
	 * parseAmountFromString.
	 *
	 * @param mixed $amount
	 *
	 * @return int|float|mixed
	 */
	protected function parseAmountFromString( $amount ) {
		if ( ! is_string( $amount ) ) {
			return $amount;
		}

		$thousandsSeparator = $this->currency->get_thousand_separator('edit');
		$decimalMark        = $this->currency->get_decimal_separator('edit');

		$amount = str_replace( $this->currency->get_symbol(), '', $amount );
		$amount = preg_replace( '/[^0-9\\' . $thousandsSeparator . '\\' . $decimalMark . '\-\+]/', '', $amount );
		$amount = str_replace( array(
			$thousandsSeparator,
			$decimalMark
		), array( '', '.' ), $amount );

		if ( preg_match( '/^([\-\+])?\d+$/', $amount ) ) {
			$amount = (int) $amount;
		} elseif ( preg_match( '/^([\-\+])?\d+\.\d+$/', $amount ) ) {
			$amount = (float) $amount;
		}

		return $amount;
	}

	/**
	 * convertAmount.
	 *
	 * @param int|float $amount
	 * @param bool $convert
	 *
	 * @return int|float
	 */
	protected function convertAmount( $amount, $convert = false ) {
		if ( ! $convert ) {
			return $amount;
		}

		return $amount * $this->currency->get_subunit();
	}

	/**
	 * __callStatic.
	 *
	 * @param string $method
	 * @param array $arguments
	 *
	 * @return EAccounting_Money
	 */
	public static function __callStatic( $method, array $arguments ) {
		$convert = ( isset( $arguments[1] ) && is_bool( $arguments[1] ) ) ? (bool) $arguments[1] : false;

		return new static( $arguments[0], new EAccounting_Currency( $method ), $convert );
	}

	/**
	 * assertSameCurrency.
	 *
	 * @param EAccounting_Money $other
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function assertSameCurrency( self $other ) {
		if ( ! $this->isSameCurrency( $other ) ) {
			throw new InvalidArgumentException( 'Different currencies "' . $this->currency . '" and "' . $other->currency . '"' );
		}
	}

	/**
	 * assertOperand.
	 *
	 * @param int|float $operand
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function assertOperand( $operand ) {
		if ( ! is_int( $operand ) && ! is_float( $operand ) ) {
			throw new InvalidArgumentException( 'Operand "' . $operand . '" should be an integer or a float' );
		}
	}

	/**
	 * assertRoundingMode.
	 *
	 * @param int $roundingMode
	 *
	 * @throws \OutOfBoundsException
	 */
	protected function assertRoundingMode( $roundingMode ) {
		$roundingModes = [ self::ROUND_HALF_DOWN, self::ROUND_HALF_EVEN, self::ROUND_HALF_ODD, self::ROUND_HALF_UP ];

		if ( ! in_array( $roundingMode, $roundingModes ) ) {
			throw new OutOfBoundsException( 'Rounding mode should be ' . implode( ' | ', $roundingModes ) );
		}
	}

	/**
	 * getAmount.
	 *
	 * @return int|float
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * getValue.
	 *
	 * @return float
	 */
	public function getValue() {
		return round( $this->amount / $this->currency->get_subunit(), $this->currency->get_precision() );
	}

	/**
	 * getCurrency.
	 *
	 * @return EAccounting_Currency
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * isSameCurrency.
	 *
	 * @param EAccounting_Money $other
	 *
	 * @return bool
	 */
	public function isSameCurrency( self $other ) {
		return $this->currency->equals( $other->currency );
	}

	/**
	 * compare.
	 *
	 * @param EAccounting_Money $other
	 *
	 * @return int
	 * @throws \InvalidArgumentException
	 *
	 */
	public function compare( self $other ) {
		$this->assertSameCurrency( $other );

		if ( $this->amount < $other->amount ) {
			return - 1;
		}

		if ( $this->amount > $other->amount ) {
			return 1;
		}

		return 0;
	}

	/**
	 * equals.
	 *
	 * @param EAccounting_Money $other
	 *
	 * @return bool
	 */
	public function equals( self $other ) {
		return $this->compare( $other ) == 0;
	}

	/**
	 * greaterThan.
	 *
	 * @param EAccounting_Money $other
	 *
	 * @return bool
	 */
	public function greaterThan( self $other ) {
		return $this->compare( $other ) == 1;
	}

	/**
	 * greaterThanOrEqual.
	 *
	 * @param EAccounting_Money $other
	 *
	 * @return bool
	 */
	public function greaterThanOrEqual( self $other ) {
		return $this->compare( $other ) >= 0;
	}

	/**
	 * lessThan.
	 *
	 * @param EAccounting_Money $other
	 *
	 * @return bool
	 */
	public function lessThan( self $other ) {
		return $this->compare( $other ) == - 1;
	}

	/**
	 * lessThanOrEqual.
	 *
	 * @param EAccounting_Money $other
	 *
	 * @return bool
	 */
	public function lessThanOrEqual( self $other ) {
		return $this->compare( $other ) <= 0;
	}

	/**
	 * convert.
	 *
	 * @param EAccounting_Currency $currency
	 * @param int|float $ratio
	 * @param int $roundingMode
	 *
	 * @return EAccounting_Money
	 * @throws \OutOfBoundsException
	 *
	 * @throws \InvalidArgumentException
	 */
	public function convert( EAccounting_Currency $currency, $ratio, $roundingMode = self::ROUND_HALF_UP ) {
		$this->currency = $currency;

		$this->assertOperand( $ratio );
		$this->assertRoundingMode( $roundingMode );

		if ( $ratio < 1 ) {
			return $this->divide( $ratio, $roundingMode );
		}

		return $this->multiply( $ratio, $roundingMode );
	}

	/**
	 * add.
	 *
	 * @param EAccounting_Money $addend
	 *
	 * @return EAccounting_Money
	 * @throws \InvalidArgumentException
	 *
	 */
	public function add( self $addend ) {
		$this->assertSameCurrency( $addend );

		return new self( $this->amount + $addend->amount, $this->currency );
	}

	/**
	 * subtract.
	 *
	 * @param EAccounting_Money $subtrahend
	 *
	 * @return EAccounting_Money
	 * @throws \InvalidArgumentException
	 *
	 */
	public function subtract( self $subtrahend ) {
		$this->assertSameCurrency( $subtrahend );

		return new self( $this->amount - $subtrahend->amount, $this->currency );
	}

	/**
	 * multiply.
	 *
	 * @param int|float $multiplier
	 * @param int $roundingMode
	 *
	 * @return EAccounting_Money
	 * @throws \OutOfBoundsException
	 *
	 * @throws \InvalidArgumentException
	 */
	public function multiply( $multiplier, $roundingMode = self::ROUND_HALF_UP ) {
		return new self( round( $this->amount * $multiplier, $this->currency->get_precision(), $roundingMode ), $this->currency );
	}

	/**
	 * divide.
	 *
	 * @param int|float $divisor
	 * @param int $roundingMode
	 *
	 * @return EAccounting_Money
	 * @throws \OutOfBoundsException
	 *
	 * @throws \InvalidArgumentException
	 */
	public function divide( $divisor, $roundingMode = self::ROUND_HALF_UP ) {
		$this->assertOperand( $divisor );
		$this->assertRoundingMode( $roundingMode );

		if ( $divisor == 0 ) {
			throw new InvalidArgumentException( 'Division by zero' );
		}

		return new self( round( $this->amount / $divisor, $this->currency->get_precision(), $roundingMode ), $this->currency );
	}

	/**
	 * allocate.
	 *
	 * @param array $ratios
	 *
	 * @return array
	 */
	public function allocate( array $ratios ) {
		$remainder = $this->amount;
		$results   = [];
		$total     = array_sum( $ratios );

		foreach ( $ratios as $ratio ) {
			$share     = floor( $this->amount * $ratio / $total );
			$results[] = new self( $share, $this->currency );
			$remainder -= $share;
		}

		for ( $i = 0; $remainder > 0; $i ++ ) {
			$results[ $i ]->amount ++;
			$remainder --;
		}

		return $results;
	}

	/**
	 * isZero.
	 *
	 * @return bool
	 */
	public function isZero() {
		return $this->amount == 0;
	}

	/**
	 * isPositive.
	 *
	 * @return bool
	 */
	public function isPositive() {
		return $this->amount > 0;
	}

	/**
	 * isNegative.
	 *
	 * @return bool
	 */
	public function isNegative() {
		return $this->amount < 0;
	}

	/**
	 * formatSimple.
	 *
	 * @return string
	 */
	public function formatSimple() {
		return number_format(
			$this->getValue(),
			$this->currency->get_precision(),
			$this->currency->get_decimal_separator('edit'),
			$this->currency->get_thousand_separator('edit')
		);
	}

	/**
	 * format.
	 *
	 * @return string
	 */
	public function format() {
		$negative  = $this->isNegative();
		$value     = $this->getValue();
		$amount    = $negative ? - $value : $value;
		$thousands = $this->currency->get_thousand_separator('edit');
		$decimals  = $this->currency->get_decimal_separator('edit');
		$prefix    = $this->currency->get_prefix();
		$suffix    = $this->currency->get_suffix();
		$value     = number_format( $amount, $this->currency->get_precision(), $decimals, $thousands );

		return ( $negative ? '-' : '' ) . $prefix . $value . $suffix;
	}

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 */
	public function toArray() {
		return [
			'amount'   => $this->amount,
			'value'    => $this->getValue(),
			'currency' => $this->currency,
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
		return $this->format();
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
