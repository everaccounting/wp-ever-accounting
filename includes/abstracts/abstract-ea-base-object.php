<?php
/**
 *
 * Handles generic data interaction which is implemented by the different classes.
 *
 * @package     EverAccounting
 * @subpackage  Abstracts
 * @class       Base_Object
 * @version     1.0.2
 *
 */

namespace EverAccounting\Abstracts;

use EAccounting\DateTime;
use EverAccounting\Exception;

defined( 'ABSPATH' ) || exit();

/**
 * Class Base_Object
 *
 * @since   1.0.2
 * @package EverAccounting\Abstracts
 */
abstract class Base_Object {

	/**
	 * ID for this object.
	 *
	 * @since 1.0.2
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $data = array();

	/**
	 * Core data changes for this object.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $changes = array();

	/**
	 * This is false until the object is read from the DB.
	 *
	 * @since 1.0.2
	 * @var bool
	 */
	protected $object_read = false;

	/**
	 * Set to _data on construct so we can track and reset data if needed.
	 *
	 * @since 1.0.2
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
	 * @since 1.0.2
	 * @var array
	 */
	protected $errors = array();

	/**
	 * This is the name of this object type.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $object_type = '';

	/***
	 * Object table name.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $table = '';

	/**
	 * EAccounting_Object constructor.
	 *
	 * @param int|array|object|null $data
	 *
	 * @since 1.0.2
	 *
	 */
	public function __construct( $data = 0 ) {
		$this->default_data = $this->data;
	}

	/**
	 * Only store the object ID to avoid serializing the data object instance.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function __sleep() {
		return array( 'id' );
	}

	/**
	 * Re-run the constructor with the object ID.
	 *
	 * If the object no longer exists, remove the ID.
	 *
	 * @since 1.0.2
	 */
	public function __wakeup() {
		try {
			$this->__construct( absint( $this->id ) );
		} catch ( \Exception $e ) {
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
	 * @since 1.0.2
	 * @return bool
	 */
	public function exists() {
		return ! empty( $this->get_id() );
	}

	/**
	 * Returns the unique ID for this object.
	 *
	 * @since  1.0.2
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Only base data of the object.
	 *
	 * @since  1.0.2
	 * @return array
	 */
	public function get_base_data() {
		$data = array_merge( array( 'id' => $this->get_id() ), $this->data );
		foreach ( $data as $prop => $value ) {
			if ( $value instanceof DateTime ) {
				$data[ $prop ] = $value->date( 'Y-m-d H:i:s' );
			}
		}

		return $data;
	}


	/**
	 * Return the object data.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_data() {
		return array_merge_recursive( $this->get_base_data(), $this->get_extra_data() );
	}

	/**
	 * Returns array of expected data keys for this object.
	 *
	 * @since   1.0.2
	 * @return array
	 */
	public function get_data_keys() {
		return array_keys( $this->data );
	}

	/**
	 * Returns all "extra" data for an object.
	 *
	 * @since  1.0.2
	 * @return array
	 */
	public function get_extra_data() {
		return $this->extra_data;
	}

	/**
	 * Returns all "extra" data keys for an object.
	 *
	 * @since  1.0.2
	 * @return array
	 */
	public function get_extra_data_keys() {
		return array_keys( $this->extra_data );
	}

	/**
	 * Set ID.
	 *
	 * @param int $id ID.
	 *
	 * @since 1.0.2
	 */
	public function set_id( $id ) {
		$this->id = absint( $id );
	}

	/**
	 * Set all props to default values.
	 *
	 * @since 1.0.2
	 */
	public function set_defaults() {
		$this->data       = array_intersect( $this->default_data, $this->data );
		$this->extra_data = array_intersect( $this->default_data, $this->extra_data );
		$this->changes    = array();
		$this->set_object_read( false );
	}


	/**
	 * Set object read property.
	 *
	 * @param boolean $read Should read?.
	 *
	 * @since 1.0.2
	 */
	public function set_object_read( $read = true ) {
		$this->object_read = (bool) $read;
	}

	/**
	 * Get object read property.
	 *
	 * @since  1.0.2
	 * @return boolean
	 */
	public function get_object_read() {
		return (bool) $this->object_read;
	}

	/**
	 * Set a collection of props in one go, collect any errors, and return the result.
	 * Only sets using public methods.
	 *
	 * @param array|object $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 * @param string       $context In what context to run this.
	 *
	 * @since  1.0.2
	 *
	 * @return bool|\WP_Error
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
				}

			} catch ( Exception $e ) {
				if ( ! $errors ) {
					$errors = new \WP_Error();
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
	 * @param mixed  $value Value of the prop.
	 *
	 * @since 1.0.2
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
		} else {
			$this->extra_data[ $prop ] = $value;
		}
	}

	/**
	 * Return data changes only.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_changes() {
		return $this->changes;
	}

	/**
	 * Merge changes with data and clear.
	 *
	 * @since 1.0.2
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
	 * @since  1.0.2
	 * @return mixed
	 */
	protected function get_prop( $prop, $context = 'raw' ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data ) ) {
			$value = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : $this->data[ $prop ];

			if ( 'view' === $context ) {
				$value = apply_filters( 'eaccounting_get_' . $this->object_type . '_' . $prop, $value, $this );
			}
		} elseif ( array_key_exists( $prop, $this->extra_data ) ) {
			$value = $this->extra_data[ $prop ];
			if ( 'view' === $context ) {
				$value = apply_filters( 'eaccounting_get_extra_' . $this->object_type . '_' . $prop, $value, $this );
			}
		}

		return $value;
	}

