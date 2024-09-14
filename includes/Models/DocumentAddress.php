<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class DocumentAddress
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the document_address.
 * @property int    $document_id Document ID of the document_address.
 * @property string $type Type of the document_address.
 * @property string $name Name of the document_address.
 * @property string $company Company of the document_address.
 * @property string $address Address of the document_address.
 * @property string $city City of the document_address.
 * @property string $state State of the document_address.
 * @property string $zip Zip of the document_address.
 * @property string $country Country of the document_address.
 * @property string $phone Phone of the document_address.
 * @property string $email Email of the document_address.
 * @property string $tax_number Tax number of the document_address.
 */
class DocumentAddress extends Model {
	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_document_addresses';

	/**
	 * The table columns of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'document_id',
		'type',
		'name',
		'company',
		'address',
		'city',
		'state',
		'zip',
		'country',
		'phone',
		'email',
		'tax_number',
	);

	/**
	 * The attributes of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'type' => 'billing',
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'          => 'int',
		'document_id' => 'int',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $has_timestamps = true;

	/*
	|--------------------------------------------------------------------------
	| Property Definition Methods
	|--------------------------------------------------------------------------
	| This section contains static methods that define and return specific
	| property values related to the model.
	| These methods are accessible without creating an instance of the model.
	|--------------------------------------------------------------------------
	*/

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators and Relationship Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting attributes (accessors
	| and mutators) as well as defining relationships between models.
	|--------------------------------------------------------------------------
	*/

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
		if ( ! $this->document_id ) {
			return new \WP_Error( 'required_missing', __( 'The document ID is required.', 'wp-ever-accounting' ) );
		}

		if ( ! $this->type ) {
			return new \WP_Error( 'required_missing', __( 'The address type is required.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}
}
