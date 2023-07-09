<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Contact
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Contact extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $table_name = 'ea_contacts';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'contact';

	/**
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $meta_type = 'ea_contact';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'id'            => null,
		'name'          => '',
		'type'          => '',
		'company'       => '',
		'email'         => '',
		'phone'         => '',
		'address_1'     => '',
		'address_2'     => '',
		'city'          => '',
		'state'         => '',
		'postcode'      => '',
		'country'       => '',
		'website'       => '',
		'vat_number'    => '',
		'vat_exempt'    => 0,
		'currency_code' => '',
		'thumbnail_id'  => null,
		'user_id'       => null,
		'status'        => 'active',
		'uuid'          => '',
		'creator_id'    => null,
		'date_updated'  => null,
		'date_created'  => null,
	);

	/**
	 * Model constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$this->core_data['country']       = eac_get_base_country();
		$this->core_data['currency_code'] = eac_get_base_currency();
		$this->core_data['uuid']          = wp_generate_uuid4();
		parent::__construct( $data );
	}

	/**
	 * When the object is cloned, make sure meta is duplicated correctly.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		parent::__clone();
		$this->set_name( $this->get_name() . ' ' . __( '(Copy)', 'wp-ever-accounting' ) );
		$this->set_email( '' );
		$this->set_phone( '' );
		$this->set_uuid( eac_generate_uuid() );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 * @since 1.0.0
	 */
	public function save() {
		// Creator ID.
		if ( empty( $this->get_creator_id() ) && ! $this->exists() && is_user_logged_in() ) {
			$this->set_creator_id( get_current_user_id() );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_date_updated( current_time( 'mysql' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_date_created() ) ) {
			$this->set_date_created( current_time( 'mysql' ) );
		}

		if ( empty( $this->get_uuid() ) ) {
			$this->set_uuid( eac_generate_uuid() );
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
	 * Get contact's wp user ID.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return int|null
	 * @since 1.0.2
	 */
	public function get_user_id( $context = 'edit' ) {
		return $this->get_prop( 'user_id', $context );
	}

	/**
	 * Set wp user id.
	 *
	 * @param int $id WP user id.
	 *
	 * @since 1.0.2
	 */
	public function set_user_id( $id ) {
		$this->set_prop( 'user_id', absint( $id ) );
	}


	/**
	 * Get contact Name.
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
	 * Set contact name.
	 *
	 * @param string $name Contact name.
	 *
	 * @since 1.0.2
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eac_clean( $name ) );
	}


	/**
	 * Get contact company.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_company( $context = 'edit' ) {
		return $this->get_prop( 'company', $context );
	}


	/**
	 * Set contact company.
	 *
	 * @param string $company Contact company.
	 *
	 * @since 1.0.2
	 */
	public function set_company( $company ) {
		$this->set_prop( 'company', eac_clean( $company ) );
	}


	/**
	 * Get contact's email.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_email( $context = 'edit' ) {
		return $this->get_prop( 'email', $context );
	}


	/**
	 * Set contact's email.
	 *
	 * @param string $value Email.
	 *
	 * @since 1.0.2
	 */
	public function set_email( $value ) {
		if ( $value && is_email( $value ) ) {
			$this->set_prop( 'email', sanitize_email( $value ) );
		}
	}

	/**
	 * Get contact's phone number.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_phone( $context = 'edit' ) {
		return $this->get_prop( 'phone', $context );
	}

	/**
	 * Set contact's phone.
	 *
	 * @param string $value Phone.
	 *
	 * @since 1.0.2
	 */
	public function set_phone( $value ) {
		$this->set_prop( 'phone', eac_clean( $value ) );
	}

	/**
	 * Get contact's website number.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_website( $context = 'edit' ) {
		return $this->get_prop( 'website', $context );
	}

	/**
	 * Set contact's website.
	 *
	 * @param string $value Website.
	 *
	 * @since 1.0.2
	 */
	public function set_website( $value ) {
		$this->set_prop( 'website', esc_url( $value ) );
	}

	/**
	 * Get contact's adrress 1.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_address_1( $context = 'edit' ) {
		return $this->get_prop( 'address_1', $context );
	}

	/**
	 * Set contact's address_1.
	 *
	 * @param string $value Street.
	 *
	 * @since 1.0.2
	 */
	public function set_address_1( $value ) {
		$this->set_prop( 'address_1', sanitize_text_field( $value ) );
	}

	/**
	 * Get contact's adrress 2.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_address_2( $context = 'edit' ) {
		return $this->get_prop( 'address_2', $context );
	}

	/**
	 * Set contact's address_2.
	 *
	 * @param string $value Street.
	 *
	 * @since 1.0.2
	 */
	public function set_address_2( $value ) {
		$this->set_prop( 'address_2', sanitize_text_field( $value ) );
	}

	/**
	 * Get contact's city.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_city( $context = 'edit' ) {
		return $this->get_prop( 'city', $context );
	}


	/**
	 * Set contact's city.
	 *
	 * @param string $city City.
	 *
	 * @since 1.0.2
	 */
	public function set_city( $city ) {
		$this->set_prop( 'city', sanitize_text_field( $city ) );
	}


	/**
	 * Get contact's state.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_state( $context = 'edit' ) {
		return $this->get_prop( 'state', $context );
	}


	/**
	 * Set contact's state.
	 *
	 * @param string $state State.
	 *
	 * @since 1.0.2
	 */
	public function set_state( $state ) {
		$this->set_prop( 'state', sanitize_text_field( $state ) );
	}

	/**
	 * Get contact's postcode.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_postcode( $context = 'edit' ) {
		return $this->get_prop( 'postcode', $context );
	}


	/**
	 * Set contact's postcode.
	 *
	 * @param string $postcode Postcode.
	 *
	 * @since 1.0.2
	 */
	public function set_postcode( $postcode ) {
		$this->set_prop( 'postcode', sanitize_text_field( $postcode ) );
	}

	/**
	 * Get contact's country.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_country( $context = 'edit' ) {
		return $this->get_prop( 'country', $context );
	}


	/**
	 * Set contact country.
	 *
	 * @param string $country Country.
	 *
	 * @since 1.0.2
	 */
	public function set_country( $country ) {
		if ( array_key_exists( $country, eac_get_countries() ) ) {
			$this->set_prop( 'country', $country );
		}
	}

	/**
	 * Get contact's vat number.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_vat_number( $context = 'edit' ) {
		return $this->get_prop( 'vat_number', $context );
	}

	/**
	 * Set contact's tax_number.
	 *
	 * @param string $value Tax number.
	 *
	 * @since 1.0.2
	 */
	public function set_vat_number( $value ) {
		$this->set_prop( 'vat_number', eac_clean( $value ) );
	}

	/**
	 * Get vat exempt status of the contact.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_vat_exempt( $context = 'edit' ) {
		return $this->get_prop( 'vat_exempt', $context );
	}

	/**
	 * Set vat exempt status of the contact.
	 *
	 * @param string $value Vat exempt status.
	 *
	 * @since 1.0.2
	 */
	public function set_vat_exempt( $value ) {
		$this->set_prop( 'vat_exempt', $this->string_to_int( $value ) );
	}

	/**
	 * Get the currency code of the contact.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Set contact's currency.
	 *
	 * @param string $value Currency code.
	 *
	 * @since 1.0.2
	 */
	public function set_currency_code( $value ) {
		$this->set_prop( 'currency_code', eac_clean( $value ) );
	}

	/**
	 * Get the type of contact.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Set contact type.
	 *
	 * @param string $type Contact type.
	 *
	 * @since 1.0.2
	 */
	public function set_type( $type ) {
		if ( array_key_exists( $type, eac_get_contact_types() ) ) {
			$this->set_prop( 'type', $type );
		}
	}

	/**
	 * Get the category status.
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
	 * Set the category status.
	 *
	 * @param string $value Category status.
	 *
	 * @since 1.0.2
	 */
	public function set_status( $value ) {
		if ( in_array( $value, array( 'active', 'inactive' ), true ) ) {
			$this->set_prop( 'status', $value );
		}
	}

	/**
	 * Get the unique_hash.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_uuid( $context = 'edit' ) {
		return $this->get_prop( 'uuid', $context );
	}

	/**
	 * Set the uuid.
	 *
	 * @param string $key uuid.
	 */
	public function set_uuid( $key ) {
		$this->set_prop( 'uuid', $key );
	}

	/**
	 * Get the creator id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return int
	 */
	public function get_creator_id( $context = 'edit' ) {
		return $this->get_prop( 'creator_id', $context );
	}

	/**
	 * Set the creator id.
	 *
	 * @param int $creator_id creator id.
	 */
	public function set_creator_id( $creator_id ) {
		$this->set_prop( 'creator_id', absint( $creator_id ) );
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
	 * @param string $date_created date created.
	 */
	public function set_date_created( $date_created ) {
		$this->set_date_prop( 'date_created', $date_created );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals methods
	|--------------------------------------------------------------------------
	| Methods that check an object's status, typically based on internal or meta data.
	*/

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
	 * Is the vat exempt?
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function is_vat_exempt() {
		return ! empty( $this->get_vat_number() );
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

	/**
	 * Get formatted name.
	 *
	 * @return string
	 * @since 1.1.6
	 */
	public function get_formatted_name() {
		return sprintf( '%s (#%s)', $this->get_name(), $this->get_id() );
	}

	/**
	 * Get formatted address.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_address() {
		$data = array(
			'name'      => $this->get_name(),
			'company'   => $this->get_company(),
			'address_1' => $this->get_address_1(),
			'address_2' => $this->get_address_2(),
			'city'      => $this->get_city(),
			'state'     => $this->get_state(),
			'postcode'  => $this->get_postcode(),
			'country'   => $this->get_country(),
		);

		return eac_get_formatted_address( $data );
	}
}
