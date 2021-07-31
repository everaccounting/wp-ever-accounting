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
	 * Currency id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Currency friendly name.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $name = '';

	/**
	 * Currency ISO code.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $code = '';

	/**
	 * Currency Rate
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $rate = 1;

	/**
	 * Currency precision.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $precision = 2;

	/**
	 * Currency Decimal separator.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $decimal_separator = '.';

	/**
	 * Thousand separator.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $thousand_separator = '.';

	/**
	 * Item status
	 *
	 * @since 1.2.1
	 * @var bool
	 */
	public $enabled = true;

	/**
	 * Currency created date.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $date_created = '0000-00-00 00:00:00';

	/**
	 * Return only the main currency fields
	 *
	 * @param int|string $value
	 * @param string $field
	 *
	 * @return object|false Raw currency object
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_data_by( $value, $field = 'code' ) {
		global $wpdb;
		if ( 'id' === $field ) {
			$value = (int) $value;
		} else {
			$value = trim( $value );
		}
		if ( ! $value ) {
			return false;
		}
		switch ( $field ) {
			case 'code':
				$value    = eaccounting_sanitize_currency_code( $value );
				$db_field = 'code';
				break;
			case 'id':
			default:
				$db_field = 'id';
				break;
		}

		$_item = wp_cache_get( "{$db_field}_{$value}", 'ea_items' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_currencies WHERE $db_field = %s LIMIT 1", $value ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_currency( $_item, 'raw' );
			wp_cache_add( "id_{$_item->id}", $_item, 'ea_items' );
			wp_cache_add( "code_{$_item->code}", $_item, 'ea_items' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_currency( $_item, 'raw' );
		}

		return new Currency( $_item );
	}

	/**
	 * Item constructor.
	 *
	 * @param $item
	 *
	 * @since 1.2.1
	 */
	public function __construct( $item ) {
		foreach ( get_object_vars( $item ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Item field to check if set.
	 *
	 * @return bool Whether the given Item field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		if ( isset( $this->$key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic method for setting Item fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Item key.
	 * @param mixed $value Item value.
	 *
	 * @since 1.2.1
	 */
	public function __set( $key, $value ) {
		if ( is_callable( array( $this, 'set_' . $key ) ) ) {
			$this->$key( $value );
		} else {
			$this->$key = $value;
		}
	}

	/**
	 * Magic method for accessing custom fields.
	 *
	 * @param string $key item field to retrieve.
	 *
	 * @return mixed Value of the given item field (if set).
	 * @since 1.2.1
	 */
	public function __get( $key ) {

		if ( is_callable( array( $this, 'get_' . $key ) ) ) {
			$value = $this->$key();
		} else {
			$value = $this->$key;
		}

		return $value;
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key item key to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		if ( isset( $this->$key ) ) {
			unset( $this->$key );
		}
	}

	/**
	 * Filter category object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Category|Object
	 * @since 1.2.1
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::load( $this->id );
		}

		if ( 'raw' === $filter ) {
			return self::get_instance( $this->id );
		}

		return eaccounting_sanitize_currency( $this, $filter );
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
		return get_object_vars( $this );
	}
}
