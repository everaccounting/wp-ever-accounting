<?php
/**
 * Handle the customer object.
 *
 * @package     EverAccounting\Models
 * @class       Customer
 * @version     1.0.2
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Core\Repositories;
use EverAccounting\Traits\AttachmentTrait;
use EverAccounting\Traits\ContactTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customer
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Customer extends ResourceModel {
	use AttachmentTrait;
	use ContactTrait;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'customer';

	/**
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $cache_group = 'ea_customers';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data = array(
		'user_id'       => null,
		'name'          => '',
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
		'type'          => 'customer',
		'thumbnail_id'  => null,
		'enabled'       => 1,
		'creator_id'    => null,
		'date_created'  => null,
	);

	/**
	 * Get the account if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Account $data object to read.
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_array( $data ) ) {
			$this->set_props( $data );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( 'customers' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		// If not income then reset to default
		if ( 'customer' !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}

		$this->required_props = array(
			'name'          => __( 'Name', 'wp-ever-accounting' ),
			'currency_code' => __( 'Currency Code', 'wp-ever-accounting' ),
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Functions for getting item data. Getter methods wont change anything unless
	| just returning from the props.
	|
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
	 * Get contact's birth date.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_birth_date( $context = 'edit' ) {
		return $this->get_prop( 'birth_date', $context );
	}

	/**
	 * Get contact's street.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_street( $context = 'edit' ) {
		return $this->get_prop( 'street', $context );
	}

	/**
	 * Get contact's city.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_city( $context = 'edit' ) {
		return $this->get_prop( 'city', $context );
	}

	/**
	 * Get contact's state.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_state( $context = 'edit' ) {
		return $this->get_prop( 'state', $context );
	}

	/**
	 * Get contact's postcode.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_postcode( $context = 'edit' ) {
		return $this->get_prop( 'postcode', $context );
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

		return isset( $countries[ $this->get_country() ] ) ? $countries[ $this->get_country() ] : $this->get_country();
	}

	/**
	 * Get contact's vat number.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_vat_number( $context = 'edit' ) {
		return $this->get_prop( 'vat_number', $context );
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
	 * Get avatar id
	 *
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return int|null
	 */
	public function get_thumbnail_id( $context = 'edit' ) {
		return $this->get_prop( 'thumbnail_id', $context );
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
		if ( $value && is_email( $value ) ) {
			$this->set_prop( 'email', sanitize_email( $value ) );
		}
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
	 * Set contact's street.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_street( $value ) {
		$this->set_prop( 'street', sanitize_text_field( $value ) );
	}

	/**
	 * Set contact's city.
	 *
	 * @since 1.0.2
	 *
	 * @param $city
	 *
	 */
	public function set_city( $city ) {
		$this->set_prop( 'city', sanitize_text_field( $city ) );
	}

	/**
	 * Set contact's state.
	 *
	 * @since 1.0.2
	 *
	 * @param $state
	 *
	 */
	public function set_state( $state ) {
		$this->set_prop( 'state', sanitize_text_field( $state ) );
	}

	/**
	 * Set contact's postcode.
	 *
	 * @since 1.0.2
	 *
	 * @param $postcode
	 *
	 */
	public function set_postcode( $postcode ) {
		$this->set_prop( 'postcode', sanitize_text_field( $postcode ) );
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
	 * Set contact's tax_number.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_vat_number( $value ) {
		$this->set_prop( 'vat_number', eaccounting_clean( $value ) );
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
		if ( eaccounting_get_currency( $value ) ) {
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
	 * Set avatar id
	 *
	 * @since 1.1.0
	 *
	 * @param int $thumbnail_id
	 */
	public function set_thumbnail_id( $thumbnail_id ) {
			$this->set_prop( 'thumbnail_id', absint( $thumbnail_id ) );

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
		if ( ! empty( $this->get_thumbnail_id() ) && $this->get_attachment() ) {
			return $this->get_attachment()->src;
		}

		return get_avatar_url( $this->get_email(), wp_parse_args( $args, array( 'size' => '100' ) ) );
	}

	/**
	 * @since 1.1.0
	 * @return string
	 */
	public function get_default_image_url() {
		return $this->get_avatar_url();
	}
}
