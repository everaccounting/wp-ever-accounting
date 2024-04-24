<?php

namespace EverAccounting\Models;

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
 */
class Contact extends Model {

	/**
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $meta_type = 'ea_contact';

	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $table = 'ea_contacts';

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
	 * Model's data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'status' => 'active',
	);

	/**
	 * Model's casts data.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected $casts = array(
		'id'           => 'int',
		'vat_exempt'   => 'bool',
		'user_id'      => 'int',
		'thumbnail_id' => 'int',
		'author_id'    => 'int',
	);

	/**
	 * Model's data that aren't mass assignable.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $guarded = array(
		'type',
	);

	/**
	 * The properties that should be hidden from array/json.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $hidden = array(
		'type',
	);

	/**
	 * Searchable properties.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name'
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $timestamps = true;

	/**
	 * Create a new Eloquent model instance.
	 *
	 * @param string|int|array $data Data properties.
	 *
	 * @throws \InvalidArgumentException If table name or object type is not set.
	 * @return void
	 */
	public function __construct( $data = null ) {
		$this->data['type']       = $this->get_object_type();
		$this->query_args['type'] = $this->get_object_type();
		parent::__construct( $data );
	}

	/**
	 * Load the object from the database.
	 *
	 * @param string|int $id ID of the object.
	 *
	 * @since 1.0.0
	 * @return $this
	 */
	protected function load( $id ) {
		parent::load( $id );
		if ( $this->get_object_type() !== $this->data['type'] ) {
			$this->apply_defaults();
		}

		return $this;
	}

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Name is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->currency_code ) ) {
			$this->set_prop_value( 'currency_code', eac_get_base_currency() );
		}

		return parent::save();
	}
}
