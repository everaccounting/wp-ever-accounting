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
	public $table_name = 'ea_currencies';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'currency';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'id'                 => null,
		'code'               => '',
		'name'               => '',
		'precision'          => 0,
		'symbol'             => '',
		'decimal_separator'  => '.',
		'thousand_separator' => ',',
		'position'           => 'before',
		'exchange_rate'      => 1,
		'auto_update'        => 0,
		'status'             => 'active',
		'date_updated'       => '',
		'date_created'       => '',
	);

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
	 * @return void
	 * @since  1.0.0
	 */
	public function set_data( $props ) {
		if ( is_object( $props ) ) {
			$props = get_object_vars( $props );
		}
		if ( ! is_array( $props ) ) {
			return;
		}

		$info = include EAC()->get_dir_path( 'i18n/currencies.php' );
		$code = isset( $props['code'] ) ? $props['code'] : $this->get_code();
		if ( isset( $info[ $code ] ) ) {
			$props = wp_parse_args( $props, $info[ $code ] );
		}

		parent::set_data( $props );
	}

	/**
	 * Retrieve the object from database instance.
	 *
	 * @param int|string $key Unique identifier for the object.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.0.0
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	protected function read( $key ) {
		global $wpdb;
		// Check code cache first.
		if ( ! is_numeric( $key ) && strlen( $key ) === 3 ) {
			$id = $this->get_cache( $key );
			if ( false === $id ) {
				$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->{$this->table_name}} WHERE code = %s", $key ) );
			}

			if ( $id ) {
				$key = $id;
			}
		}

		$data = parent::read( $key );

		if ( $data ) {
			wp_cache_set( $this->get_code(), $this->get_id(), $this->cache_group );
		}

		return $data;
	}


	/**
	 * Deletes the object from database.
	 *
	 * @return array|false true on success, false on failure.
	 * @since 1.0.0
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
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 * @since 1.0.0
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
		if ( $this->get_exchange_rate() <= 0 ) {
			return new \WP_Error( 'invalid_rate', __( 'Exchange rate should be greater than 0.', 'wp-ever-accounting' ) );
		}

		// Duplicate check.
		$currency = eac_get_currency( $this->get_code() );
		if ( $currency && $currency->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate_currency', __( 'Currency already exists.', 'wp-ever-accounting' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_date_created() ) ) {
			$this->set_date_created( current_time( 'mysql' ) );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_date_updated( current_time( 'mysql' ) );
		}

		return parent::save();
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
	 * Get id.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function get_id() {
		return (int) $this->get_prop( 'id' );
	}

	/**
	 * Set id.
	 *
	 * @param int $id
	 *
	 * @since 1.0.0
	 */
	public function set_id( $id ) {
		$this->set_prop( 'id', absint( $id ) );
	}

	/**
	 * Get currency name.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set currency name.
	 *
	 * @param string $name Currency name.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', sanitize_text_field( $name ) );
	}

	/**
	 * Get currency code.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
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
	 * Get currency symbol.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
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
	 * Get decimal sep.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
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
	 * Get thousand sep.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
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
	 * Get number of decimal points.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
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
	 * @return string
	 * @since 1.0.2
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
	 * Get currency rate.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_exchange_rate( $context = 'edit' ) {
		return $this->get_prop( 'exchange_rate', $context );
	}

	/**
	 * Set the rate.
	 *
	 * @param string $value Currency rate.
	 *
	 * @since 1.0.2
	 */
	public function set_exchange_rate( $value ) {
		$this->set_prop( 'exchange_rate', eac_format_decimal( $value, 8, true ) );
	}

	/**
	 * Get if the currency is auto updated.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return int
	 * @since 1.0.2
	 */
	public function get_auto_update( $context = 'edit' ) {
		return $this->get_prop( 'auto_update', $context );
	}

	/**
	 * Set if the currency is auto updated.
	 *
	 * @param int $auto_update Currency auto update.
	 *
	 * @since 1.0.2
	 */
	public function set_auto_update( $auto_update ) {
		$this->set_prop( 'auto_update', $this->string_to_int( $auto_update ) );
	}

	/**
	 * Get status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
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
	 * Get the date updated.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_date_updated( $context = 'edit' ) {
		return $this->get_prop( 'date_updated', $context );
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $date date updated.
	 */
	public function set_date_updated( $date ) {
		$this->set_date_prop( 'date_updated', $date );
	}

	/**
	 * Get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Set the date created.
	 *
	 * @param string $date date created.
	 */
	public function set_date_created( $date ) {
		$this->set_date_prop( 'date_created', $date );
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/
	/**
	 * Set cache.
	 *
	 * @param string|int $key Key.
	 * @param mixed $value Value.
	 */
	protected function set_cache( $key, $value ) {
		parent::set_cache( $key, $value );
		// if code and id are set, set them in the cache.
		if ( ! empty( $this->code ) && ! empty( $this->id ) ) {
			wp_cache_set( $this->code, $this->id, $this->cache_group );
		}
	}

	/**
	 * Is the category active?
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function is_active() {
		return 'active' === $this->get_status();
	}

	/**
	 * Is base currency.
	 *
	 * @return bool
	 */
	public function is_base_currency() {
		return $this->get_code() === eac_get_base_currency();
	}

	/**
	 * Get formatted name.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_formatted_name() {
		return sprintf( '%s (%s)', $this->get_name(), $this->get_code() );
	}
}
