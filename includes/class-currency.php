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

	/**
	 * Retrieve the object from database instance.
	 *
	 * @param int|string $id Object id.
	 * @param string     $field Database field.
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
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	public function save() {
		// TODO: Implement save() method.
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
		 * Fires before an currency is deleted.
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
		 * Fires after an currency is deleted.
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
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting item data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * Overwrite base so it can accept string.
	 *
	 * Set ID.
	 *
	 * @param int $id ID.
	 *
	 * @since 1.1.0
	 */
	public function set_id( $id ) {
		$this->id = eaccounting_clean( $id );
	}

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