	/**
	 * Sets a date prop whilst handling formatting and datetime objects.
	 *
	 * @param string         $prop Name of prop to set.
	 * @param string|integer $value Value of the prop.
	 *
	 * @since 1.0.2
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
				$datetime = new DateTime( "@{$value}", new \DateTimeZone( 'UTC' ) );
			} else {
				// Strings are defined in local WP timezone. Convert to UTC.
				if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $value, $date_bits ) ) {
					$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : eaccounting_timezone_offset();
					$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
				} else {
					$timestamp = eaccounting_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', eaccounting_string_to_timestamp( $value ) ) ) );
				}
				$datetime = new DateTime( "@{$timestamp}", new \DateTimeZone( 'UTC' ) );
			}

			// Set local timezone or offset.
			if ( get_option( 'timezone_string' ) ) {
				$datetime->setTimezone( new \DateTimeZone( eaccounting_timezone_string() ) );
			} else {
				$datetime->set_utc_offset( eaccounting_timezone_offset() );
			}

			$this->set_prop( $prop, $datetime );
		} catch ( Exception $e ) {
		} // @codingStandardsIgnoreLine.
	}

	/**
	 * Populate data based on the object or array passed.
	 *
	 * @param array|Object $data Object data.
	 *
	 * @since 1.0.2
	 * @throws Exception
	 */
	public function populate( $data ) {
		$errors = $this->set_props( $data );

		if ( is_wp_error( $errors ) ) {
			$class = get_called_class();
			eaccounting_logger()->error(
				sprintf( __( 'Failed populating object because %s', 'wp-ever-accounting' ), $errors->get_error_message()
				),
				array( 'id' => $this->get_id(), 'context' => $class . __METHOD__ )
			);
			$this->error( $errors->get_error_code(), $errors->get_error_message() );
		}

		$this->set_object_read( true );
	}

	/**
	 * Method to read a record. Creates a new EAccounting_Object based object.
	 *
	 * @param int $id ID of the object to read.
	 *
	 * @since 1.0.2
	 * @throws Exception
	 */
	public function read() {
		$this->set_defaults();
		global $wpdb;

		// Get from cache if available.
		$item = 0 < $this->get_id() ? wp_cache_get( $this->object_type . '-item-' . $this->get_id(), $this->object_type ) : false;

		if ( false === $item ) {
			$item = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}{$this->table} WHERE id = %d;", $this->get_id() )
			);

