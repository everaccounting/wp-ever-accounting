<?php
/**
 * Handle the Currency object.
 *
 * @package     EverAccounting
 * @class       Contact
 * @version     1.2.1
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Currency object.
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property string $name
 */
class Currency {
	/**
	 * Currency data container.
	 *
	 * @since 1.2.1
	 * @var \stdClass
	 */
	public $data;

	/**
	 * Currency id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;


	/**
	 * Currency constructor.
	 */
	public function __construct( $currency ) {
		if ( $currency instanceof self ) {
			$this->id = (int) $currency->id;
		} elseif ( is_numeric( $currency ) ) {
			$this->id = $currency;
		} elseif ( ! empty( $currency->id ) ) {
			$this->id = (int) $currency->id;
		} else {
			$this->id = 0;
		}

		if ( $this->id > 0 ) {
			$data = self::load( $this->id );
			if ( ! $data ) {
				$this->id = null;

				return;
			}
			$this->data = $data;
			$this->id   = (int) $data->id;
		}
	}

	/**
	 * Return only the main currency fields
	 *
	 * @param int $id The id of the currency
	 *
	 * @return object|false Raw currency object
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	public static function load( $id ) {
		global $wpdb;

		if ( ! absint( $id ) ) {
			return false;
		}

		$data = wp_cache_get( $id, 'ea_currencys' );
		if ( $data ) {
			return $data;
		}

		$data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}ea_currencys WHERE id = %d LIMIT 1",
				$id
			)
		);

		if ( ! $data ) {
			return false;
		}

		eaccounting_set_cache( 'currency', $data );

		return $data;
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Contact field to check if set.
	 *
	 * @return bool Whether the given Contact field is set.
	 * @since 1.2.1
	 *
	 */
	public function __isset( $key ) {
		if ( isset( $this->data->$key ) ) {
			return true;
		}

		return metadata_exists( 'currency', $this->id, $key );
	}

	/**
	 * Magic method for setting currency fields.
	 *
	 * This method does not update custom fields in the database. It only stores
	 * the value on the WP_User instance.
	 *
	 * @param string $key Contact key.
	 * @param mixed $value Contact value.
	 *
	 * @since 1.2.1
	 *
	 */
	public function __set( $key, $value ) {
		if ( is_callable( array( $this, 'set_' . $key ) ) ) {
			$this->$key( $value );
		} else if ( isset( $this->data->$key ) ) {
			$this->data->$key = $value;
		} else {
			$this->update_meta( $key, $value );
		}
	}

	/**
	 * Magic method for accessing custom fields.
	 *
	 * @param string $key User field to retrieve.
	 *
	 * @return mixed Value of the given Contact field (if set).
	 * @since 1.2.1
	 *
	 */
	public function __get( $key ) {

		if ( is_callable( array( $this, 'get_' . $key ) ) ) {
			$value = $this->$key();
		} else if ( isset( $this->data->$key ) ) {
			$value = $this->data->$key;
		} else {
			$value = $this->get_meta( $key, true );
		}

		return $value;
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key Contact key to unset.
	 *
	 * @since 1.2.1
	 *
	 */
	public function __unset( $key ) {
		if ( isset( $this->data->$key ) ) {
			unset( $this->data->$key );
		}
	}

	/**
	 * Get meta data.
	 *
	 * @param string $meta_key
	 * @param false $single
	 *
	 * @return array|false|mixed
	 * @since 1.2.1
	 */
	protected function get_meta( $meta_key = '', $single = true ) {
		return get_metadata( 'currency', $this->id, $meta_key, $single );
	}

	/**
	 * Update meta value.
	 *
	 * @param $meta_key
	 * @param $meta_value
	 * @param string $prev_value
	 *
	 * @return bool|int
	 * @since 1.2.1
	 */
	protected function update_meta( $meta_key, $meta_value, $prev_value = '' ) {
		return update_metadata( 'currency', $this->id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * Consults the users and currency_meta tables.
	 *
	 * @param string $key Property
	 *
	 * @return bool
	 * @since 1.2.1
	 *
	 */
	public function has_prop( $key ) {
		return $this->__isset( $key );
	}

	/**
	 * Determine whether the currency exists in the database.
	 *
	 * @return bool True if currency exists in the database, false if not.
	 * @since 1.2.1
	 *
	 */
	public function exists() {
		return ! empty( $this->id );
	}

	/**
	 * Return an array representation.
	 *
	 * @return array Array representation.
	 * @since 1.2.1
	 *
	 */
	public function to_array() {
		return get_object_vars( $this->data );
	}
}
