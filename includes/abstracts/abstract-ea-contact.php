<?php
/**
 * Handle the core contact object
 *
 * @class       EAccounting_Contact
 * @version     1.0.0
 * @package     EverAccounting/Classes
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Contact
 * @since 1.0.2
 */
class EAccounting_Contact extends EAccounting_Object {
	/**
	 * Contact Data array.
	 *
	 * @since 1.0.0
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
		'currency_code' => 'USD',
		'type'          => '',
		'note'          => '',
		'creator_id'    => '',
		'company_id'    => '',
		'date_created'  => '',
	);

	/**
	 * EAccounting_Contact constructor.
	 *
	 * @param mixed $data
	 */
	public function __construct( $data = 0 ) {
//		parent::__construct( $data );

//		if ( is_numeric( $data ) && $data > 0 ) {
//			$this->set_id( $data );
//		} elseif ( $data instanceof self ) {
//			$this->set_id( $data->get_id() );
//		} elseif ( ! empty( $data->id ) ) {
//			$this->set_id( $data->id );
//		} else {
//			$this->set_id( 0 );
//		}
//
//		if ( $this->get_id() > 0 ) {
//			$this->read( $this->get_id() );
//		}
	}

	/**
	 * Load contact from database.
	 *
	 * @param int $id
	 *
	 * @throws Exception
	 */
	public function read( $id ) {
		$this->set_defaults();
		global $wpdb;

		// Get from cache if available.
		$item = 0 < $this->get_id() ? wp_cache_get( 'contact-item-' . $this->get_id(), 'contacts' ) : false;

		if ( false === $item ) {
			$item = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_contacts WHERE id = %d;", $this->get_id() )
			);

			if ( 0 < $item->id ) {
				wp_cache_set( 'contact-item-' . $item->id, $item, 'contacts' );
			}
		}

		if ( ! $item || ! $item->id ) {
			throw new Exception( __( 'Invalid contact.', 'wp-ever-accounting' ) );
		}

		// Gets extra data associated with the order if needed.
		foreach ( $item as $key => $value ) {
			$function = 'set_' . $key;
			if ( is_callable( array( $this, $function ) ) ) {
				$this->{$function}( $value );
			} else {
				$this->set_prop( $key, $value );
			}
		}

