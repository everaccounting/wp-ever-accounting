<?php

namespace EverAccounting\Abstracts;

use EverAccounting\DateTime;
use EverAccounting\Interfaces\Arrayable;
use EverAccounting\Interfaces\JSONable;
use EverAccounting\Interfaces\Stringable;

abstract class ResourceModel implements Arrayable, JSONable, Stringable {
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
	 * Stores additional meta data.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $meta_data = array();

	/**
	 * Holds all the errors of the item.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Holds the caller class.
	 *
	 * @since 1.1.0
	 * @var null
	 */
	protected $caller = null;

	/**
	 * Repository class.
	 *
	 * @since 1.1.0
	 * @var ResourceRepository
	 */
	protected $repository;

	/**
	 * Data constructor.
	 *
	 * @param int|array|object|null $data
	 * @param object    $repository
	 */
	public function __construct( $data = 0, $repository = null ) {
		if ( null !== $repository && class_exists( $repository ) ) {
			$this->data         = $repository->get_defaults();
			$this->default_data = $this->repository->get_defaults();
		}

		$this->caller = get_called_class();

		if ( $data instanceof $this->caller ) {
			$this->set_id( absint( $data->get_id() ) );
			$this->set_props( $data->to_array() );
			$this->set_object_read( true );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( is_array( $data ) || is_object( $data ) ) {
			$data = wp_parse_args( (array) $data, array( 'id' => null ) );
			$this->set_id( $data['id'] );
			if ( count( $data ) - 1 !== count( $this->repository->get_columns() ) ) {
				$this->read();
			}
			$this->set_props( $data );
		} else {
			$this->set_object_read( false );
		}
		if ( $this->get_id() > 0 && ! $this->object_read ) {
			$this->read();
		}
	}

	/**
	 * Read the entry from database.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	protected function read() {
		$this->set_defaults();
		$item = $this->repository->get( $this->get_id() );
		if ( $item ) {
			$this->set_props( $item );
			$this->set_object_read( true );
		} else {
			$this->set_id( 0 );
		}
	}

	/**
	 * Save entry.
	 *
	 * @since 1.1.0
	 * @return \EverAccounting\Abstracts\ResourceModel|\WP_Error
	 */
	public function save() {
		$changes = $this->get_changes();
		if ( ! $this->exists() ) {
			$item_id = $this->repository->insert( $this->to_array() );
			if ( is_wp_error( $item_id ) ) {
				return $item_id;
			}
			$this->set_id( $item_id );
			$this->apply_changes();
			$this->set_object_read( true );
		} elseif ( ! empty( $changes ) ) {
			$updated = $this->repository->update( $this->get_id(), $changes );
			if ( is_wp_error( $updated ) ) {
				return $updated;
			}
			$this->apply_changes();
			$this->set_object_read( true );
		}

		return $this;
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
	 * Returns the unique ID for this object.
	 *
	 * @since  1.0.2
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set ID.
	 *
	 * @since 1.0.2
	 *
	 * @param int $id ID.
	 *
	 */
	public function set_id( $id ) {
		$this->id = absint( $id );
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
	 * Returns all data for this object.
	 *
	 * @since  2.6.0
	 * @return array
	 */
	public function get_data() {
		return array_merge( array( 'id' => $this->get_id() ), $this->data, array( 'meta_data' => $this->get_meta_data() ) );
	}

	/**
	 * Get All Meta Data.
	 *
	 * @since 1.0.0
	 * @return array of objects.
	 */
	public function get_meta_data() {
		return array_values( array_filter( $this->meta_data, array( $this, 'filter_null_meta' ) ) );
	}

	/**
	 * Filter null meta values from array.
	 *
	 * @since  1.0.0
	 *
	 * @param mixed $meta Meta value to check.
	 *
	 * @return bool
	 */
	protected function filter_null_meta( $meta ) {
		return ! is_null( $meta->value );
	}

	/**
	 * Set all props to default values.
	 *
	 * @since 1.0.2
	 */
	public function set_defaults() {
		$this->data    = $this->default_data;
		$this->changes = array();
		$this->set_object_read( false );
	}

	/**
	 * Set object read property.
	 *
	 * @since 1.0.2
	 *
	 * @param boolean $read Should read?.
	 *
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
	 * Set a collection of props in one go, collect any errors, and return the result.
	 * Only sets using public methods.
	 *
	 * @since  1.0.2
	 *
	 * @param array|object $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 *
	 * @return void
	 */
	public function set_props( $props ) {
		if ( is_object( $props ) ) {
			$props = get_object_vars( $props );
		}

		foreach ( $props as $prop => $value ) {
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
		}
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * This stores changes in a special array so we can track what needs saving
	 * the the DB later.
	 *
	 * @since 1.0.2
	 *
	 * @param mixed  $value Value of the prop.
	 *
	 * @param string $prop  Name of prop to set.
	 */
	protected function set_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data ) && true === $this->object_read && ( $value !== $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) ) {
			if ( $value !== $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) {
				$this->changes[ $prop ] = $value;
			}
		} else {
			$this->data[ $prop ] = eaccounting_clean( $value );
		}
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * Gets the value from either current pending changes, or the data itself.
	 * Context controls what happens to the value before it's returned.
	 *
	 * @since  1.0.2
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @param string $prop    Name of prop to get.
	 *
	 * @return mixed
	 */
	protected function get_prop( $prop, $context = 'raw' ) {
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
	 * @since 1.0.2
	 *
	 * @param string|integer $value Value of the prop.
	 *
	 * @param string         $prop  Name of prop to set.
	 */
	protected function set_date_prop( $prop, $value ) {
		if ( empty( $value ) ) {
			$this->set_prop( $prop, null );

			return;
		}

		if ( is_a( $value, 'EverAccounting\\DateTime' ) ) {
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
	}


	/**
	 * Get object created date.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return DateTime
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * get object status
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return bool
	 */
	public function get_enabled( $context = 'edit' ) {
		return $this->get_prop( 'enabled', $context );
	}


	/**
	 * get object status
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return eaccounting_string_to_bool( $this->get_prop( 'enabled', 'edit' ) );
	}

	/**
	 * Return object created by.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_creator_id( $context = 'edit' ) {
		return $this->get_prop( 'creator_id', $context );
	}

	/**
	 * Set object status.
	 *
	 * @since 1.0.2
	 *
	 * @param int $enabled Company id
	 *
	 */
	public function set_enabled( $enabled ) {
		$this->set_prop( 'enabled', absint( $enabled ) );
	}

	/**
	 * Set object created date.
	 *
	 * @since 1.0.2
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 *
	 */
	public function set_date_created( $date = null ) {
		if ( null === $date ) {
			$date = time();
		}
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Set object creator id.
	 *
	 * @since 1.0.2
	 *
	 * @param int $creator_id Creator id
	 *
	 */
	public function set_creator_id( $creator_id = null ) {
		if ( null === $creator_id ) {
			$creator_id = eaccounting_get_current_user_id();
		}
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}

	/**
	 * Returns model as array.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function __toArray() {
		$output = array();
		foreach ( $this->get_data() as $prop => $value ) {
			$output[ $prop ] = $this->__get_cleaned( $value );
		}

		return $output;
	}

	/**
	 * Returns model as array.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function to_array() {
		return $this->__toArray();
	}

	/**
	 * Returns model as json string.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function __toString() {
		return json_encode( $this->__toArray() );
	}

	/**
	 * Returns object as JSON string.
	 *
	 * @since 1.1.0
	 *
	 * @param int $options JSON encoding options. See @link.
	 * @param int $depth   JSON encoding depth. See @link.
	 *
	 * @return string
	 * @link  http://php.net/manual/en/function.json-encode.php
	 *
	 */
	public function __toJSON( $options = 0, $depth = 512 ) {
		return json_encode( $this->__toArray(), $options, $depth );
	}

	/**
	 * Returns object as JSON string.
	 *
	 * @since 1.1.0
	 *
	 * @param int $options JSON encoding options. See @link.
	 * @param int $depth   JSON encoding depth. See @link.
	 *
	 * @return string
	 * @link  http://php.net/manual/en/function.json-encode.php
	 *
	 */
	public function to_JSON( $options = 0, $depth = 512 ) {
		return $this->__toJSON( $options, $depth );
	}

	/**
	 * Returns cleaned value for casting.
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $value Value to clean.
	 *
	 * @return mixed
	 */
	private function __get_cleaned( $value ) {
		switch ( gettype( $value ) ) {
			case 'object':
				return method_exists( $value, '__toArray' )
					? $value->__toArray()
					: ( method_exists( $value, 'to_Array' )
						? $value->to_Array()
						: (array) $value
					);
			case 'array':
				$output = array();
				foreach ( $value as $key => $data ) {
					if ( null !== $data ) {
						$output[ $key ] = $this->__get_cleaned( $data );
					}
				}

				return $output;
		}

		return $value;
	}

	/**
	 * Generates a hook name based on the caller class.
	 *
	 * @since 1.1.0
	 *
	 * @param null $suffix
	 *
	 * @return string
	 */
	public function get_hook_name( $suffix = null ) {
		$array = explode( '\\', $this->caller );
		$hook  = sanitize_key( array_pop( $array ) );
		if ( $suffix ) {
			$hook .= '_' . sanitize_key( $suffix );
		}

		return 'eaccounting_' . $hook;
	}


}
