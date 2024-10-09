<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\BelongsTo;
use ByteKit\Models\Relations\HasMany;

/**
 * DocumentItem model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int           $id ID of the item.
 * @property int           $document_id Document ID of the item.
 * @property int           $item_id Item ID of the item.
 * @property string        $type Type of the item.
 * @property string        $name Name of the item.
 * @property string        $description Description of the item.
 * @property string        $unit Unit of the item.
 * @property double        $price Price of the item.
 * @property double        $quantity Quantity of the item.
 * @property double        $subtotal Subtotal of the item.
 * @property double        $discount Discount of the item.
 * @property double        $tax Tax total of the item.
 * @property double        $total Total of the item.
 * @property string        $updated_at Date updated of the item.
 * @property string        $created_at Date created of the item.
 *
 * @property double        $discounted_subtotal Discounted subtotal of the document_item.
 * @property DocumentTax[] $taxes Taxes of the document_item.
 */
class DocumentItem extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_document_items';

	/**
	 * The table columns of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'document_id',
		'item_id',
		'type',
		'name',
		'description',
		'unit',
		'price',
		'quantity',
		'subtotal',
		'discount',
		'tax',
		'total',
	);

	/**
	 * The attributes of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'type'     => 'standard',
		'quantity' => 1,
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'          => 'int',
		'item_id'     => 'int',
		'document_id' => 'int',
		'price'       => 'double',
		'quantity'    => 'double',
		'subtotal'    => 'double',
		'discount'    => 'double',
		'tax'         => 'double',
		'total'       => 'double',
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

	/**
	 * Tax relationship.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function taxes() {
		return $this->has_many( DocumentTax::class, 'document_item_id' );
	}

	/**
	 * Item relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function item() {
		return $this->belongs_to( Item::class );
	}

	/**
	 * Document relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function document() {
		return $this->belongs_to( Document::class );
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
		if ( empty( $this->type ) ) {
			return new \WP_Error( 'required_missing', __( 'Product type is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->quantity ) ) {
			return new \WP_Error( 'required_missing', __( 'Product quantity is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->document_id ) ) {
			return new \WP_Error( 'required_missing', __( 'Document ID is required.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}

	/**
	 * Delete the object from the database.
	 *
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function delete() {
		$return = parent::delete();
		if ( $return ) {
			$this->taxes()->delete();
		}

		return $return;
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
