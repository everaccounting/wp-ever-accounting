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
		'user_id'          => null,
		'name'             => '',
		'company'          => '',
		'email'            => '',
		'phone'            => '',
		'birth_date'       => '',
		'street'           => '',
		'city'             => '',
		'state'            => '',
		'postcode'         => '',
		'country'          => '',
		'website'          => '',
		'vat_number'       => '',
		'currency_code'    => '',
		'type'             => 'contact',
		'thumbnail_id'     => null,
		'enabled'          => 1,
		'creator_id'       => null,
		'date_created'     => null,

		//meta
//		'total_paid'       => 0.00,
//		'total_due'        => 0.00,
//		'total_payable'    => 0.00,
//		'total_receivable' => 0.00,
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
	public function __construct( $contact ) {
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


	/**
	 * Retrieve the object from database instance.
	 *
	 * @param int|string $contact_id Object id.
	 * @param string $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	static function get_raw( $contact_id, $field = 'id' ) {
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

			$contact = $wpdb->get_row( $sql );

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
	 * @param array $fields An array of database fields and type.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function insert( $fields ) {
		global $wpdb;
		$data_arr = $this->to_array();
		$data     = wp_array_slice_assoc( $data_arr, array_keys( $fields ) );
		$format   = wp_array_slice_assoc( $fields, array_keys( $data ) );
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
			return new \WP_Error( 'eaccounting_contact_db_insert_error', __( 'Could not insert contact into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );

		/**
		 * Fires immediately after an contact is inserted in the database.
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
		 * Fires immediately after an contact is inserted in the database.
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
	 * @param array $fields An array of database fields and type.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function update( $fields ) {
		global $wpdb;
		$changes = $this->get_changes();
		$data    = wp_array_slice_assoc( $changes, array_keys( $fields ) );
		$format  = wp_array_slice_assoc( $fields, array_keys( $data ) );
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
			return new \WP_Error( 'eaccounting_contact_db_update_error', __( 'Could not update contact in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
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
		do_action( 'eaccounting_pre_update_contact', $this->get_id(), $this->to_array(), $changes, $this );

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
		do_action( "eaccounting_pre_update_contact_{$this->type}", $this->get_id(), $this->to_array(), $changes, $this );


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
		$fields  = array(
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
			$is_error = $this->update( $fields );
		} else {
			$is_error = $this->insert( $fields );
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
		 *
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
		 *
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
		 * Fires before an contact is deleted.
		 *
		 * @param int $contact_id Contact id.
		 * @param array $data Contact data array.
		 * @param Contact $contact Contact object.
		 *
		 * @since 1.2.1
		 *
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
		 *
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
	 * @param $id
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_user_id( $id ) {
		$this->set_prop( 'user_id', absint( $id ) );
	}

	/**
	 * Set contact name.
	 *
	 * @param $name
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * Set contact company.
	 *
	 * @param $company
	 *
	 * @since 1.0.2
	 *
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
	 *
	 */
	public function set_email( $value ) {
		if ( $value && is_email( $value ) ) {
			$this->set_prop( 'email', sanitize_email( $value ) );
		}
	}

	/**
	 * Set contact's phone.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_phone( $value ) {
		$this->set_prop( 'phone', eaccounting_clean( $value ) );
	}


	/**
	 * Set contact's birth date.
	 *
	 * @param $date
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_birth_date( $date ) {
		$this->set_date_prop( 'birth_date', $date, 'Y-m-d' );
	}

	/**
	 * Set contact's website.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_website( $value ) {
		$this->set_prop( 'website', esc_url( $value ) );
	}

	/**
	 * Set contact's street.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_street( $value ) {
		$this->set_prop( 'street', sanitize_text_field( $value ) );
	}

	/**
	 * Set contact's city.
	 *
	 * @param $city
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_city( $city ) {
		$this->set_prop( 'city', sanitize_text_field( $city ) );
	}

	/**
	 * Set contact's state.
	 *
	 * @param $state
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_state( $state ) {
		$this->set_prop( 'state', sanitize_text_field( $state ) );
	}

	/**
	 * Set contact's postcode.
	 *
	 * @param $postcode
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_postcode( $postcode ) {
		$this->set_prop( 'postcode', sanitize_text_field( $postcode ) );
	}

	/**
	 * Set contact country.
	 *
	 * @param $country
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_country( $country ) {
		if ( array_key_exists( $country, eaccounting_get_countries() ) ) {
			$this->set_prop( 'country', $country );
		}
	}

	/**
	 * Set contact's tax_number.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_vat_number( $value ) {
		$this->set_prop( 'vat_number', eaccounting_clean( $value ) );
	}

	/**
	 * Set contact's currency_code.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_currency_code( $value ) {
		if ( eaccounting_get_currency( $value ) ) {
			$this->set_prop( 'currency_code', eaccounting_clean( $value ) );
		}
	}

	/**
	 * Set contact type.
	 *
	 * @param $type
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_type( $type ) {
		if ( array_key_exists( $type, eaccounting_get_contact_types() ) ) {
			$this->set_prop( 'type', $type );
		}
	}

	/**
	 * Set avatar id
	 *
	 * @param int $thumbnail_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_thumbnail_id( $thumbnail_id ) {
		$this->set_prop( 'thumbnail_id', absint( $thumbnail_id ) );
	}

	/**
	 * Set object status.
	 *
	 * @param int $enabled
	 *
	 * @since 1.0.2
	 *
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
	 *
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
	 * @param string
	 *
	 * @since 1.0.2
	 *
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
}
