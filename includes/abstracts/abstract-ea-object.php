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
	 * This is false until the object is read from the DB.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $object_read = false;

	/**
	 * Set to _data on construct so we can track and reset data if needed.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $default_data = array();

	/**
	 * Extra data for this object. Name value pairs (name + default value).
	 * Used as a standard way for sub classes to add
	 * additional information to an inherited class.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $extra_data = array();

	/**
	 * Holds all the errors of the item.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Key to save cache.
	 *
	 * @var string
	 * @since 1.0.2
	 */
	protected $cache_key = '';

	/**
	 * A group must be set to to enable caching.
	 *
	 * @var string
	 * @since 1.0.2
	 */
	protected $cache_group = '';

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 * @since 1.0.2
	 */
	public $object_type = '';

	/**
	 * EAccounting_Object constructor.
	 *
	 * @param int|array|object|null $data
	 */
	public function __construct( $data = 0 ) {
		$this->data         = array_merge( $this->data, $this->extra_data );
		$this->default_data = $this->data;
	}

	/**
	 * Only store the object ID to avoid serializing the data object instance.
	 *
	 * @return array
	 */
	public function __sleep() {
		return array( 'id' );
	}

	/**
	 * Re-run the constructor with the object ID.
	 *
	 * If the object no longer exists, remove the ID.
	 */
	public function __wakeup() {
		try {
			$this->__construct( absint( $this->id ) );
		} catch ( Exception $e ) {
			$this->set_id( 0 );
			$this->set_object_read( true );
		}
	}


	/**
	 * When the object is cloned, make sure meta is duplicated correctly.
	 *
	 * @since 1.0.2
	 */
	public function __clone() {

	}

	/**
	 * Returns whether or not the item exists.
	 *
	 * @return bool
	 */
	public function exists() {
		return ! empty( $this->get_id() );
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
	 * Returns all "extra" data keys for an object.
	 *
	 * @return array
	 * @since  1.0.2
	 */
	public function get_extra_data_keys() {
		return array_keys( $this->extra_data );
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
		$this->set_object_read( false );
	}


	/**
	 * Set object read property.
	 *
	 * @param boolean $read Should read?.
	 *
	 * @since 1.0.0
	 */
	public function set_object_read( $read = true ) {
		$this->object_read = (bool) $read;
	}

	/**
	 * Get object read property.
	 *
	 * @return boolean
	 * @since  1.0.0
	 */
	public function get_object_read() {
		return (bool) $this->object_read;
	}

	/**
	 * Set a collection of props in one go, collect any errors, and return the result.
	 * Only sets using public methods.
	 *
	 * @param array|object $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 * @param string $context In what context to run this.
	 *
	 * @return bool|WP_Error
	 * @since  1.0.0
	 *
	 */
	public function set_props( $props, $context = 'set' ) {
		$errors = false;

		if ( is_object( $props ) ) {
			$props = get_object_vars( $props );
		}

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
				} else {
					$this->set_prop( $prop, $value );
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
			if ( true === $this->object_read ) {
				if ( $value !== $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) {
					$this->changes[ $prop ] = $value;
				}
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
					$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : eaccounting_timezone_offset();
					$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
				} else {
					$timestamp = eaccounting_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', eaccounting_string_to_timestamp( $value ) ) ) );
				}
				$datetime = new EAccounting_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );
			}

			// Set local timezone or offset.
			if ( get_option( 'timezone_string' ) ) {
				$datetime->setTimezone( new DateTimeZone( eaccounting_timezone_string() ) );
			} else {
				$datetime->set_utc_offset( eaccounting_timezone_offset() );
			}

			$this->set_prop( $prop, $datetime );
		} catch ( Exception $e ) {
		} // @codingStandardsIgnoreLine.
	}


	/**
	 * Get object created date.
	 *
	 * @param string $context
	 *
	 * @return EAccounting_DateTime
	 * @since 1.0.2
	 *
	 */
	public function get_date_created( $context = 'view' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * get object status
	 *
	 * @param string $context
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function get_enabled( $context = 'view' ) {
		return $this->get_prop( 'enabled', $context );
	}

	/**
	 * get object status
	 *
	 * @param string $context
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function is_enabled() {
		return eaccounting_string_to_bool( $this->get_prop( 'enabled', 'edit' ) );
	}

	/**
	 * Set object status.
	 *
	 * @param int $enabled Company id
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_enabled( $enabled ) {
		$this->set_prop( 'enabled', absint( $enabled ) );
	}

	/**
	 * Set object belonging company id.
	 *
	 * @param int $company_id Company id
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_company_id( $company_id ) {
		$this->set_prop( 'company_id', absint( $company_id ) );
	}

	/**
	 * Set object creator id.
	 *
	 * @param int $creator_id Creator id
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_creator_id( $creator_id ) {
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}

	/**
	 * Set object created date.
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_date_created( $date ) {
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Return object belonging company id.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_company_id( $context = 'view' ) {
		return absint( $this->get_prop( 'company_id', $context ) );
	}

	/**
	 * Return object created by.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_creator_id( $context = 'view' ) {
		return $this->get_prop( 'creator_id', $context );
	}

	/**
	 * Populate data based on the object or array passed.
	 *
	 * @param array|Object $data Object data.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	public function populate( $data ) {
		$errors = $this->set_props( $data );

		if ( is_wp_error( $errors ) ) {
			$class = get_called_class();
			eaccounting_logger()->error(
				sprintf( __( 'Failed populating account because %s', 'wp-ever-accounting' ), $errors->get_error_message()
				),
				array( 'id' => $this->get_id(), 'context' => $class . __METHOD__ )
			);
			$this->error( $errors->get_error_code(), $errors->get_error_message() );
		}

		$this->set_object_read( true );
	}

	/**
	 * Method to validate before inserting and updating EverAccounting object.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	abstract public function validate_props();

	/**
	 * Method to read a record. Creates a new EAccounting_Object based object.
	 *
	 * @param int $id ID of the object to read.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	abstract public function read( $id );

	/**
	 * Method to create a new record of an EverAccounting object.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	abstract public function create();

	/**
	 * Updates a record in the database.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	abstract public function update();

	/**
	 * Deletes a record from the database.
	 *
	 * @return bool result
	 * @since 1.0.2
	 */
	abstract public function delete();

	/**
	 * Conditionally save item if id present then update
	 * otherwise create.
	 *
	 * @return mixed
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function save() {
		$this->validate_props();

		if ( $this->get_id() ) {
			return $this->update();
		}

		return $this->create();
	}

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
