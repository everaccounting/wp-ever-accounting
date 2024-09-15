<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\BelongsTo;

/**
 * DocumentItemTax model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int               $id ID of the document_item_tax.
 * @property string            $name Name of the document_item_tax.
 * @property double            $rate Rate of the document_item_tax.
 * @property bool              $compound Compound of the document_item_tax.
 * @property double            $amount Amount of the document_item_tax.
 * @property int               $item_id Document item ID of the document_item_tax.
 * @property int               $tax_id Tax ID of the document_item_tax.
 * @property int               $document_id Document ID of the document_item_tax.
 * @property string            $updated_at Date updated of the document_item_tax.
 * @property string            $created_at Date created of the document_item_tax.
 *
 * @property-read string       $formatted_name Formatted name of the document_item_tax.
 * @property-read DocumentItem $item Item relationship.
 * @property-read Tax          $tax Tax relationship.
 * @property-read Document     $document Document relationship.
 */
class DocumentTax extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_document_taxes';

	/**
	 * The table columns of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'name',
		'rate',
		'compound',
		'amount',
		'item_id',
		'tax_id',
		'document_id'
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'          => 'int',
		'rate'        => 'double',
		'compound'    => 'bool',
		'amount'      => 'double',
		'item_id'     => 'int',
		'tax_id'      => 'int',
		'document_id' => 'int',
	);

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'formatted_name',
	);

	/**
	 * Indicates if the model should be timestamped.
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
	 * Get the formatted name of the document_item_tax.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_name() {
		return $this->name . ' (' . $this->rate . '%)';
	}

	/**
	 * Item relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function item() {
		return $this->belongs_to( DocumentItem::class );
	}

	/**
	 * Tax relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function tax() {
		return $this->belongs_to( Tax::class );
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
		if ( empty( $this->rate ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax rate is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->tax_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->document_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Document ID is required.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}
}
