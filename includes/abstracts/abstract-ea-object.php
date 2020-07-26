<?php
/**
 *
 * Handles generic data interaction which is implemented by
 * the different classes.
 *
 * @class       EAccounting_Object
 * @version     1.0.0
 * @package     EverAccounting/Classes
 */

defined( 'ABSPATH' ) || exit();

abstract class EAccounting_Object {

	/**
	 * ID for this object.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array();

	/**
	 * Core data changes for this object.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $changes = array();

	/**
	 * Set to _data on construct so we can track and reset data if needed.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $default_data = array();

	/**
	 * Holds all the errors of the item.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $errors = array();

	/**
	 * EAccounting_Object constructor.
	 *
	 * @param mixed $object
	 */
	public function __construct( $object ) {
		$this->default_data = $this->data;
		if ( is_numeric( $object ) && $object > 0 ) {
			$this->set_id( $object );
		} elseif ( $object instanceof self ) {
			$this->set_id( $object->get_id() );
		} elseif ( ! empty( $object->id ) ) {
			$this->set_id( $object->id );
		} else {
			$this->set_id( null );
		}
	}


	/**
	 * Returns the unique ID for this object.
	 *
	 * @return int
	 * @since  1.0.0
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Returns whether or not the item exists.
	 *
	 * @return bool
	 */
	public function exists() {
		return false !== $this->get_id();
	}

	/**
	 * Returns all data for this object.
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function get_data() {
		return array_merge( array( 'id' => $this->get_id() ), $this->data );
	}

	/**
	 * Returns array of expected data keys for this object.
	 *
	 * @return array
	 * @since   1.0.0
	 */
	public function get_data_keys() {
		return array_keys( $this->data );
	}

	/**
	 * Set ID.
	 *
	 * @param int $id ID.
	 *
	 * @since 1.0.0
	 */
	public function set_id( $id ) {
		$this->id = absint( $id );
	}

	/**
	 * Set all props to default values.
	 *
	 * @since 1.0.0
	 */
	public function set_defaults() {
		$this->data    = $this->default_data;
		$this->changes = array();
	}

	/**
	 * Set a collection of props in one go, collect any errors, and return the result.
	 * Only sets using public methods.
	 *
	 * @param array $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 * @param string $context In what context to run this.
	 *
	 * @return bool|WP_Error
	 * @since  1.0.0
	 *
	 */
	public function set_props( $props, $context = 'set' ) {
		$errors = false;

		foreach ( $props as $prop => $value ) {
			try {
				/**
				 * Checks if the prop being set is allowed, and the value is not null.
				 */
				if ( is_null( $value ) || in_array( $prop, array( 'prop', 'date_prop' ), true ) ) {
					continue;
				}
				$setter = "set_$prop";

				if ( is_callable( array( $this, $setter ) ) ) {
					$this->{$setter}( $value );
				}
			} catch ( EAccounting_Exception $e ) {
				if ( ! $errors ) {
					$errors = new WP_Error();
				}
				$errors->add( $e->getErrorCode(), $e->getMessage() );
			}
		}

		return $errors && count( $errors->get_error_codes() ) ? $errors : true;
	}


	/**
	 * Sets a prop for a setter method.
	 *
	 * This stores changes in a special array so we can track what needs saving
	 * the the DB later.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed $value Value of the prop.
	 *
	 * @since 1.0.0
	 */
	protected function set_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data ) ) {
			if ( $value !== $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) {
				$this->changes[ $prop ] = $value;
			} else {
				$this->data[ $prop ] = $value;
			}
		}
	}

	/**
	 * Return data changes only.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_changes() {
		return $this->changes;
	}

	/**
	 * Merge changes with data and clear.
	 *
	 * @since 1.0.0
	 */
	public function apply_changes() {
		$this->data    = array_replace_recursive( $this->data, $this->changes );
		$this->changes = array();
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * Gets the value from either current pending changes, or the data itself.
	 * Context controls what happens to the value before it's returned.
	 *
	 * @param string $prop Name of prop to get.
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return mixed
	 * @since  1.0.0
	 */
	protected function get_prop( $prop, $context = 'view' ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data ) ) {
			$value = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : $this->data[ $prop ];

			if ( 'view' === $context ) {
				$value = apply_filters( 'eaccounting_get_' . $prop, $value, $this );
			}
		}

		return $value;
	}

	/**
	 * Sets a date prop whilst handling formatting and datetime objects.
	 *
	 * @param string $prop Name of prop to set.
	 * @param string|integer $value Value of the prop.
	 *
	 * @since 1.0.0
	 */
	protected function set_date_prop( $prop, $value ) {
		try {
			if ( empty( $value ) ) {
				$this->set_prop( $prop, null );

				return;
			}

			if ( is_a( $value, 'EAccounting_DateTime' ) ) {
				$datetime = $value;
			} elseif ( is_numeric( $value ) ) {
				// Timestamps are handled as UTC timestamps in all cases.
				$datetime = new EAccounting_DateTime( "@{$value}", new DateTimeZone( 'UTC' ) );
			} else {
				// Strings are defined in local WP timezone. Convert to UTC.
				if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $value, $date_bits ) ) {
					$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : wc_timezone_offset();
					$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
				} else {
					$timestamp = wc_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', wc_string_to_timestamp( $value ) ) ) );
				}
				$datetime = new EAccounting_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );
			}

			// Set local timezone or offset.
			if ( get_option( 'timezone_string' ) ) {
				$datetime->setTimezone( new DateTimeZone( wc_timezone_string() ) );
			} else {
				$datetime->set_utc_offset( wc_timezone_offset() );
			}

			$this->set_prop( $prop, $datetime );
		} catch ( Exception $e ) {
		} // @codingStandardsIgnoreLine.
	}

	/**
	 * Handle savings the item.
	 * @return mixed
	 * @since 1.0.0
	 */
	public function save() {
		$this->validate_props();

		if ( $this->get_id() ) {
			return $this->update();
		} else {
			return $this->create();
		}
	}

	/**
	 * Reads an object.
	 *
	 * @param int $id ID of the object.
	 *
	 * @throws Exception Throw exception if invalid id is passed.
	 * @since 1.0.0
	 */
	protected abstract function load( $id );

	/**
	 * Create an object.
	 * @return void
	 * @since 1.0.0
	 */
	protected abstract function validate_props();

	/**
	 * Create an object.
	 *
	 * @since 1.0.0
	 */
	public abstract function create();

	/**
	 * Update an object.
	 *
	 * @return
	 * @since 1.0.0
	 */
	public abstract function update();

	/**
	 * Delete an object.
	 *
	 * @param array $args Array of args to pass to the delete method.
	 *
	 * @since 1.0.0
	 */
	public abstract function delete( $args = array() );


	/**
	 * When invalid data is found, throw an exception unless reading from the DB.
	 *
	 * @param string $code Error code.
	 * @param string $message Error message.
	 * @param int $http_status_code HTTP status code.
	 * @param array $data Extra error data.
	 *
	 * @throws EAccounting_Exception Data Exception.
	 * @since 1.0.0
	 */
	protected function error( $code, $message, $http_status_code = 400, $data = array() ) {
		throw new EAccounting_Exception( $code, $message, $http_status_code, $data );
	}
}
