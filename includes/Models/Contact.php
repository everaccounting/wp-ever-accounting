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
 * @property string $address_1 Address line 1 of the contact.
 * @property string $address_2 Address line 2 of the contact.
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
 * @property int    $author_id Author ID of the contact.
 * @property string $uuid UUID of the contact.
 * @property string $date_created Date created of the contact.
 * @property string $date_updated Date updated of the contact.
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
		'address_1',
		'address_2',
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
		'author_id',
		'uuid',
	);

	/**
	 * Data properties of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'status' => 'active',
	);

	/**
	 * The properties that should be cast to native types.
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
		'author_id'    => 'int',
	);

	/**
	 * The properties those are guarded against mass assignment.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $guarded = array(
		'type',
	);

	/**
	 * The properties that should be appended to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'formatted_name',
		'country_name',
	);

	/**
	 * The properties that should be hidden for arrays.
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
	protected $timestamps = true;

	/**
	 * The properties that should be searchable when querying.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'company',
		'email',
		'phone',
		'address_1',
		'address_2',
	);

	/*
	|--------------------------------------------------------------------------
	| Prop Definition Methods
	|--------------------------------------------------------------------------
	| This section contains methods that define and provide specific prop values
	| related to the model, such as statuses or types. These methods can be accessed
	| without instantiating the model.
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
	| Accessors, Mutators, Relationship and Validation Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting properties (accessors
	| and mutators) as well as defining relationships between models. It also includes
	| a data validation method that ensures data integrity before saving.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get country name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_country_name_prop() {
		$countries = I18n::get_countries();
		return isset( $countries[ $this->country ] ) ? $countries[ $this->country ] : $this->country;
	}

	/**
	 * Get formatted name.
	 *
	 * @return string
	 * @since 1.1.6
	 */
	public function get_formatted_name_prop() {
		$company = $this->company ? ' (' . $this->company . ')' : '';
		return $this->name . $company;
	}

	/**
	 * Sanitize data before saving.
	 *
	 * @since 1.0.0
	 * @return void|\WP_Error Return WP_Error if data is not valid or void.
	 */
	public function validate_save_data() {
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Name is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->currency_code ) ) {
			$this->set_prop_value( 'currency_code', eac_get_base_currency() );
		}

		if ( empty( $this->uuid ) ) {
			$this->uuid = wp_generate_uuid4();
		}

		if ( empty( $this->author_id ) && is_user_logged_in() ) {
			$this->author_id = get_current_user_id();
		}
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
