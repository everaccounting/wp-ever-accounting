<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Currency.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Currency extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_currencies';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'currency';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_currencies';


	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'code'               => '',
		'name'               => '',
		'rate'               => 1,
		'precision'          => 0,
		'symbol'             => '',
		'thousand_separator' => ',',
		'decimal_separator'  => '.',
		'position'           => 'before',
		'status'             => 'active',
		'updated_at'         => '',
		'created_at'         => '',
	);

	/**
	 * Model constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );
		if ( ! is_numeric( $data ) && strlen( $data ) === 3 ) {
			$this->set_code( $data );
			$this->object_read = false;
			$this->read();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data.
	|
	*/

	/**
	 * Get currency name.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set currency name.
	 *
	 * @param string $name Currency name.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', sanitize_text_field( $name ) );
	}

	/**
	 * Get currency code.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_code( $context = 'edit' ) {
		return $this->get_prop( 'code', $context );
	}

	/**
	 * Set the code.
	 *
	 * @param string $code Currency code.
	 *
	 * @since 1.0.2
	 */
	public function set_code( $code ) {
		if ( ! empty( $code ) ) {
			$this->set_prop( 'code', $code );
		}
	}

	/**
	 * Get currency rate.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_rate( $context = 'edit' ) {
		return $this->get_prop( 'rate', $context );
	}

	/**
	 * Set the rate.
	 *
	 * @param string $value Currency rate.
	 *
	 * @since 1.0.2
	 */
	public function set_rate( $value ) {
		$this->set_prop( 'rate', eac_format_decimal( $value, 7 ) );
	}

	/**
	 * Get currency symbol.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_symbol( $context = 'edit' ) {
		return $this->get_prop( 'symbol', $context );
	}

	/**
	 * Set the symbol.
	 *
	 * @param string $symbol Currency symbol.
	 *
	 * @since 1.0.2
	 */
	public function set_symbol( $symbol ) {
		$this->set_prop( 'symbol', sanitize_text_field( $symbol ) );
	}

	/**
	 * Get thousand sep.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_thousand_separator( $context = 'edit' ) {
		return $this->get_prop( 'thousand_separator', $context );
	}

	/**
	 * Set the thousand sep.
	 *
	 * @param string $sep A Thousand sep.
	 *
	 * @since 1.0.2
	 */
	public function set_thousand_separator( $sep ) {
		$this->set_prop( 'thousand_separator', sanitize_text_field( $sep ) );
	}

	/**
	 * Get decimal sep.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_decimal_separator( $context = 'edit' ) {
		return $this->get_prop( 'decimal_separator', $context );
	}

	/**
	 * Set the decimal sep.
	 *
	 * @param string $sep A Decimal sep.
	 *
	 * @since 1.0.2
	 */
	public function set_decimal_separator( $sep ) {
		$this->set_prop( 'decimal_separator', sanitize_text_field( $sep ) );
	}

	/**
	 * Get number of decimal points.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_precision( $context = 'edit' ) {
		return $this->get_prop( 'precision', $context );
	}

	/**
	 * Set precision.
	 *
	 * @param string $precision Currency precision.
	 *
	 * @since 1.0.2
	 */
	public function set_precision( $precision ) {
		// if the value more than 2, set it to 2.
		if ( $precision > 2 ) {
			$precision = 2;
		}
		$this->set_prop( 'precision', intval( $precision ) );
	}

	/**
	 * Get currency position.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_position( $context = 'edit' ) {
		return $this->get_prop( 'position', $context );
	}

	/**
	 * Set the position.
	 *
	 * @param string $position Currency position.
	 *
	 * @since 1.0.2
	 */
	public function set_position( $position ) {
		$this->set_prop( 'position', sanitize_text_field( $position ) );
	}

	/**
	 * Get status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Set status.
	 *
	 * @param string $status Status.
	 *
	 * @since 1.0.2
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', $status );
	}

	/**
	 * Is currency active.
	 *
	 * @since 1.0.2
	 * @return bool
	 */
	public function is_active() {
		return 'active' === $this->get_status();
	}

	/**
	 * Get the date updated.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_updated_at( $context = 'edit' ) {
		return $this->get_prop( 'updated_at', $context );
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $updated_at date updated.
	 */
	public function set_updated_at( $updated_at ) {
		$this->set_date_prop( 'updated_at', $updated_at );
	}

	/**
	 * Get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_created_at( $context = 'edit' ) {
		return $this->get_prop( 'created_at', $context );
	}

	/**
	 * Set the date created.
	 *
	 * @param string $created_at date created.
	 */
	public function set_created_at( $created_at ) {
		$this->set_date_prop( 'created_at', $created_at );
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
	 * Set a collection of props in one go, collect any errors, and return the result.
	 * Only sets using public methods.
	 *
	 * @param array|object $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function set_props( $props ) {
		if ( is_object( $props ) ) {
			$props = get_object_vars( $props );
		}
		if ( ! is_array( $props ) ) {
			return;
		}

		$info = include ever_accounting()->get_dir_path( 'i18n/currencies.php' );
		$code = isset( $props['code'] ) ? $props['code'] : $this->get_code();
		if ( isset( $info[ $code ] ) ) {
			$props = array_merge( $info[ $code ], $props );
		}

		parent::set_props( $props );
	}

	/**
	 * Retrieve the object from database instance.
	 *
	 * @since 1.0.0
	 *
	 * @return object|false Object, false otherwise.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	protected function read() {
		global $wpdb;
		// Check code cache first.
		if ( $this->get_code() && ! $this->get_id() ) {
			$id = wp_cache_get( $this->get_code(), static::CACHE_GROUP );
			if ( false === $id ) {
				$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}$this->table WHERE code = %s", $this->get_code() ) );
				wp_cache_set( $this->get_code(), $id, static::CACHE_GROUP );
			}
		}

		$data = parent::read();

		if ( $data ) {
			wp_cache_set( $this->get_code(), $this->get_id(), static::CACHE_GROUP );
		}

		return $data;
	}

	/**
	 * Retrieve the object instance.
	 *
	 * @param mixed $id Object id to retrieve.
	 *
	 * @since 1.0.0
	 *
	 * @return static|false Object instance on success, false on failure.
	 */
	public static function get( $id ) {
		if ( ! is_array( $id ) && ! is_numeric( $id ) && wp_cache_get( $id, static::CACHE_GROUP ) ) {
			$id = wp_cache_get( $id, static::CACHE_GROUP );
		}

		return parent::get( $id );
	}


	/**
	 * Deletes the object from database.
	 *
	 * @since 1.0.0
	 * @return array|false true on success, false on failure.
	 */
	public function delete() {
		if ( eac_get_base_currency() === $this->get_code() ) {
			return false;
		}

		return parent::delete();
	}

	/**
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		// Required fields check.
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'missing_required', __( 'Currency name is required.', 'wp-ever-accounting' ) );
		}

		// Code is required field.
		if ( empty( $this->get_code() ) ) {
			return new \WP_Error( 'missing_required', __( 'Currency code is required.', 'wp-ever-accounting' ) );
		}

		// Rate should be greater than 0.
		if ( $this->get_rate() <= 0 ) {
			return new \WP_Error( 'invalid_rate', __( 'Rate should be greater than 0.', 'wp-ever-accounting' ) );
		}

		// Duplicate check.
		$currency = self::get( $this->get_code() );
		if ( $currency && $currency->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate_currency', __( 'Currency already exists.', 'wp-ever-accounting' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_created_at() ) ) {
			$this->set_created_at( current_time( 'mysql' ) );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_updated_at( current_time( 'mysql' ) );
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

	/**
	 * Get formatted name.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_formatted_name() {
		return sprintf( '%s (%s)', $this->get_name(), $this->get_code() );
	}
}
