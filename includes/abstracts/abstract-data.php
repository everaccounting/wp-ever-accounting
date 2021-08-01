<?php

namespace EverAccounting\Abstracts;

class Data {

	/**
	 *
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array();


	/**
	 * Magic method for setting account fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Account key.
	 * @param mixed $value Account value.
	 *
	 * @since 1.2.1
	 */
	public function __set( $key, $value ) {
		if ( method_exists( $this, 'set_' . $key ) ) {
			$this->{'set_' . $key}( $value );
		} else if ( property_exists( $this, $key ) && is_callable( array( $this, $key ) ) ) {
			$this->$key = $value;
		} else if ( array_key_exists( $key, $this->data ) ) {
			$this->data[ $key ] = $value;
		}
	}

	/**
	 * Magic method for accessing custom fields.
	 *
	 * @param string $key Account field to retrieve.
	 *
	 * @return mixed Value of the given Account field (if set).
	 * @since 1.2.1
	 */
	public function __get( $key ) {
		$value = '';
		if ( method_exists( $this, 'get_' . $key ) ) {
			$value = $this->{'get_' . $key};
		} else if ( property_exists( $this, $key ) && is_callable( array( $this, $key ) ) ) {
			$value = $this->$key;
		} else if ( array_key_exists( $key, $this->data ) ) {
			$value = $this->data[ $key ];
		}

		return $value;
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Account field to check if set.
	 *
	 * @return bool Whether the given Account field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		if ( property_exists( $this, $key ) && is_callable( array( $this, $key ) ) && isset( $this->$key ) ) {
			return true;
		}

		if ( array_key_exists( $key, $this->data ) && isset( $this->data[ $key ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key Account key to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		if ( property_exists( $this, $key ) && is_callable( array( $this, $key ) ) && isset( $this->$key ) ) {
			unset( $this->$key );
		} else if ( array_key_exists( $key, $this->data ) && isset( $this->data[ $key ] ) ) {
			unset( $this->data[ $key ] );
		}
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * @param string $key Property
	 *
	 * @return bool
	 * @since 1.2.1
	 */
	public function has_prop( $key ) {
		return $this->__isset( $key );
	}

	/**
	 * Determine whether the item exists in the database.
	 *
	 * @return bool True if item exists in the database, false if not.
	 * @since 1.2.1
	 */
	public function exists() {
		return ! empty( $this->id );
	}

	/**
	 * Return an array representation.
	 *
	 * @return array Array representation.
	 * @since 1.2.1
	 */
	public function to_array() {
		return array_merge(
			array(
				'id' => $this->id,

			),
			$this->data
		);
	}
}
