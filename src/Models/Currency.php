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
use EverAccounting\Core\Repositories;
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
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'currency';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	public $cache_group = 'eaccounting_currency';

	/**
	 * Item Data array.
	 *
	 * @since 1.0.4
	 * @var array
	 */
	protected $data = array(
		'name'               => '',
		'code'               => '',
		'rate'               => 1,
		'precision'          => 0,
		'symbol'             => '',
		'position'           => 'before',
		'decimal_separator'  => '.',
		'thousand_separator' => ',',
		'enabled'            => 1,
		'date_created'       => null,
	);

	/**
	 * Get the category if ID is passed, otherwise the category is new and empty.
	 *
	 * @param int|string|object|Item $item Item object to read.
	 */
	public function __construct( $item = 0 ) {
		parent::__construct( $item );

		if ( $item instanceof self ) {
			$this->set_id( $item->get_id() );
		} elseif ( is_numeric( $item ) ) {
			$this->set_id( $item );
		} elseif ( is_string( $item )
		           && eaccounting_get_currency_data( $item ) // @codingStandardsIgnoreLine
		           && $id = $this->get_id_by_code( $item ) ) { // @codingStandardsIgnoreLine
			$this->set_id( $id );
		} elseif ( is_string( $item )
		           && eaccounting_get_currency_data( $item ) // @codingStandardsIgnoreLine
		           && ! $this->get_id_by_code( $item ) ) { // @codingStandardsIgnoreLine
			$this->set_props( eaccounting_get_currency_data( $item ) );
		} elseif ( ! empty( $item->id ) ) {
			$this->set_id( $item->id );
		} elseif ( is_array( $item ) ) {
			$this->set_props( $item );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( $this->object_type );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/

	/**
	 * Get currency id by code.
	 *
	 * @since 1.1.0
	 *
	 * @param $code
	 *
	 * @return bool|false|mixed|string|null
	 */
	public function get_id_by_code( $code ) {
		if ( $code ) {
			return false;
		}
		$id = wp_cache_get( $code, $this->cache_group );
		if ( false === $id ) {
			global $wpdb;
			$id = $wpdb->get_var( "SELECT id from {$wpdb->prefix}ea_currencies where code = %s", $code );
			wp_cache_add( $code, $id, $this->cache_group );
		}

		return $id;
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
	|
	| Functions for setting item data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
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
	 * @param $code
	 */
	public function set_code( $code ) {
		if ( eaccounting_get_currency_data( $code ) ) {
			$this->set_prop( 'code', $code );
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
}
