<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Contact
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 *
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
		'user_id',
		'name',
		'company',
		'email',
		'phone',
		'birth_date',
		'street',
		'city',
		'state',
		'postcode',
		'country',
		'website',
		'vat_number',
		'currency_code',
		'type',
		'thumbnail_id',
		'enabled',
		'creator_id',
		'date_created',
	);

	/**
	 * Model's data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'enabled' => 1,
	);

	/**
	 * Model's casts data.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected $casts = array(
		'id'           => 'int',
		'user_id'      => 'int',
		'thumbnail_id' => 'int',
		'enabled'      => 'bool',
		'creator_id'   => 'int',
		'date_created' => 'datetime',
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