		$this->set_object_read( true );
	}

	/**
	 * Validate the properties before saving the object
	 * in the database.
	 * @return void
	 * @throws Exception
	 * @since 1.0.2
	 */
	protected function validate_props() {
		global $wpdb;

		if ( ! $this->get_date_created( 'edit' ) ) {
			$this->set_date_created( time() );
		}

		if ( ! $this->get_company_id( 'edit' ) ) {
			$this->set_company_id( 1 );
		}

//		if ( ! $this->get_prop( 'creator_id' ) ) {
//			$this->set_prop( 'creator_id', eaccounting_get_current_user_id() );
//		}
//
//		if ( ! $this->get_currency_code( 'edit' ) ) {
//			$this->set_prop( 'currency_code', eaccounting_get_currency_code() );
//		}

//		if ( empty( $this->get_name( 'edit' ) ) ) {
//			throw new Exception( __( 'Name is required', 'wp-ever-accounting' ) );
//		}
//
//		if ( empty( $this->get_type( 'edit' ) ) ) {
//			throw new Exception( __( 'Type is required', 'wp-ever-accounting' ) );
//		}
//
//		if ( $this->get_user_id( 'edit' ) != null && ! get_user_by( 'ID', $this->get_user_id( 'edit' ) ) ) {
//			throw new Exception( __( 'Invalid WP User ID', 'wp-ever-accounting' ) );
//		}

//		if ( $existing_id = $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_contacts where email=%s AND type=%s AND company_id=%d",
//			$this->get_prop( 'email' ),
//			$this->get_prop( 'type' ),
//			$this->get_prop( 'company_id' ) ) ) ) {
//			if ( ! empty( $existing_id ) && $existing_id != $this->get_id() ) {
//				throw new Exception( __( 'The email address is already in used.', 'wp-ever-accounting' ) );
//			}
//		}

	}


	/**
	 * Create a new account in the database.
	 *
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function create() {
		$this->validate_props();
		global $wpdb;

		$contact_data = array(
			'user_id'       => null,
			'name'          => $this->get_name( 'edit' ),
			'email'         => $this->get_email( 'edit' ),
			'phone'         => $this->get_phone( 'edit' ),
			'fax'           => $this->get_fax( 'edit' ),
			'birth_date'    => ! empty( $this->get_birth_date( 'edit' ) ) ? $this->get_birth_date( 'edit' )->get_mysql_date() : null,
			'address'       => $this->get_address( 'edit' ),
			'country'       => $this->get_country( 'edit' ),
			'website'       => $this->get_website( 'edit' ),
			'tax_number'    => $this->get_tax_number( 'edit' ),
			'currency_code' => $this->get_currency_code( 'edit' ),
			'type'          => $this->get_type( 'edit' ),
			'note'          => $this->get_note( 'edit' ),
			'file_id'       => '',
			'company_id'    => $this->get_company_id( 'edit' ),
			'creator_id'    => $this->get_prop( 'creator_id' ),
			'date_created'  => $this->get_date_created( 'edit' )->get_mysql_date(),
		);

		do_action( 'eaccounting_pre_insert_contact', $contact_data, $this );

		$data = wp_unslash( apply_filters( 'eaccounting_new_contact_data', $contact_data ) );
		var_dump($data);
		var_dump($wpdb->insert( $wpdb->prefix . 'ea_contacts', $data ));


//		if ( ! $wpdb->insert( $wpdb->prefix . 'ea_contacts', $data ) ) {
//			throw new Exception( $wpdb->last_error );
//		}
//
//		$this->set_id( $wpdb->insert_id );
//
//		do_action( 'eaccounting_insert_contact', $this->get_id(), $this );
//
//		$this->apply_changes();
//		$this->set_object_read( true );
	}

	/**
	 * Update a contact in the database.
	 *
	 * @throws Exception
	 * @since 1.0.0
	 *
	 */
	public function update() {
		global $wpdb;

		$this->validate_props();
		$changes = $this->get_changes();

		do_action( 'eaccounting_pre_update_contact', $this->get_id(), $changes );

		try {
			$wpdb->update( $wpdb->prefix . 'ea_contacts', $changes, array( 'id' => $this->get_id() ) );
		} catch ( Exception $e ) {
			throw new Exception( __( 'Could not update contact.', 'wp-ever-accounting' ) );
		}

		do_action( 'eaccounting_update_contact', $this->get_id(), $changes, $this->data );

		$this->apply_changes();
		$this->set_object_read( true );
		wp_cache_delete( 'contact-item-' . $this->get_id(), 'contacts' );
	}

	/**
	 * Conditionally save contact in the database
	 * if exist then update otherwise create.
	 *
	 * @return int|mixed
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function save() {
		if ( $this->get_id() ) {
			$this->update();
		} else {
			$this->create();
		}

		return $this->get_id();
	}


	/**
	 * Remove an contact from the database.
	 *
	 * @param array $args
	 *
	 * @since 1.0.
	 */
	public function delete( $args = array() ) {
		if ( $this->get_id() ) {
			global $wpdb;
			do_action( 'eaccounting_pre_delete_contact', $this->get_id() );
			$wpdb->delete( $wpdb->prefix . 'ea_contacts', array( 'id' => $this->get_id() ) );
			do_action( 'eaccounting_delete_contact', $this->get_id() );
			$this->set_id( 0 );
		}
	}

	/**
	 * Return this customer's avatar.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_avatar_url() {
		return get_avatar_url( $this->get_email() );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get contact's wp user ID.
	 *
	 * @param string $context
	 *
	 * @return int|null
	 * @since 1.0.2
	 *
	 */
	public function get_user_id( $context = 'view' ) {
		return $this->get_prop( 'user_id', $context );
	}

	/**
	 * Get contact Name.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get contact's email.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_email( $context = 'view' ) {
		return $this->get_prop( 'email', $context );
	}

	/**
	 * Get contact's phone number.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_phone( $context = 'view' ) {
		return $this->get_prop( 'phone', $context );
	}

	/**
	 * Get contact's phone number.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_fax( $context = 'view' ) {
		return $this->get_prop( 'fax', $context );
	}

	/**
	 * Get contact's birth date.
	 *
	 * @param string $context
	 *
	 * @return EAccounting_DateTime|string
	 * @since 1.0.2
	 *
	 */
	public function get_birth_date( $context = 'view' ) {
		return $this->get_prop( 'birth_date', $context );
	}

	/**
	 * Get contact's address.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_address( $context = 'view' ) {
		return $this->get_prop( 'address', $context );
	}

	/**
	 * Get contact's country.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_country( $context = 'view' ) {
		return $this->get_prop( 'country', $context );
	}

	/**
	 * Get contact's website number.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_website( $context = 'view' ) {
		return $this->get_prop( 'website', $context );
	}

	/**
	 * Get contact's tax number.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_tax_number( $context = 'view' ) {
		return $this->get_prop( 'tax_number', $context );
	}

	/**
	 * Get the currency code of the contact.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_currency_code( $context = 'view' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Get the type of contact.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_type( $context = 'view' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Get contact's note.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_note( $context = 'view' ) {
		return $this->get_prop( 'note', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set contact's email.
	 *
	 * @param string $value Email.
	 *
	 * @since 1.0.2
	 */
	public function set_email( $value ) {
		if ( $value && ! is_email( $value ) ) {
			$this->error( 'contact_invalid_email', __( 'Invalid email address', 'wp-ever-accounting' ) );
		}
		$this->set_prop( 'email', sanitize_email( $value ) );
	}

	/**
	 * Set contact's website.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_website( $value ) {
		$this->set_prop( 'website', esc_url( $value ) );
	}

	/**
	 * Set contact's birth date.
	 *
	 * @param $date
	 *
	 * @since 1.0.2
	 */
	public function set_birth_date( $date ) {
		$this->set_date_prop( 'birth_date', $date );
	}

	/**
	 * Set contact type.
	 *
	 * @param $type
	 *
	 * @since 1.0.2
	 */
	public function set_type( $type ) {
		if ( array_key_exists( $type, eaccounting_get_contact_types() ) ) {
			$this->set_prop( 'type', $type );
		}
	}
}
