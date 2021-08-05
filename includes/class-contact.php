<?php
/**
 * Handle the Contact object.
 *
 * @package     EverAccounting
 * @class       Contact
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;
use EverAccounting\Abstracts\MetaData;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Contact object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property int $user_id
 * @property string $name
 * @property string $company
 * @property string $email
 * @property string $phone
 * @property string $website
 * @property string $vat_number
 * @property string $birth_date
 * @property string $street
 * @property string $city
 * @property string $state
 * @property string $postcode
 * @property string $country
 * @property string $type
 * @property string $currency_code
 * @property string $thumbnail_id
 * @property boolean $enabled
 * @property int $creator_id
 * @property string $date_created
 */
class Contact extends MetaData {
	/**
	 * Contact id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Contact data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'user_id'       => null,
		'name'          => '',
		'company'       => '',
		'email'         => '',
		'phone'         => '',
		'birth_date'    => '',
		'street'        => '',
		'city'          => '',
		'state'         => '',
		'postcode'      => '',
		'country'       => '',
		'website'       => '',
		'vat_number'    => '',
		'currency_code' => '',
		'type'          => 'contact',
		'thumbnail_id'  => null,
		'enabled'       => 1,
		'creator_id'    => null,
		'date_created'  => null,
	);

	/**
	 * Stores the contact object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $filter;

	/**
	 * Meta type.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	protected $meta_type = 'contact';

	/**
	 * Get contact by field
	 *
	 * @param int|string $value
	 * @param string $field
	 * @param string $type
	 *
	 * @return object|false Raw currency object
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_data_by( $value, $field = 'id' ) {
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
			case 'user_id':
				$value    = (int) $value;
				$db_field = 'user_id';
				break;
			case 'id':
			default:
				$db_field = 'id';
				break;
		}

		$_item = wp_cache_get( "{$db_field}_{$value}", 'ea_contacts' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_contacts WHERE $db_field = %s LIMIT 1", $value ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_contact( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_contacts' );
			wp_cache_add( "{$_item->type}_id_{$_item->id}", $_item, 'ea_contacts' );
			if ( ! empty( $_item->user_id ) ) {
				wp_cache_add( "{$_item->type}_user_id_{$_item->user_id}", $_item, 'ea_contacts' );
			}
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_contact( $_item, 'raw' );
		}

		return new Contact( $_item );
	}

	/**
	 * Contact constructor.
	 *
	 * @param $contact
	 *
	 * @since 1.2.1
	 */
	public function __construct( $contact ) {
		foreach ( get_object_vars( $contact ) as $key => $value ) {
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
	 * @param string $key contact field to retrieve.
	 *
	 * @return mixed Value of the given contact field (if set).
	 * @since 1.2.1
	 */
	public function __get( $key ) {
		$value = '';
		if ( method_exists( $this, 'get_' . $key ) ) {
			eaccounting_doing_it_wrong( __FUNCTION__, sprintf( __( 'Object data such as "%s" should not be accessed directly. Use getters and setters.', 'wp-ever-accounting' ), $key ), '1.1.0' );
			$value = $this->{'get_' . $key}();
		} else if ( property_exists( $this, $key ) && is_callable( array( $this, $key ) ) ) {
			$value = $this->$key;
		} else if ( $this->meta_exists( $key ) ) {
			$value = $this->get_meta( $key );
			$value = eaccounting_sanitize_contact_field( $key, $value, $this->id, $this->filter );
		}

		return $value;
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key contact key to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		if ( isset( $this->$key ) ) {
			unset( $this->$key );
		}
	}

	/**
	 * Filter contact object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Contact|Object
	 * @since 1.2.1
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::get_data_by( $this->id );
		}

		return eaccounting_sanitize_contact( $this, $filter );
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * @param string $key Property
	 *
	 * @return bool
	 * @since 1.2.1
	 */
	public function has_prop( string $key ) {
		return $this->__isset( $key );
	}

	/**
	 * Determine whether the contact exists in the database.
	 *
	 * @return bool True if contact exists in the database, false if not.
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
		return get_object_vars( $this );
	}

}
