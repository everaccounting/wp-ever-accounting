<?php
/**
 * Handle the core contact object.
 *
 * @package     EverAccounting
 * @class       Contact
 * @version     1.0.2
 *
 */

namespace EverAccounting\Contacts;

use EverAccounting\Abstracts\Model;
use  EverAccounting\DateTime;

defined( 'ABSPATH' ) || exit();

class Contact extends Model {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $object_type = 'contact';

	/***
	 * Object table name.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $table = 'ea_contacts';

	/**
	 * Contact Data array.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $data = array(
		'user_id'       => null,
		'name'          => '',
		'email'         => '',
		'phone'         => '',
		'fax'           => '',
		'birth_date'    => '',
		'address'       => '',
		'country'       => '',
		'website'       => '',
		'tax_number'    => '',
		'currency_code' => '',
		'type'          => '',
		'note'          => '',
		'enabled'       => 1,
		'creator_id'    => '',
		'date_created'  => '',
	);

	/**
	 * Contact constructor.
	 *
	 * @since 1.0.2
	 *
	 * @param mixed $data
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} else {
			$this->set_id( 0 );
		}

		if ( $this->get_id() > 0 && ! $this->object_read ) {
			$this->read();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get contact's wp user ID.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return int|null
	 */
	public function get_user_id( $context = 'edit' ) {
		return $this->get_prop( 'user_id', $context );
	}

	/**
	 * Get contact Name.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get contact's email.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_email( $context = 'edit' ) {
		return $this->get_prop( 'email', $context );
	}

	/**
	 * Get contact's phone number.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_phone( $context = 'edit' ) {
		return $this->get_prop( 'phone', $context );
	}

	/**
	 * Get contact's phone number.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_fax( $context = 'edit' ) {
		return $this->get_prop( 'fax', $context );
	}

	/**
	 * Get contact's birth date.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return DateTime|string
	 */
	public function get_birth_date( $context = 'edit' ) {
		return $this->get_prop( 'birth_date', $context );
	}

	/**
	 * Get contact's address.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_address( $context = 'edit' ) {
		return $this->get_prop( 'address', $context );
	}

	/**
	 * Get contact's country.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_country( $context = 'edit' ) {
		return $this->get_prop( 'country', $context );
	}

	/**
	 * Get contact's website number.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_website( $context = 'edit' ) {
		return $this->get_prop( 'website', $context );
	}

	/**
	 * Get contact's tax number.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_tax_number( $context = 'edit' ) {
		return $this->get_prop( 'tax_number', $context );
	}

	/**
	 * Get the currency code of the contact.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Get the type of contact.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Get contact's note.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_note( $context = 'edit' ) {
		return $this->get_prop( 'note', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set wp user id.
	 *
	 * @since 1.0.2
	 *
	 * @param $id
	 *
	 */
	public function set_user_id( $id ) {
		$this->set_prop( 'user_id', absint( $id ) );
	}

	/**
	 * Set contact name.
	 *
	 * @since 1.0.2
	 *
	 * @param $name
	 *
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * Set contact's email.
	 *
	 * @since 1.0.2
	 *
	 * @param string $value Email.
	 *
	 */
	public function set_email( $value ) {
		if ( $value && ! is_email( $value ) ) {
			$this->error( 'contact_invalid_email', __( 'Invalid email address', 'wp-ever-accounting' ) );
		}
		$this->set_prop( 'email', sanitize_email( $value ) );
	}

	/**
	 * Set contact's phone.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_phone( $value ) {
		$this->set_prop( 'phone', eaccounting_clean( $value ) );
	}

	/**
	 * Set contact's fax.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_fax( $value ) {
		$this->set_prop( 'fax', eaccounting_clean( $value ) );
	}

	/**
	 * Set contact's birth date.
	 *
	 * @since 1.0.2
	 *
	 * @param $date
	 *
	 */
	public function set_birth_date( $date ) {
		$this->set_date_prop( 'birth_date', $date );
	}

	/**
	 * Set contact's phone.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_address( $value ) {
		$this->set_prop( 'address', sanitize_textarea_field( $value ) );
	}

	/**
	 * Set contact country.
	 *
	 * @since 1.0.2
	 *
	 * @param $country
	 *
	 */
	public function set_country( $country ) {
		if ( array_key_exists( $country, eaccounting_get_countries() ) ) {
			$this->set_prop( 'country', $country );
		}
	}

	/**
	 * Set contact's website.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_website( $value ) {
		$this->set_prop( 'website', esc_url( $value ) );
	}

	/**
	 * Set contact's tax_number.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_tax_number( $value ) {
		$this->set_prop( 'tax_number', eaccounting_clean( $value ) );
	}

	/**
	 * Set contact's currency_code.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_currency_code( $value ) {
		$currency = eaccounting_get_currency( $value );
		if ( $currency && $currency->exists() ) {
			$this->set_prop( 'currency_code', eaccounting_clean( $value ) );
		}
	}

	/**
	 * Set contact type.
	 *
	 * @since 1.0.2
	 *
	 * @param $type
	 *
	 */
	public function set_type( $type ) {
		if ( array_key_exists( $type, eaccounting_get_contact_types() ) ) {
			$this->set_prop( 'type', $type );
		}
	}

	/**
	 * Set contact's note.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_note( $value ) {
		$this->set_prop( 'note', sanitize_textarea_field( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Extra
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return this customer's avatar.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_avatar_url() {
		return get_avatar_url( $this->get_email() );
	}
}