			if ( $item && 0 < $item->id ) {
				wp_cache_set( $this->object_type . '-item-' . $item->id, $item, $this->object_type );
			}
		}

		if ( ! $item || ! $item->id ) {
			throw new Exception( 'invalid_id', __( 'Invalid item.', 'wp-ever-accounting' ) );
		}
		$this->populate( $item );
	}

	/**
	 * Method to create a new record of an EverAccounting object.
	 *
	 * @since 1.0.2
	 * @throws Exception
	 */
	public function create() {
		global $wpdb;

		do_action( 'eaccounting_pre_insert_' . $this->object_type, $this->get_id(), $this );

		$data = wp_unslash( apply_filters( 'eaccounting_new_' . $this->object_type . '_data', $this->get_base_data() ) );
		if ( false === $wpdb->insert( $wpdb->prefix . $this->table, $data ) ) {
			throw new Exception( 'db_error', $wpdb->last_error );
		}

		do_action( 'eaccounting_insert_' . $this->object_type, $this->get_id(), $this );

		$this->set_id( $wpdb->insert_id );
		$this->apply_changes();
		$this->set_object_read( true );
	}

	/**
	 * Updates a record in the database.
	 *
	 * @since 1.0.2
	 * @throws Exception
	 */
	public function update() {
		global $wpdb;
		$changes = $this->get_changes();
		foreach ( $changes as $prop => $value ) {
			if ( $value instanceof DateTime ) {
				$changes[ $prop ] = $value->date( 'Y-m-d H:i:s' );
			}
		}

//		$changes = array_intersect($changes, $this->get_base_data() );
//		var_dump($changes);

//		if ( ! empty( $changes ) ) {
//			do_action( 'eaccounting_pre_update_' . $this->object_type, $this->get_id(), $changes );
//
//			try {
//				$wpdb->update( $wpdb->prefix . $this->table, $changes, array( 'id' => $this->get_id() ) );
//			} catch ( Exception $e ) {
//				throw new Exception( 'db_error', __( 'Could not update resource.', 'wp-ever-accounting' ) );
//			}
//
//			do_action( 'eaccounting_update_' . $this->object_type, $this->get_id(), $changes, $this->data );
//
//			$this->apply_changes();
//			$this->set_object_read( true );
//			wp_cache_delete( $this->object_type . '-item-' . $this->get_id(), $this->object_type );
//		}
	}

	/**
	 * Deletes a record from the database.
	 *
	 * @since 1.0.2
	 * @return bool result
	 */
	public function delete() {
		if ( $this->get_id() && $this->table ) {
			global $wpdb;
			do_action( 'eaccounting_pre_delete_' . $this->object_type, $this->get_id(), $this->get_data(), $this );
			$wpdb->delete( $wpdb->prefix . $this->table, array( 'id' => $this->get_id() ) );
			do_action( 'eaccounting_delete_' . $this->object_type, $this->get_id(), $this->get_data(), $this );
			$this->set_id( 0 );
			wp_cache_delete( $this->object_type . '-item-' . $this->get_id(), $this->object_type );

			return true;
		}

		return false;
	}

	/**
	 * Conditionally save item if id present then update
	 * otherwise create.
	 *
	 * @since 1.0.2
	 * @throws Exception
	 * @return int $id of the object.
	 */
	public function save() {
		if ( empty( $this->table ) ) {
			return $this->get_id();
		}

		/**
		 * Trigger action before saving to the DB. Allows you to adjust object props before save.
		 *
		 * @param Base_Object $this The object being saved.
		 */
		do_action( 'eaccounting_before_' . $this->object_type . '_object_save', $this );

		if ( $this->get_id() ) {
			$this->update();
		} else {
			$this->create();
		}

		/**
		 * Trigger action after saving to the DB.
		 *
		 * @param Base_Object $this The object being saved.
		 */
		do_action( 'eaccounting_before_' . $this->object_type . '_object_save', $this );


		return $this->get_id();
	}

	/**
	 * When invalid data is found, throw an exception unless reading from the DB.
	 *
	 * @param string $code Error code.
	 * @param string $message Error message.
	 * @param int    $http_status_code HTTP status code.
	 * @param array  $data Extra error data.
	 *
	 * @since 1.0.2
	 * @throws Exception Data Exception.
	 */
	protected function error( $code, $message, $http_status_code = 400, $data = array() ) {
		throw new Exception( $code, $message, $http_status_code, $data );
	}

	/**
	 * Get object created date.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return DateTime
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * get object status
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 * @return bool
	 */
	public function get_enabled( $context = 'edit' ) {
		return $this->get_prop( 'enabled', $context );
	}

	/**
	 * get object status
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 * @return bool
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
	public function set_company_id( $company_id = 1 ) {
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
	public function set_creator_id( $creator_id = null ) {
		if ( $creator_id === null ) {
			$creator_id = eaccounting_get_current_user_id();
		}
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
	public function set_date_created( $date = null ) {
		if ( $date === null ) {
			$date = time();
		}
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Return object belonging company id.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_company_id( $context = 'edit' ) {
		return absint( $this->get_prop( 'company_id', $context ) );
	}

	/**
	 * Return object created by.
	 *
	 * @param string $context
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_creator_id( $context = 'edit' ) {
		return $this->get_prop( 'creator_id', $context );
	}
}
