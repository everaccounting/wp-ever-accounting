<?php
/**
 * Handle the Currency object.
 *
 * @package     EverAccounting
 * @class       Contact
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Currency object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property string $name
 * @property string $code
 * @property float $rate
 * @property int $precision
 * @property string $symbol
 * @property string $position
 * @property string $decimal_separator
 * @property string $thousand_separator
 * @property string $date_created
 */
class Currency extends Data {

	/**
	 * Currency data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'name'               => '',
		'code'               => '',
		'rate'               => 1,
		'number'             => '',
		'precision'          => 2,
		'subunit'            => 100,
		'symbol'             => '',
		'position'           => 'before',
		'decimal_separator'  => '.',
		'thousand_separator' => ',',
		'date_created'       => null,
	);

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data_type = array(
		'id'                 => '%d',
		'name'               => '%s',
		'code'               => '%s',
		'rate'               => '%f',
		'precision'          => '%d',
		'symbol'             => '%s',
		'subunit'            => '%d',
		'position'           => '%s',
		'decimal_separator'  => '%s',
		'thousand_separator' => '%s',
		'date_created'       => '%s',
	);

	/**
	 * Currency constructor.
	 *
	 * Get the Currency if ID is passed, otherwise the currency is new and empty.
	 *
	 * @param int|object|Currency $currency object to read.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $currency = 0 ) {
		parent::__construct();
		if ( $currency instanceof self ) {
			$this->set_id( $currency->get_id() );
		} elseif ( is_object( $currency ) && ! empty( $currency->id ) ) {
			$this->set_id( $currency->id );
		} elseif ( is_array( $currency ) && ! empty( $currency['id'] ) ) {
			$this->set_props( $currency );
		} elseif ( is_numeric( $currency ) ) {
			$this->set_id( $currency );
		} elseif ( is_string( $currency ) ) {
			$this->set_code( $currency );
		} else {
			$this->set_object_read( true );
		}

		$data = false;
		if ( ! empty( $this->code ) ) {
			$data = self::get_raw( $this->code, 'code' );
		} elseif ( ! empty( $this->get_id() ) ) {
			$data = self::get_raw( $this->get_id(), 'id' );
		}

		if ( $data ) {
			$this->set_props( $data );
			$this->set_object_read( true );
		} else {
			$this->set_id( 0 );
			$this->set_code( '' );
		}
	}
	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete documents from the database.
	| Written in abstract fashion so that the way documents are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/
	/**
	 * Retrieve the object from database instance.
	 *
	 * @param int|string $id Object id.
	 * @param string $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_raw( $id, $field = 'id' ) {
		global $wpdb;
		if ( 'id' === $field ) {
			$id = (int) $id;
		} else {
			$id = trim( $id );
		}
		if ( ! $id ) {
			return false;
		}

		$currency = wp_cache_get( $id, 'ea_currencies' );

		if ( ! $currency ) {

			switch ( $field ) {
				case 'code':
					$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_currencies WHERE code = %s LIMIT 1", $id );
					break;
				case 'id':
				default:
					$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_currencies WHERE id = %d LIMIT 1", $id );
					break;
			}

			$currency = $wpdb->get_row( $sql ); //phpcs:ignore

			if ( ! $currency ) {
				return false;
			}

			wp_cache_add( $currency->id, $currency, 'ea_currencies' );
			wp_cache_add( $currency->code, $currency, 'ea_currencies' );
		}

		return apply_filters( 'eaccounting_currency_raw_item', $currency );
	}

	/**
	 *  Update a currency in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function update( $args = array() ) {
		global $wpdb;
		$changes = $this->get_changes();
		$data    = wp_array_slice_assoc( $changes, array_keys( $this->data_type ) );
		$format  = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data    = wp_unslash( $data );
		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before an existing currency is updated in the database.
		 *
		 * @param int $currency_id Currency id.
		 * @param array $data Currency data.
		 * @param array $changes The data will be updated.
		 * @param Currency $currency Currency object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_currency', $this->get_id(), $this->to_array(), $changes, $this );

		if ( false === $wpdb->update( $wpdb->prefix . 'ea_currencies', $data, [ 'id' => $this->get_id() ], $format, [ 'id' => '%d' ] ) ) {
			return new \WP_Error( 'db_update_error', __( 'Could not update currency in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing currency is updated in the database.
		 *
		 * @param int $currency_id Currency id.
		 * @param array $data Currency data.
		 * @param array $changes The data will be updated.
		 * @param Currency $currency Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_currency', $this->get_id(), $this->to_array(), $changes, $this );

		return true;
	}

	/**
	 *  Insert a currency in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function insert( $args = array() ) {
		global $wpdb;
		$data_arr = $this->to_array();
		$data     = wp_array_slice_assoc( $data_arr, array_keys( $this->data_type ) );
		$format   = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data     = wp_unslash( $data );

		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before a currency is inserted in the database.
		 *
		 * @param array $data Currency data to be inserted.
		 * @param string $data_arr Sanitized currency data.
		 * @param Currency $currency Currency object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_currency', $data, $data_arr, $this );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_currencies', $data, $format ) ) {
			return new \WP_Error( 'db_insert_error', __( 'Could not insert currency into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );

		/**
		 * Fires immediately after a currency is inserted in the database.
		 *
		 * @param int $currency_id Currency id.
		 * @param array $data Currency has been inserted.
		 * @param array $data_arr Sanitized currency data.
		 * @param Currency $currency Currency object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_currency', $this->id, $data, $data_arr, $this );

		return true;
	}

	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|int True on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	public function save() {
		// check if the code is available or not
		if ( empty( $this->get_prop( 'code' ) ) ) {
			return new \WP_Error( 'invalid_currency_code', esc_html__( 'Currency code is required', 'wp-ever-accounting' ) );
		}

		// check if the currency rate is available or not
		if ( empty( $this->get_prop( 'rate' ) ) ) {
			return new \WP_Error( 'invalid_currency_rate', esc_html__( 'Currency rate is required', 'wp-ever-accounting' ) );
		}

		// check if the currency symbol is available or not
		if ( empty( $this->get_prop( 'symbol' ) ) ) {
			return new \WP_Error( 'invalid_currency_symbol', esc_html__( 'Currency symbol is required', 'wp-ever-accounting' ) );
		}

		// check if the currency position is available or not
		if ( empty( $this->get_prop( 'position' ) ) ) {
			return new \WP_Error( 'invalid_currency_position', esc_html__( 'Currency position is required', 'wp-ever-accounting' ) );
		}

		// check if the currency decimal_separator is available or not
		if ( empty( $this->get_prop( 'decimal_separator' ) ) ) {
			return new \WP_Error( 'invalid_currency_decimal_separator', esc_html__( 'Currency decimal separator is required', 'wp-ever-accounting' ) );
		}

		// check if the currency thousand_separator is available or not
		if ( empty( $this->get_prop( 'thousand_separator' ) ) ) {
			return new \WP_Error( 'invalid_currency_thousand_separator', esc_html__( 'Currency thousand separator is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'date_created' ) ) || '0000-00-00 00:00:00' === $this->get_prop( 'date_created' ) ) {
			$this->set_date_prop( 'date_created', current_time( 'mysql' ) );
		}

		if ( $this->exists() ) {
			$is_error = $this->update();
		} else {
			$is_error = $this->insert();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), 'ea_currencies' );
		wp_cache_set( 'last_changed', microtime(), 'ea_currencies' );

		/**
		 * Fires immediately after a currency is inserted or updated in the database.
		 *
		 * @param int $currency_id Currency id.
		 * @param Item $currency Currency object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_saved_currency', $this->get_id(), $this );

		return $this->get_id();
	}

	/**
	 * Deletes the currency from database.
	 *
	 * @return array|false true on success, false on failure.
	 * @since 1.1.0
	 */
	public function delete() {
		global $wpdb;
		if ( ! $this->exists() ) {
			return false;
		}

		$data = $this->to_array();

		/**
		 * Filters whether a currency delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $currency_id Currency id.
		 * @param array $data Currency data array.
		 * @param Currency $currency Transaction object.
		 *
		 * @since 1.2.1
		 */
		$check = apply_filters( 'eaccounting_check_delete_currency', null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before a currency is deleted.
		 *
		 * @param int $currency_id Currency id.
		 * @param array $data Currency data array.
		 * @param Currency $currency Currency object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_delete_currency', $this->get_id(), $data, $this );

		$result = $wpdb->delete( $wpdb->prefix . 'ea_currencies', array( 'id' => $this->get_id() ) );
		if ( ! $result ) {
			return false;
		}

		/**
		 * Fires after a currency is deleted.
		 *
		 * @param int $currency_id Currency id.
		 * @param array $data Currency data array.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_delete_currency', $this->get_id(), $data );

		// Clear object.
		wp_cache_delete( $this->get_id(), 'ea_currencies' );
		wp_cache_set( 'last_changed', microtime(), 'ea_currencies' );
		$this->set_id( 0 );
		$this->set_defaults();

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Functions for getting item data. Getter methods won't change anything unless
	| just returning from the props.
	|
	*/

	/**
	 * Get currency name.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_name() {
		return $this->get_prop( 'name' );
	}

	/**
	 * Get currency code.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_code() {
		return $this->get_prop( 'code' );
	}

	/**
	 * Get currency rate.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_rate() {
		return $this->get_prop( 'rate' );
	}

	/**
	 * Get currency number.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_number() {
		return $this->get_prop( 'number' );
	}

	/**
	 * Get number of decimal points.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_precision() {
		return $this->get_prop( 'precision' );
	}

	/**
	 * Get number of decimal points.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_subunit() {
		return $this->get_prop( 'subunit' );
	}

	/**
	 * Get currency symbol.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_symbol() {
		return $this->get_prop( 'symbol' );
	}

	/**
	 * Get symbol position.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_position() {
		return $this->get_prop( 'position' );
	}

	/**
	 * Get decimal separator.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_decimal_separator() {
		return $this->get_prop( 'decimal_separator' );
	}

	/**
	 * Get thousand_separator.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_thousand_separator() {
		return $this->get_prop( 'thousand_separator' );
	}

	/**
	 * Get object created date.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_date_created() {
		return $this->get_prop( 'date_created' );
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
	 * @param string $value Currency Name
	 *
	 * @since 1.0.2
	 */
	public function set_name( $value ) {
		$this->set_prop( 'name', eaccounting_clean( $value ) );
	}

	/**
	 * Set the code.
	 *
	 * @param string $code Code
	 *
	 * @since 1.0.2
	 */
	public function set_code( $code ) {
		$code = eaccounting_sanitize_currency_code( $code );
		if ( ! empty( $code ) ) {
			$this->set_prop( 'code', $code );
		}
	}

	/**
	 * Set the rate.
	 *
	 * @param float $value Rate
	 *
	 * @since 1.0.2
	 */
	public function set_rate( $value ) {
		$this->set_prop( 'rate', (float) $value );
	}

	/**
	 * Set the number.
	 *
	 * @param int $value number
	 *
	 * @since 1.0.2
	 */
	public function set_number( $value ) {
		$this->set_prop( 'number', (int) $value );
	}

	/**
	 * Set precision.
	 *
	 * @param int $value Precision
	 *
	 * @since 1.0.2
	 */
	public function set_precision( $value ) {
		$this->set_prop( 'precision', (int) $value );
	}

	/**
	 * Set precision.
	 *
	 * @param int $value Subunit
	 *
	 * @since 1.0.2
	 */
	public function set_subunit( $value ) {
		$this->set_prop( 'subunit', (int) $value );
	}

	/**
	 * Set symbol.
	 *
	 * @param string $value Symbol
	 *
	 * @since 1.0.2
	 */
	public function set_symbol( $value ) {
		$this->set_prop( 'symbol', eaccounting_clean( $value ) );
	}

	/**
	 * Set symbol position.
	 *
	 * @param string $value Position
	 *
	 * @since 1.0.2
	 */
	public function set_position( $value ) {
		if ( in_array( $value, array( 'before', 'after' ), true ) ) {
			$this->set_prop( 'position', eaccounting_clean( $value ) );
		}
	}

	/**
	 * Set decimal separator.
	 *
	 * @param string $value Decimal Separator
	 *
	 * @since 1.0.2
	 */
	public function set_decimal_separator( $value ) {
		$this->set_prop( 'decimal_separator', eaccounting_clean( $value ) );
	}

	/**
	 * Set thousands separator.
	 *
	 * @param string $value Thousand-Separator
	 *
	 * @since 1.0.2
	 */
	public function set_thousand_separator( $value ) {
		$this->set_prop( 'thousand_separator', eaccounting_clean( $value ) );
	}

	/**
	 * Set object created date.
	 *
	 * @param string $date Creation date
	 *
	 * @since 1.0.2
	 */
	public function set_date_created( $date = null ) {
		if ( null === $date ) {
			$date = current_time( 'mysql' );
		}
		$this->set_date_prop( 'date_created', $date );
	}
}
