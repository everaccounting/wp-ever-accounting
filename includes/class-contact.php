<?php
/**
 * Contact data handler class.
 *
 * @version     1.0.2
 * @package     EverAccounting
 * @class       Contact
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Contact class.
 */
class Contact extends Data {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'contact';

	/**
	 * Table name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	protected $table = 'ea_contacts';

	/**
	 * Meta type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $meta_type = 'contactmeta';

	/**
	 * Cache group.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $cache_group = 'ea_contacts';


	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.1.3
	 * @var array
	 */
	protected $core_data = [
		'user_id'       => null,
		'name'          => '',
		'company'       => '',
		'email'         => '',
		'phone'         => '',
		'website'       => '',
		'birth_date'    => '',
		'vat_number'    => '',
		'street'        => '',
		'city'          => '',
		'state'         => '',
		'postcode'      => '',
		'country'       => '',
		'currency_code' => '',
		'type'          => 'contact',
		'thumbnail_id'  => null,
		'enabled'       => 1,
		'creator_id'    => null,
		'date_created'  => null,
	];

	/**
	 * Metadata for this object.
	 *
	 * @since 1.1.3
	 * @var array
	 */
	protected $meta_data = array();

	/**
	 * Contact constructor.
	 *
	 * @param int|contact|object|null $contact contact instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $contact = 0 ) {
		// Call early so default data is set.
		parent::__construct();

		if ( is_numeric( $contact ) && $contact > 0 ) {
			$this->set_id( $contact );
		} elseif ( $contact instanceof self ) {
			$this->set_id( absint( $contact->get_id() ) );
		} elseif ( ! empty( $contact->ID ) ) {
			$this->set_id( absint( $contact->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->read();
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
	 *  Create an item in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	protected function create() {
		global $wpdb;

		$data = wp_unslash( $this->get_core_data() );

		/**
		 * Fires immediately before a contact is inserted in the database.
		 *
		 * @param array $data Contact data to be inserted.
		 * @param string $data_arr Sanitized contact data.
		 * @param Contact $contact Contact object.
		 *
		 * @since 1.1.3
		 */
		do_action( 'eaccounting_pre_insert_' . $this->object_type, $data, $this->get_data(), $this );

