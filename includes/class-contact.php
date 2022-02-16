<?php
/**
 * Contact data handler class.
 *
 * @version     1.0.2
 * @package     Ever_Accounting
 * @class       Contact
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit;

/**
 * Contact class.
 */
class Contact extends Abstracts\Data  {
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
		'phone'         => 'null',
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
//	protected $meta_data = array(
//		'total_paid' => '',
//		'total_due' => ''
//	);

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

	/**
	 * Get contact's country.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_country_nicename( $context = 'edit' ) {
		$countries = eaccounting_get_countries();

		return isset( $countries[ $this->get_prop( 'country' ) ] ) ? $countries[ $this->get_prop( 'country' ) ] : $this->get_prop( 'country' );
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
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.3
	 */
	public function save() {
		// check if anything missing before save.
		if ( ! $this->is_date_valid( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		$requires = [ 'name', 'currency_code', 'type' ];
		foreach ( $requires as $required ) {
			if ( empty( $this->$required ) ) {
				return new \WP_Error( 'missing_required_params', sprintf( __( 'Contact %s is required.', 'wp-ever-accounting' ), $required ) );
			}
		}

		//@todo need to check this
		$duplicate_customer = Contacts::get_customer_by_email( $this->email );

		if ( $duplicate_customer && $duplicate_customer->exists() && $duplicate_customer->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate_customer', __( 'Customer already exists', 'wp-ever-accounting' ) );
		}

		$duplicate_vendor = Contacts::get_vendor_by_email( $this->email );

		if ( $duplicate_vendor && $duplicate_vendor->exists() && $duplicate_vendor->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate_vendor', __( 'Vendor already exists', 'wp-ever-accounting' ) );
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
		do_action( 'ever_accounting_saved_' . $this->object_type, $this->get_id(), $this );

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
	 * Set contact country.
	 *
	 * @param $country
	 *
	 * @since 1.0.2
	 */
	protected function set_country( $country ) {
		if ( array_key_exists( $country, \Ever_Accounting\Helpers\Misc::get_countries() ) ) {
			$this->set_prop( 'country', $country );
		}
	}

	/**
	 * Set contact's currency_code.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	protected function set_currency_code( $value ) {
		if ( Currencies::get( $value ) ) {
			$this->set_prop( 'currency_code', \Ever_Accounting\Helpers\Formatting::clean( $value ) );
		}
	}

	/**
	 * Set contact type.
	 *
	 * @param $type
	 *
	 * @since 1.0.2
	 */
	protected function set_type( $type ) {
		if ( array_key_exists( $type, Contacts::get_types() ) ) {
			$this->set_prop( 'type', $type );
		}
	}

	/**
	 * Set avatar id
	 *
	 * @param int $thumbnail_id
	 *
	 * @since 1.1.0
	 */
	protected function set_thumbnail_id( $thumbnail_id ) {
		$this->set_prop( 'thumbnail_id', absint( $thumbnail_id ) );
	}
}
