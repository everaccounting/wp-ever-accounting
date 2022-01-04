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

		// meta
		// 'total_paid'       => 0.00,
		// 'total_due'        => 0.00,
		// 'total_payable'    => 0.00,
		// 'total_receivable' => 0.00,
	);

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data_type = array(
		'id'            => '%d',
		'user_id'       => '%d',
		'name'          => '%s',
		'company'       => '%s',
		'email'         => '%s',
		'phone'         => '%s',
		'website'       => '%s',
		'vat_number'    => '%s',
		'birth_date'    => '%s',
		'street'        => '%s',
		'city'          => '%s',
		'state'         => '%s',
		'postcode'      => '%s',
		'country'       => '%s',
		'type'          => '%s',
		'currency_code' => '%s',
		'thumbnail_id'  => '%d',
		'enabled'       => '%d',
		'creator_id'    => '%d',
		'date_created'  => '%s',
	);

	/**
	 * Meta type.
	 *
	 * @var string
	 */
	protected $meta_type = 'contact';

	/**
	 * Contact constructor.
	 *
	 * Get the Contact if ID is passed, otherwise the contact is new and empty.
	 *
	 * @param int|object|Contact $contact object to read.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $contact = 0 ) {
		parent::__construct();
		if ( $contact instanceof self ) {
			$this->set_id( $contact->get_id() );
		} elseif ( is_object( $contact ) && ! empty( $contact->id ) ) {
			$this->set_id( $contact->id );
		} elseif ( is_array( $contact ) && ! empty( $contact['id'] ) ) {
			$this->set_props( $contact );
		} elseif ( is_numeric( $contact ) ) {
			$this->set_id( $contact );
		} else {
			$this->set_object_read( true );
		}

		$data = self::get_raw( $this->get_id(), 'id' );

		if ( $data ) {
			$this->set_props( $data );
			$this->set_object_read( true );
		} else {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete documents from the database.
	| Written in abstract fashion so that the way documents are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/
	/**
	 * Retrieve the object from database instance.
	 *
	 * @param int|string $contact_id Object id.
	 * @param string     $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_raw( $contact_id, $field = 'id' ) {
		global $wpdb;
		if ( 'id' === $field ) {
			$contact_id = (int) $contact_id;
		} else {
			$contact_id = trim( $contact_id );
		}
		if ( ! $contact_id ) {
			return false;
		}

		$contact = wp_cache_get( $contact_id, 'ea_contacts' );

		if ( ! $contact ) {

			switch ( $field ) {
				case 'id':
				default:
					$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_contacts WHERE id = %d LIMIT 1", $contact_id );
					break;
			}

			$contact = $wpdb->get_row( $sql ); //phpcs:ignore

			if ( ! $contact ) {
				return false;
			}

			wp_cache_add( $contact->id, $contact, 'ea_contacts' );
		}

		return apply_filters( 'eaccounting_contact_result', $contact );
	}


	/**
	 *  Insert a contact in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function insert( $args = array() ) {
		global $wpdb;
		$data_arr = $this->to_array();
		$data     = wp_array_slice_assoc( $data_arr, array_keys( $this->data_type ) );
		$format   = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data     = wp_unslash( $data );

		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before a contact is inserted in the database.
		 *
		 * @param array $data contact data to be inserted.
		 * @param string $data_arr Sanitized contact data.
		 * @param contact $contact contact object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_contact', $data, $data_arr, $this );

		/**
		 * Fires immediately before a contact is inserted in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of contact.
		 *
		 * @param array $data contact data to be inserted.
		 * @param string $data_arr Sanitized contact data.
		 * @param contact $contact contact object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_pre_insert_contact_{$this->type}", $data, $data_arr, $this );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_contacts', $data, $format ) ) {
			return new \WP_Error( 'db_insert_error', __( 'Could not insert contact into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );

		/**
		 * Fires immediately after a contact is inserted in the database.
		 *
		 * @param int $contact_id contact id.
		 * @param array $data contact has been inserted.
		 * @param array $data_arr Sanitized contact data.
		 * @param contact $contact contact object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_contact', $this->id, $data, $data_arr, $this );

		/**
		 * Fires immediately after a contact is inserted in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of contact.
		 *
		 * @param int $contact_id contact id.
		 * @param array $data contact has been inserted.
		 * @param array $data_arr Sanitized contact data.
		 * @param contact $contact contact object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_insert_contact_{$this->type}", $this->id, $data, $data_arr, $this );

		return true;
	}

	/**
	 *  Update a contact in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function update( $args = array() ) {
		global $wpdb;
		$changes = $this->get_changes();
		$data    = wp_array_slice_assoc( $changes, array_keys( $this->data_type ) );
		$format  = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data    = wp_unslash( $data );
		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before an existing contact is updated in the database.
		 *
		 * @param int $contact_id contact id.
		 * @param array $data contact data.
		 * @param array $changes The data will be updated.
		 * @param contact $contact contact object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_contact', $this->get_id(), $this->to_array(), $changes, $this );

		/**
		 * Fires immediately before an existing contact is updated in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of contact.
		 *
		 * @param int $contact_id contact id.
		 * @param array $data contact data.
		 * @param array $changes The data will be updated.
		 * @param contact $contact contact object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_pre_update_contact_{$this->type}", $this->get_id(), $this->to_array(), $changes, $this );

		if ( false === $wpdb->update( $wpdb->prefix . 'ea_contacts', $data, [ 'id' => $this->get_id() ], $format, [ 'id' => '%d' ] ) ) {
			return new \WP_Error( 'db_update_error', __( 'Could not update contact in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after a existing contact is updated in the database.
		 *
		 * @param int $contact_id contact id.
		 * @param array $data contact data.
		 * @param array $changes The data will be updated.
		 * @param contact $contact Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_contact', $this->get_id(), $this->to_array(), $changes, $this );

		/**
		 * Fires immediately after a existing contact is updated in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of contact.
		 *
		 * @param int $contact_id contact id.
		 * @param array $data contact data.
		 * @param array $changes The data will be updated.
		 * @param contact $contact Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_update_contact_{$this->type}", $this->get_id(), $this->to_array(), $changes, $this );

		return true;
	}

	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	public function save() {
		$user_id = get_current_user_id();

		// Check if the contact name exist or not
		if ( empty( $this->get_prop( 'name' ) ) ) {
			return new \WP_Error( 'invalid_contact_name', esc_html__( 'Contact name is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'date_created' ) ) || '0000-00-00 00:00:00' === $this->get_prop( 'date_created' ) ) {
			$this->set_date_prop( 'date_created', current_time( 'mysql' ) );
		}

		if ( empty( $this->get_prop( 'creator_id' ) ) ) {
			$this->set_prop( 'creator_id', $user_id );
		}

		if ( $this->exists() ) {
			$is_error = $this->update();
		} else {
			$is_error = $this->insert();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->save_meta_data();
		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), 'ea_contacts' );
		wp_cache_delete( $this->get_id(), 'ea_contactmeta' );
		wp_cache_set( 'last_changed', microtime(), 'ea_contacts' );

		/**
		 * Fires immediately after a contact is inserted or updated in the database.
		 *
		 * @param int $contact_id Contact id.
		 * @param Contact $contact Contact object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_saved_contact', $this->get_id(), $this );

		/**
		 * Fires immediately after a contact is inserted or updated in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of contact.
		 *
		 * @param int $contact_id Contact id.
		 * @param Contact $contact Contact object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_saved_contact_{$this->type}", $this->get_id(), $this );

		return $this->get_id();
	}

	/**
	 * Deletes the object from database.
	 *
	 * @return array|false true on success, false on failure.
	 * @since 1.1.0
	 */
	public function delete() {
		global $wpdb;
		if ( ! $this->exists() ) {
			return false;
		}

		$data = $this->to_array();

		/**
		 * Filters whether an contact delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $contact_id Contact id.
		 * @param array $data Contact data array.
		 * @param Contact $contact Transaction object.
		 *
		 * @since 1.2.1
		 */
		$check = apply_filters( 'eaccounting_check_delete_contact', null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before a contact is deleted.
		 *
		 * @param int $contact_id Contact id.
		 * @param array $data Contact data array.
		 * @param Contact $contact Contact object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_delete_contact', $this->get_id(), $data, $this );

		$result = $wpdb->delete( $wpdb->prefix . 'ea_contacts', array( 'id' => $this->get_id() ) );
		if ( ! $result ) {
			return false;
		}

		/**
		 * Fires after an contact is deleted.
		 *
		 * @param int $contact_id Contact id.
		 * @param array $data Contact data array.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_delete_contact', $this->get_id(), $data );

		// Clear object.
		wp_cache_delete( $this->get_id(), 'ea_contacts' );
		wp_cache_delete( $this->get_id(), 'ea_contactmeta' );
		wp_cache_set( 'last_changed', microtime(), 'ea_contacts' );
		$this->set_id( 0 );
		$this->set_defaults();

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Functions for getting item data. Getter methods won't change anything unless
	| just returning from the props.
	|
	*/
	/**
	 * Get contact's wp user ID.
	 *
	 * @return int|null
	 * @since 1.0.2
	 */
	public function get_user_id() {
		return $this->get_prop( 'user_id' );
	}

	/**
	 * Get contact Name.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_name() {
		return $this->get_prop( 'name' );
	}

	/**
	 * Get contact company.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_company() {
		return $this->get_prop( 'company' );
	}

	/**
	 * Get contact's email.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_email() {
		return $this->get_prop( 'email' );
	}

	/**
	 * Get contact's phone number.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_phone() {
		return $this->get_prop( 'phone' );
	}

	/**
	 * Get contact's website number.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_website() {
		return $this->get_prop( 'website' );
	}

	/**
	 * Get contact's birth_date.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_birth_date() {
		return $this->get_prop( 'birth_date' );
	}

	/**
	 * Get contact's street.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_street() {
		return $this->get_prop( 'street' );
	}

	/**
	 * Get contact's city.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_city() {
		return $this->get_prop( 'city' );
	}

	/**
	 * Get contact's state.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_state() {
		return $this->get_prop( 'state' );
	}

	/**
	 * Get contact's postcode.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_postcode() {
		return $this->get_prop( 'postcode' );
	}

	/**
	 * Get contact's country.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_country() {
		return $this->get_prop( 'country' );
	}

	/**
	 * Get contact's country.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_country_nicename() {
		$countries = eaccounting_get_countries();

		return isset( $countries[ $this->get_country() ] ) ? $countries[ $this->get_country() ] : $this->get_country();
	}

	/**
	 * Get contact's vat number.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_vat_number() {
		return $this->get_prop( 'vat_number' );
	}

	/**
	 * Get the currency code of the contact.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_currency_code() {
		return $this->get_prop( 'currency_code' );
	}

	/**
	 * Get the type of contact.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_type() {
		return $this->get_prop( 'type' );
	}

	/**
	 * Get thumbnail id
	 *
	 * @return int
	 * @since 1.1.0
	 */
	public function get_thumbnail_id() {
		return $this->get_prop( 'thumbnail_id' );
	}

	/**
	 * get object status
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function get_enabled() {
		return $this->get_prop( 'enabled' );
	}

	/**
	 * Return object created by.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_creator_id() {
		return $this->get_prop( 'creator_id' );
	}

	/**
	 * Get object created date.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_date_created() {
		return $this->get_prop( 'date_created' );
	}

	/**
	 * Total amount paid by contact
	 *
	 * @return float
	 */
	public function get_total_paid() {
		return (float) $this->get_meta( 'total_paid' );
	}

	/**
	 * Total amount due by contact
	 *
	 * @return float
	 */
	public function get_total_due() {
		return (float) $this->get_meta( 'total_due' );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting contact data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * Set wp user id.
	 *
	 * @param int $id User ID
	 *
	 * @since 1.0.2
	 */
	public function set_user_id( $id ) {
		$this->set_prop( 'user_id', absint( $id ) );
	}

	/**
	 * Set contact name.
	 *
	 * @param string $name Name
	 *
	 * @since 1.0.2
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * Set contact company.
	 *
	 * @param string $company Company Name
	 *
	 * @since 1.0.2
	 */
	public function set_company( $company ) {
		$this->set_prop( 'company', eaccounting_clean( $company ) );
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
	 * Set contact's phone.
	 *
	 * @param string $value Phone Number
	 *
	 * @since 1.0.2
	 */
	public function set_phone( $value ) {
		$this->set_prop( 'phone', eaccounting_clean( $value ) );
	}


	/**
	 * Set contact's birth-date.
	 *
	 * @param string $date Birth-date
	 *
	 * @since 1.0.2
	 */
	public function set_birth_date( $date ) {
		$this->set_date_prop( 'birth_date', $date, 'Y-m-d' );
	}

	/**
	 * Set contact's website.
	 *
	 * @param string $value website
	 *
	 * @since 1.0.2
	 */
	public function set_website( $value ) {
		$this->set_prop( 'website', esc_url( $value ) );
	}

	/**
	 * Set contact's street.
	 *
	 * @param string $value Street
	 *
	 * @since 1.0.2
	 */
	public function set_street( $value ) {
		$this->set_prop( 'street', sanitize_text_field( $value ) );
	}

	/**
	 * Set contact's city.
	 *
	 * @param string $city City
	 *
	 * @since 1.0.2
	 */
	public function set_city( $city ) {
		$this->set_prop( 'city', sanitize_text_field( $city ) );
	}

	/**
	 * Set contact's state.
	 *
	 * @param string $state State
	 *
	 * @since 1.0.2
	 */
	public function set_state( $state ) {
		$this->set_prop( 'state', sanitize_text_field( $state ) );
	}

	/**
	 * Set contact's postcode.
	 *
	 * @param string $postcode Postcode
	 *
	 * @since 1.0.2
	 */
	public function set_postcode( $postcode ) {
		$this->set_prop( 'postcode', sanitize_text_field( $postcode ) );
	}

	/**
	 * Set contact country.
	 *
	 * @param string $country Country
	 *
	 * @since 1.0.2
	 */
	public function set_country( $country ) {
		if ( array_key_exists( $country, eaccounting_get_countries() ) ) {
			$this->set_prop( 'country', $country );
		}
	}

	/**
	 * Set contact's tax_number.
	 *
	 * @param string $value vat-number
	 *
	 * @since 1.0.2
	 */
	public function set_vat_number( $value ) {
		$this->set_prop( 'vat_number', eaccounting_clean( $value ) );
	}

	/**
	 * Set contact's currency_code.
	 *
	 * @param string $value currency_code
	 *
	 * @since 1.0.2
	 */
	public function set_currency_code( $value ) {
		if ( eaccounting_get_currency( $value ) ) {
			$this->set_prop( 'currency_code', eaccounting_clean( $value ) );
		}
	}

	/**
	 * Set contact type.
	 *
	 * @param string $type Contact type
	 *
	 * @since 1.0.2
	 */
	public function set_type( $type ) {
		if ( array_key_exists( $type, eaccounting_get_contact_types() ) ) {
			$this->set_prop( 'type', $type );
		}
	}

	/**
	 * Set avatar id
	 *
	 * @param int $thumbnail_id Thumbnail id
	 *
	 * @since 1.1.0
	 */
	public function set_thumbnail_id( $thumbnail_id ) {
		$this->set_prop( 'thumbnail_id', absint( $thumbnail_id ) );
	}

	/**
	 * Set object status.
	 *
	 * @param int $enabled Contact enabled
	 *
	 * @since 1.0.2
	 */
	public function set_enabled( $enabled ) {
		$this->set_prop( 'enabled', (int) $enabled );
	}


	/**
	 * Set object creator id.
	 *
	 * @param int $creator_id Creator id
	 *
	 * @since 1.0.2
	 */
	public function set_creator_id( $creator_id = null ) {
		if ( null === $creator_id ) {
			$creator_id = get_current_user_id();
		}
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}

	/**
	 * Set object created date.
	 *
	 * @param string $date Created date
	 *
	 * @since 1.0.2
	 */
	public function set_date_created( $date = null ) {
		if ( null === $date ) {
			$date = current_time( 'mysql' );
		}
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Set paid.
	 *
	 * @param string $value paid amount.
	 */
	public function set_total_paid( $value ) {
		$this->update_meta_data( 'total_paid', (float) $value );
	}

	/**
	 * Set due.
	 *
	 * @param string $value due amount.
	 */
	public function set_total_due( $value ) {
		$this->update_meta_data( 'total_due', (float) $value );
	}


	/*
	|--------------------------------------------------------------------------
	| Extra
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return this customer's avatar.
	 *
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function get_avatar_url( $args = array() ) {
		if ( ! empty( $this->get_thumbnail_id() ) && $url = wp_get_attachment_thumb_url( $this->get_thumbnail_id() ) ) { //phpcs:ignore
			return $url;
		}

		return get_avatar_url( $this->get_email(), wp_parse_args( $args, array( 'size' => '100' ) ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditional
	|--------------------------------------------------------------------------
	*/

	/**
	 * Alias self::get_enabled()
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return eaccounting_string_to_bool( $this->get_prop( 'enabled' ) );
	}
}
