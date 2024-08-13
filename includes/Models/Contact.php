<?php

namespace EverAccounting\Models;

use EverAccounting\Utilities\I18n;

defined( 'ABSPATH' ) || exit;

/**
 * Class Contact
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 *
 * @property int    $id ID of the contact.
 * @property string $type Type of the contact.
 * @property string $name Name of the contact.
 * @property string $company Company of the contact.
 * @property string $email Email of the contact.
 * @property string $phone Phone of the contact.
 * @property string $website Website url of the contact.
 * @property string $address Address line of the contact.
 * @property string $city City of the contact.
 * @property string $state State of the contact.
 * @property string $postcode Postcode of the contact.
 * @property string $country Country of the contact.
 * @property string $vat_number VAT number of the contact.
 * @property bool   $vat_exempt VAT exempt status of the contact.
 * @property string $currency_code Currency code of the contact.
 * @property int    $thumbnail_id Thumbnail ID of the contact.
 * @property int    $user_id User ID of the contact.
 * @property string $status Status of the contact.
 * @property string $created_via Created via of the contact.
 * @property int    $creator_id Author ID of the contact.
 * @property string $created_at Date created of the contact.
 * @property string $updated_at Date updated of the contact.
 *
 * @property-read  string $formatted_name Get formatted name.
 * @property-read  string $country_name Get country name.
 */
class Contact extends Model {
	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $table = 'ea_contacts';

	/**
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $meta_type = 'ea_contact';

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'type',
		'name',
		'company',
		'email',
		'phone',
		'website',
		'address',
		'city',
		'state',
		'postcode',
		'country',
		'vat_number',
		'vat_exempt',
		'currency_code',
		'thumbnail_id',
		'user_id',
		'status',
		'created_via',
		'creator_id',
	);

	/**
	 * The model's attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'status' => 'active',
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'           => 'int',
		'email'        => 'sanitize_email',
		'website'      => 'esc_url',
		'vat_exempt'   => 'bool',
		'thumbnail_id' => 'int',
		'user_id'      => 'int',
		'creator_id'    => 'int',
	);

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'formatted_name',
		'country_name',
	);

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $hidden = array(
		'type',
	);

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $has_timestamps = true;

	/**
	 * The attributes that are searchable.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'company',
		'email',
		'phone',
		'address'
	);

	/*
	|--------------------------------------------------------------------------
	| Property Definition Methods
	|--------------------------------------------------------------------------
	| This section contains static methods that define and return specific
	| property values related to the model.
	| These methods are accessible without creating an instance of the model.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get contact types.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public static function get_contact_types() {
		return apply_filters(
			'ever_accounting_contact_types',
			array(
				'customer' => esc_html__( 'Customer', 'wp-ever-accounting' ),
				'vendor'   => esc_html__( 'Vendor', 'wp-ever-accounting' ),
			)
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators and Relationship Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting attributes (accessors
	| and mutators) as well as defining relationships between models.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get country name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_country_name() {
		$countries = I18n::get_countries();
		return isset( $countries[ $this->country ] ) ? $countries[ $this->country ] : $this->country;
	}

	/**
	 * Get formatted name.
	 *
	 * @return string
	 * @since 1.1.6
	 */
	public function get_formatted_name() {
		$company = $this->company ? ' (' . $this->company . ')' : '';
		return $this->name . $company;
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	| This section contains methods for creating, reading, updating, and deleting
	| objects in the database.
	|--------------------------------------------------------------------------
	*/
	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|static WP_Error on failure, or the object on success.
	 */
	public function save() {
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Name is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->currency_code ) ) {
			$this->set( 'currency_code', eac_get_base_currency() );
		}

		if ( empty( $this->creator_id ) && is_user_logged_in() ) {
			$this->creator_id = get_current_user_id();
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/
}
