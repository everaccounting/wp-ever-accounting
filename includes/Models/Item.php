<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relation;

/**
 * Item model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int      $id ID of the item.
 * @property string   $type Type of the item.
 * @property string   $name Name of the item.
 * @property string   $description Description of the item.
 * @property string   $unit Unit of the item.
 * @property double   $price Price of the item.
 * @property double   $cost Cost of the item.
 * @property bool     $taxable Whether the item is taxable.
 * @property array    $tax_ids Tax IDs of the item.
 * @property int      $category_id Category ID of the item.
 * @property int      $thumbnail_id Thumbnail ID of the item.
 * @property string   $status Status of the item.
 * @property string   $date_created Date created of the item.
 * * @property string $date_updated Date updated of the item.
 *
 * @property string   $formatted_name Formatted name of the item.
 * @property string   $formatted_price Formatted price of the item.
 * @property string   $formatted_cost Formatted cost of the item.
 * @property Category $category Category of the item.
 * @property Tax[]    $taxes Taxes of the item.
 */
class Item extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_items';

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
		'description',
		'unit',
		'price',
		'cost',
		'taxable',
		'tax_ids',
		'category_id',
		'thumbnail_id',
		'status',
	);

	/**
	 * The model's attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'type'    => 'standard',
		'status'  => 'active',
		'taxable' => false,
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'           => 'int',
		'type'         => array( __CLASS__, 'cast_item_type' ),
		'price'        => 'double',
		'cost'         => 'double',
		'taxable'      => 'bool',
		'category_id'  => 'int',
		'thumbnail_id' => 'int',
		'status'       => array( 'active', 'inactive' ),
	);

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'formatted_name',
		'formatted_price',
		'formatted_cost',
	);

	/**
	 * Searchable attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'description',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;

	/*
	|--------------------------------------------------------------------------
	| Attributes & Relations
	|--------------------------------------------------------------------------
	| Define the attributes and relations of the model.
	*/

	/**
	 * Get formatted name.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	protected function get_formatted_name_attribute() {
		return sprintf( '%s (#%s)', $this->name, $this->id );
	}

	/**
	 * Get formatted sale price.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_price_attribute() {
		return eac_format_amount( $this->price );
	}

	/**
	 * Get formatted purchase price.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_cost_attribute() {
		return eac_format_amount( $this->cost );
	}

	/**
	 * Set item taxes.
	 *
	 * @param array $taxes Tax IDs.
	 *
	 * @since 1.2.1
	 */
	public function set_tax_ids_attribute( $taxes ) {
		$tax_ids = wp_parse_id_list( $taxes );
		$tax_ids = array_filter( array_map( 'intval', array_unique( $tax_ids ) ) );
		$this->set_attribute_value( 'tax_ids', $tax_ids );
	}

	/**
	 * Get items category
	 *
	 * @since 1.2.1
	 * @return Relation
	 */
	protected function category() {
		return $this->belongs_to( Category::class );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for saving, updating, and deleting objects.
	*/

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Item name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->type ) ) {
			return new \WP_Error( 'missing_required', __( 'Item type is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->price ) ) {
			return new \WP_Error( 'missing_required', __( 'Item price is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->cost ) ) {
			$this->cost = $this->price;
		}

		return parent::save();
	}


	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

	/**
	 * Cast item type.
	 *
	 * @param string $value Item type.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function cast_item_type( $value ) {
		return in_array( $value, array_keys( eac_get_item_types() ) ) ? $value : null;
	}

	/**
	 * Get items tax
	 *
	 * @since 1.2.1
	 * @return Tax[]
	 */
	public function get_taxes() {
		if ( ! $this->exists() || empty( $this->tax_ids ) ) {
			return array();
		}

		return Tax::query(
			array(
				'include' => $this->tax_ids,
			)
		);
	}
}