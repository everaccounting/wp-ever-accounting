<?php
/**
 * Handle the currency object.
 *
 * @package     EverAccounting
 * @class       Currency
 * @version     1.0.2
 *
 */

namespace EverAccounting\Currencies;

use EverAccounting\Abstracts\Model;

defined( 'ABSPATH' ) || exit();

/**
 * Class Currency
 * @since   1.1.0
 *
 * @package EverAccounting\Currency
 */
class Currency extends Model {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $object_type = 'currency';

	/***
	 * Object table name.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $table = 'ea_currencies';

	/**
	 * Currency Data array.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $data = array(
		'name'               => '',
		'code'               => '',
		'rate'               => 1,
		'precision'          => 0,
		'symbol'             => '',
		'position'           => '',
		'decimal_separator'  => '.',
		'thousand_separator' => ',',
		'enabled'            => 1,
		'date_created'       => null,
	);

	/**
	 * @since 1.0.2
	 * @var int
	 */
	protected $subunit;

	/**
	 * Store global currencies.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	public $global_currencies;

	/**
	 * Get the currency if ID is passed, otherwise the currency is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_currency function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param int|object|string|Currency $data Order to read.
	 *
	 * @since 1.0.2
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		$this->global_currencies = eaccounting_get_global_currencies();

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_string( $data ) && $this->is_valid_currency_code( $data ) ) {
			$this->populate_by_code( $data );
		} else {
			$this->set_id( 0 );
		}

		if ( $this->get_id() > 0 && ! $this->object_read ) {
			$this->read();
		}
	}

	/**
	 * Checks if given code is valid.
	 *
	 * @param $code
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_valid_currency_code( $code ) {
		return array_key_exists( strtoupper( $code ), $this->global_currencies );
	}

	/**
	 * @param string $code
	 *
	 * @since 1.0.2
	 *
	 * @return array|Object
	 */
	public function get_by_code( $code ) {
		$code = strtoupper( $code );

		return query()->find( $code, 'code' );
	}

	/**
	 * Populate data based on the object or array passed.
	 *
	 * @param string $code Object data.
	 *
	 * @since 1.0.2
	 * @throws \EverAccounting\Exception
	 */
	public function populate_by_code( $code ) {
		$currency = $this->get_by_code( $code );
		if ( is_object( $currency ) ) {
			$currency = get_object_vars( $currency );
		}
		if ( null === $currency ) {
			$currency = array();
		}
		$attributes = $this->global_currencies[ $code ];
		$this->populate( array_merge( $attributes, $currency ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get currency name.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get currency code.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_code( $context = 'edit' ) {
		return $this->get_prop( 'code', $context );
	}

	/**
	 * Get currency rate.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_rate( $context = 'edit' ) {
		return $this->get_prop( 'rate', $context );
	}

	/**
	 * Get number of decimal points.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_precision( $context = 'edit' ) {
		return $this->get_prop( 'precision', $context );
	}

	/**
	 * Get currency symbol.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_symbol( $context = 'edit' ) {
		return $this->get_prop( 'symbol', $context );
	}

	/**
	 * Get symbol position.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_position( $context = 'edit' ) {
		return $this->get_prop( 'position', $context );
	}

	/**
	 * Get decimal separator.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_decimal_separator( $context = 'edit' ) {
		return $this->get_prop( 'decimal_separator', $context );
	}

	/**
	 * Get thousand separator.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
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
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_name( $value ) {
		$this->set_prop( 'name', eaccounting_clean( $value ) );
	}

	/**
	 * Set the code.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_code( $value ) {
		$code = strtoupper( eaccounting_clean( $value ) );
		if ( ! $this->is_valid_currency_code( $value ) ) {
			$this->error( 'invalid_currency_code', __( 'Unsupported currency code', 'wp-ever-accounting' ) );
		}
		$this->set_prop( 'code', $code );
	}

	/**
	 * Set the code.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_rate( $value ) {
		$this->set_prop( 'rate', eaccounting_sanitize_number( $value, true ) );
	}

	/**
	 * Set precision.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_precision( $value ) {
		$this->set_prop( 'precision', eaccounting_sanitize_number( $value ) );
	}

	/**
	 * Set symbol.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_symbol( $value ) {
		$this->set_prop( 'symbol', eaccounting_clean( $value ) );
	}

	/**
	 * Set symbol position.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_position( $value ) {
		$this->set_prop( 'position', eaccounting_clean( $value ) );
	}

	/**
	 * Set decimal separator.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_decimal_separator( $value ) {
		$this->set_prop( 'decimal_separator', eaccounting_clean( $value ) );
	}

	/**
	 * Set thousand separator.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
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
		return $this->subunit;
	}

	/**
	 * Set subunit.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_subunit( $value ) {
		$this->subunit = absint( $value );
	}

	/**
	 * equals.
	 *
	 * @param Currency $currency
	 *
	 * @since 1.0.2
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
	 * Convert the object to its JSON representation.
	 *
	 * @param int $options
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function toJson( $options = 0 ) {
		return json_encode( $this->get_base_data(), $options );
	}

	/**
	 * Get the evaluated contents of the object.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function render() {
		return $this->get_code( 'edit' ) . ' (' . $this->get_name( 'edit' ) . ')';
	}

	/**
	 * __toString.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}

	/**
	 * __callStatic.
	 *
	 * @param string $method
	 * @param array  $arguments
	 *
	 * @since 1.0.2
	 * @return Currency
	 */
	public static function __callStatic( $method, array $arguments ) {
		return new static( $method, $arguments );
	}
}
