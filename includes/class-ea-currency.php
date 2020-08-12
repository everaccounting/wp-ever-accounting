<?php
/**
 * Handle the currency object.
 *
 * @package     EverAccounting
 * @class       Currency
 * @version     1.0.2
 *
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Base_Object;
use EverAccounting\Exception;

defined( 'ABSPATH' ) || exit();

class Currency extends Base_Object {
	/**
	 * A group must be set to to enable caching.
	 *
	 * @var string
	 * @since 1.0.2
	 */
	protected $cache_group = 'currencies';

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 * @since 1.0.2
	 */
	public $object_type = 'currency';

	/**
	 * @var int
	 * @since 1.0.2
	 */
	protected $subunit;

	/**
	 * Currency Data array.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $data
		= array(
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
	 * Get the currency if ID is passed, otherwise the currency is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_currency function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param int|object|Currency $data Order to read.
	 *
	 * @since 1.0.2
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} else {
			$this->set_id( 0 );
		}

		if ( $this->get_id() > 0 ) {
			$this->read( $this->get_id() );
		}
	}


	/*
	|--------------------------------------------------------------------------
	| Crud
	|--------------------------------------------------------------------------
	*/

	/**
	 * Method to validate before inserting and updating EverAccounting object.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	public function validate_props() {
		global $wpdb;

		if ( ! $this->get_date_created( 'edit' ) ) {
			$this->set_date_created( time() );
		}

		if ( ! $this->get_company_id( 'edit' ) ) {
			$this->set_company_id( 1 );
		}

		if ( empty( $this->get_code( 'edit' ) ) ) {
			throw new Exception( 'empty-code', __( 'Currency code is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_rate( 'edit' ) ) ) {
			throw new Exception( 'empty-rate', __( 'Currency rate is required', 'wp-ever-accounting' ) );
		}

		$currencies = eaccounting_get_global_currencies();
		$code       = $this->get_code( 'edit' );
		$currency   = $currencies[ $code ];


		if ( empty( $this->get_name( 'edit' ) ) ) {
			$this->set_name( $currency['name'] );
		}

		if ( empty( $this->get_symbol( 'edit' ) ) ) {
			$this->set_symbol( $currency['symbol'] );
		}

		if ( empty( $this->get_position( 'edit' ) ) ) {
			$this->set_position( $currency['position'] );
		}

		if ( empty( $this->get_decimal_separator( 'edit' ) ) ) {
			$this->set_decimal_separator( $currency['decimal_separator'] );
		}

		if ( empty( $this->get_thousand_separator( 'edit' ) ) ) {
			$this->set_thousand_separator( $currency['thousand_separator'] );
		}

		if ( $existing_id = $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_currencies where code=%s", $this->get_code( 'edit' ) ) ) ) {
			if ( ! empty( $existing_id ) && $existing_id != $this->get_id() ) {
				throw new Exception( 'invalid-code', __( 'Duplicate currency code.', 'wp-ever-accounting' ) );
			}
		}
	}

	/**
	 * Method to read a record. Creates a new EAccounting_Object based object.
	 *
	 * @param int $id ID of the object to read.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	public function read( $id ) {
		global $wpdb;
		$this->set_defaults();

		// Get from cache if available.
		$item = 0 < $id ? wp_cache_get( $this->object_type . '-item-' . $id, $this->cache_group ) : false;

		if ( false === $item ) {
			$item = $wpdb->get_row( $wpdb->prepare( "SELECT * from {$wpdb->prefix}ea_currencies where id=%d", $id ) );
			if ( $item && 0 < $item->id ) {
				wp_cache_set( $this->object_type . '-item-' . $item->id, $item, $this->cache_group );
			}
		}

		if ( ! $item || ! $item->id ) {
			throw new Exception( 'invalid-id', __( 'Invalid currency.', 'wp-ever-accounting' ) );
		}

		$this->populate( $item );
	}

	/**
	 * Create a new account in the database.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	public function create() {
		$this->validate_props();
		global $wpdb;
		$currency_data = array(
			'name'               => $this->get_name( 'edit' ),
			'code'               => $this->get_code( 'edit' ),
			'rate'               => $this->get_rate( 'edit' ),
			'precision'          => $this->get_precision( 'edit' ),
			'symbol'             => $this->get_symbol( 'edit' ),
			'position'           => $this->get_position( 'edit' ),
			'decimal_separator'  => $this->get_decimal_separator( 'edit' ),
			'thousand_separator' => $this->get_thousand_separator( 'edit' ),
			'enabled'            => $this->get_enabled( 'edit' ),
			'date_created'       => $this->get_date_created( 'edit' )->get_mysql_date(),
		);

		do_action( 'eaccounting_pre_insert_currency', $this->get_id(), $this );

		$data = wp_unslash( apply_filters( 'eaccounting_new_currency_data', $currency_data ) );
		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_currencies', $data ) ) {
			throw new Exception( 'db-error', $wpdb->last_error );
		}

		do_action( 'eaccounting_insert_currency', $this->get_id(), $this );

		$this->set_id( $wpdb->insert_id );
		$this->apply_changes();
		$this->set_object_read( true );
	}

	/**
	 * Update a account in the database.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 *
	 */
	public function update() {
		global $wpdb;

		$this->validate_props();
		$changes = $this->get_changes();
		if ( ! empty( $changes ) ) {
			do_action( 'eaccounting_pre_update_currency', $this->get_id(), $changes );

			try {
				$wpdb->update( $wpdb->prefix . 'ea_currencies', $changes, array( 'id' => $this->get_id() ) );
			} catch ( Exception $e ) {
				throw new Exception( 'db-error', __( 'Could not update currency.', 'wp-ever-accounting' ) );
			}

			do_action( 'eaccounting_update_currency', $this->get_id(), $changes, $this->data );

			$this->apply_changes();
			$this->set_object_read( true );
			wp_cache_delete( 'transaction-currency-' . $this->get_id(), 'currencies' );
		}
	}

	/**
	 * Remove an account from the database.
	 *
	 * @param array $args
	 *
	 * @since 1.0.2
	 */
	public function delete( $args = array() ) {
		if ( $this->get_id() ) {
			global $wpdb;
			do_action( 'eaccounting_pre_delete_currency', $this->get_id() );
			$wpdb->delete( $wpdb->prefix . 'ea_currencies', array( 'id' => $this->get_id() ) );
			do_action( 'eaccounting_delete_currency', $this->get_id() );
			$this->set_id( 0 );
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
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get currency code.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_code( $context = 'view' ) {
		return $this->get_prop( 'code', $context );
	}

	/**
	 * Get currency rate.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_rate( $context = 'view' ) {
		return $this->get_prop( 'rate', $context );
	}

	/**
	 * Get number of decimal points.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_precision( $context = 'view' ) {
		return $this->get_prop( 'precision', $context );
	}

	/**
	 * Get currency symbol.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_symbol( $context = 'view' ) {
		return $this->get_prop( 'symbol', $context );
	}

	/**
	 * Get symbol position.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_position( $context = 'view' ) {
		return $this->get_prop( 'position', $context );
	}

	/**
	 * Get decimal separator.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_decimal_separator( $context = 'view' ) {
		return $this->get_prop( 'decimal_separator', $context );
	}

	/**
	 * Get thousand separator.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_thousand_separator( $context = 'view' ) {
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
		$code       = strtoupper( eaccounting_clean( $value ) );
		$currencies = eaccounting_get_global_currencies();
		if ( ! array_key_exists( $code, $currencies ) ) {
			$this->error( 'invalid_currency_code', __( 'Unsupported currency code', 'wp-ever-accounting' ) );
		}
		$currency = $currencies[ $code ];
		$this->set_prop( 'code', $code );

		$this->subunit = (int) $currency['subunit'];

		if ( empty( $this->get_name( 'edit' ) ) ) {
			$this->set_name( $currency['name'] );
		}

		if ( empty( $this->get_symbol( 'edit' ) ) ) {
			$this->set_symbol( $currency['symbol'] );
		}

		if ( empty( $this->get_position( 'edit' ) ) ) {
			$this->set_position( $currency['precision'] );
		}

		if ( empty( $this->get_decimal_separator( 'edit' ) ) ) {
			$this->set_decimal_separator( $currency['decimal_separator'] );
		}

		if ( empty( $this->get_thousand_separator( 'edit' ) ) ) {
			$this->set_thousand_separator( $currency['thousand_separator'] );
		}
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
	 * __callStatic.
	 *
	 * @param string $method
	 * @param array $arguments
	 *
	 * @return Currency
	 * @since 1.0.2
	 */
	public static function __callStatic( $method, array $arguments ) {
		return new static( $method, $arguments );
	}

	/**
	 * equals.
	 *
	 * @param Currency $currency
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function equals( self $currency ) {
		return $this->get_code( 'edit' ) === $currency->get_code( 'edit' );
	}

	/**
	 * getSubunit.
	 *
	 * @return int
	 * @since 1.0.2
	 */
	public function get_subunit() {
		return $this->subunit;
	}

	/**
	 * is_symbol_first.
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function is_symbol_first() {
		return 'before' === $this->get_position( 'edit' );
	}

	/**
	 * getPrefix.
	 *
	 * @return string
	 * @since 1.0.2
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
	 * @return string
	 * @since 1.0.2
	 */
	public function get_suffix() {
		if ( $this->is_symbol_first() ) {
			return '';
		}

		return ' ' . $this->get_symbol( 'edit' );
	}

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function toArray() {
		return [
			'name'               => $this->get_name( 'edit' ),
			'code'               => $this->get_code( 'edit' ),
			'precision'          => $this->get_precision( 'edit' ),
			'subunit'            => $this->get_subunit(),
			'symbol'             => $this->get_symbol( 'edit' ),
			'position'           => $this->get_position( 'edit' ),
			'decimal_separator'  => $this->get_decimal_separator( 'edit' ),
			'thousand_separator' => $this->get_thousand_separator( 'edit' ),
			'prefix'             => $this->get_prefix(),
			'suffix'             => $this->get_suffix(),
		];
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param int $options
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function toJson( $options = 0 ) {
		return json_encode( $this->toArray(), $options );
	}

	/**
	 * jsonSerialize.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function json_serialize() {
		return $this->toArray();
	}

	/**
	 * Get the evaluated contents of the object.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function render() {
		return $this->get_code( 'edit' ) . ' (' . $this->get_name( 'edit' ) . ')';
	}

	/**
	 * __toString.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function __toString() {
		return $this->render();
	}

	/**
	 * Get the value for select option.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_select_option() {
		return array( $this->get_code() => sprintf( '%(%s)', $this->get_name(), $this->get_symbol() ) );
	}
}