		if ( false === $wpdb->insert( $wpdb->prefix . $this->table, $data, array() ) ) {
			return new \WP_Error( 'db_insert_error', __( 'Could not insert contact into the database.', 'text-domain' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );
		$this->update_meta_data();
		$this->apply_changes();

		return $this->exists();
	}

	/**
	 * Retrieve the object from database instance.
	 *
	 * @since 1.1.3
	 *
	 * @return object|false Object, false otherwise.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	protected function read() {
		global $wpdb;
		$this->set_defaults();
		// Bail early if no id is set.
		if ( ! $this->get_id() ) {
			return false;
		}

		$data = wp_cache_get( $this->get_id(), $this->cache_group );
		if ( false === $data ) {
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}{$this->table} WHERE id = %d LIMIT 1;", $this->get_id() ) ); // WPCS: cache ok, DB call ok.
			wp_cache_add( $this->get_id(), $data, $this->cache_group );
		}

		if ( ! $data ) {
			$this->set_id( 0 );

			return false;
		}

		$this->set_props( $data );
		$this->read_meta_data();
		$this->set_object_read( true );
		do_action( 'eaccounting_read_' . $this->object_type . '_item', $this->get_id(), $this );

		return $data;
	}

	/**
	 *  Update an object in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @since 1.1.3
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	protected function update() {
		global $wpdb;
		$changes = $this->get_changes();

		// Bail if nothing to save
		if ( empty( $changes ) ) {
			return true;
		}

		/**
		 * Fires immediately before an existing contact is updated in the database.
		 *
		 * @param int $id Contact id.
		 * @param array $data Contact data.
		 * @param array $changes The data will be updated.
		 * @param Contact $contact Contact object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_pre_update_' . $this->object_type, $this->get_id(), $this->get_data(), $changes, $this );


		$this->date_updated = current_time( 'mysql' );
		$data               = wp_unslash( $this->get_core_data() );
		if ( false === $wpdb->update( $wpdb->prefix . $this->table, $data, [ 'id' => $this->get_id() ], array(), [ 'id' => '%d' ] ) ) {
			return new \WP_Error( 'db_update_error', __( 'Could not update contact in the database.', 'text-domain' ), $wpdb->last_error );
		}

		$this->update_meta_data();

		/**
		 * Fires immediately after an existing contact is updated in the database.
		 *
		 * @param int $id Contact id.
		 * @param array $data Contact data.
		 * @param array $changes The data will be updated.
		 * @param Contact $contact Contact object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_update_' . $this->object_type, $this->get_id(), $this->get_data(), $changes, $this );


		return true;
	}

	/**
	 * Deletes the object from database.
	 *
	 * @param array $args Array of args to pass to the delete method.
	 *
	 * @since 1.1.3
	 * @return array|false true on success, false on failure.
	 */
	public function delete( $args = array() ) {
		if ( ! $this->exists() ) {
			return false;
		}
		$data = $this->get_data();

		/**
		 * Filters whether a contact delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $id Contact id.
		 * @param array $data Contact data array.
		 * @param Contact $contact Contact object.
		 *
		 * @since 1.0.0
		 */
		$check = apply_filters( 'eaccounting_check_delete_' . $this->object_type, null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before a contact is deleted.
		 *
		 * @param int $id Contact id.
		 * @param array $data Contact data array.
		 * @param Contact $contact Contact object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_pre_delete_' . $this->object_type, $this->get_id(), $data, $this );

		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . $this->table,
			array(
				'id' => $this->get_id(),
			),
			array( '%d' )
		);

		/**
		 * Fires after a contact is deleted.
		 *
		 * @param int $id Contact id.
		 * @param array $data Contact data array.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_delete_' . $this->object_type, $this->get_id(), $data );

		wp_cache_delete( $this->get_id(), $this->cache_group );
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );
		$this->set_defaults();

		return $data;
	}

	/**
	 * Saves an object in the database.
	 *
	 * @since 1.1.3
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 */
	public function save() {
		// check if anything missing before save.
		if ( ! $this->is_date_valid( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_param', esc_html__( 'Contact name is required', 'text-domain' ) );
		}
		if ( empty( $this->currency_code ) ) {
			return new \WP_Error( 'missing_param', esc_html__( 'Currency code is required', 'text-domain' ) );
		}

		if ( ! $this->exists() ) {
			$is_error = $this->create();
		} else {
			$is_error = $this->update();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), $this->cache_group );
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );

		/**
		 * Fires immediately after a contact is inserted or updated in the database.
		 *
		 * @param int $id Contact id.
		 * @param array $data Contact data array.
		 * @param Contact $contact Contact object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_saved_' . $this->object_type, $this->get_id(), $this );

		return $this->get_id();
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting item data. These should not update anything in the
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
	protected function set_user_id( $id ) {
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
	protected function set_name( $name ) {
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
	protected function set_company( $company ) {
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
	protected function set_email( $value ) {
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
	protected function set_phone( $value ) {
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
	protected function set_birth_date( $date ) {
		$this->set_date_prop( 'birth_date', $date );
	}

	/**
	 * Set contact's website.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 *
	 */
	protected function set_website( $value ) {
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
	protected function set_street( $value ) {
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
	protected function set_city( $city ) {
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
	protected function set_state( $state ) {
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
	protected function set_postcode( $postcode ) {
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
	protected function set_country( $country ) {
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
	protected function set_vat_number( $value ) {
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
	protected function set_currency_code( $value ) {
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
	protected function set_type( $type ) {
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
	protected function set_thumbnail_id( $thumbnail_id ) {
		$this->set_prop( 'thumbnail_id', absint( $thumbnail_id ) );
	}
}
