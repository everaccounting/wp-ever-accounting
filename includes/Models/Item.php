<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\BelongsTo;
use ByteKit\Models\Relations\BelongsToMany;

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
 * @property string   $created_at Date created of the item.
 * * @property string $updated_at Date updated of the item.
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
	 * The table columns of the model.
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
	 * The properties that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'price'        => 'double',
		'cost'         => 'double',
		'taxable'      => 'bool',
		'tax_ids'      => 'id_list',
		'category_id'  => 'int',
		'thumbnail_id' => 'int',
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
	 * Indicates if the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;

	/**
	 * The properties that are searchable.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'description',
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
	 * Get item types.
	 *
	 * @return array
	 * @since 1.1.6
	 */
	public static function get_types() {
		return apply_filters(
			'ever_accounting_item_types',
			array(
				'standard' => __( 'Standard Item', 'wp-ever-accounting' ),
				'shipping' => __( 'Shipping Fee', 'wp-ever-accounting' ),
				'fee'      => __( 'Fee Item', 'wp-ever-accounting' ),
			)
		);
	}

	/**
	 * Get item units
	 *
	 * @return array
	 * @since 1.1.6
	 */
	public static function get_units() {
		return apply_filters(
			'ever_accounting_units',
			array(
				'box'   => __( 'Box', 'wp-ever-accounting' ),
				'cm'    => __( 'Centimeter', 'wp-ever-accounting' ),
				'day'   => __( 'Day', 'wp-ever-accounting' ),
				'doz'   => __( 'Dozen', 'wp-ever-accounting' ),
				'ft'    => __( 'Feet', 'wp-ever-accounting' ),
				'gm'    => __( 'Gram', 'wp-ever-accounting' ),
				'hr'    => __( 'Hour', 'wp-ever-accounting' ),
				'inch'  => __( 'Inch', 'wp-ever-accounting' ),
				'kg'    => __( 'Kilogram', 'wp-ever-accounting' ),
				'km'    => __( 'Kilometer', 'wp-ever-accounting' ),
				'l'     => __( 'Liter', 'wp-ever-accounting' ),
				'lb'    => __( 'Pound', 'wp-ever-accounting' ),
				'm'     => __( 'Meter', 'wp-ever-accounting' ),
				'mg'    => __( 'Milligram', 'wp-ever-accounting' ),
				'mile'  => __( 'Mile', 'wp-ever-accounting' ),
				'min'   => __( 'Minute', 'wp-ever-accounting' ),
				'mm'    => __( 'Millimeter', 'wp-ever-accounting' ),
				'month' => __( 'Month', 'wp-ever-accounting' ),
				'oz'    => __( 'Ounce', 'wp-ever-accounting' ),
				'pc'    => __( 'Piece', 'wp-ever-accounting' ),
				'sec'   => __( 'Second', 'wp-ever-accounting' ),
				'unit'  => __( 'Unit', 'wp-ever-accounting' ),
				'week'  => __( 'Week', 'wp-ever-accounting' ),
				'year'  => __( 'Year', 'wp-ever-accounting' ),
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
	 * Cast item type.
	 *
	 * @param string $value Item type.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_item_type_attribute( $value ) {
		$this->attributes['type'] = array_key_exists( $value, self::get_types() ) ? $value : 'standard';
	}

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
	 * Get items category
	 *
	 * @since 1.2.1
	 * @return BelongsTo
	 */
	public function category() {
		return $this->belongs_to( Category::class );
	}

	/**
	 * Get tax rates.
	 *
	 * @since 1.2.1
	 * @return BelongsToMany
	 */
	public function taxes() {
		return $this->belongs_to_many( Tax::class );
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
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get items tax
	 *
	 * @since 1.2.1
	 * @return Tax[]
	 */
	// public function get_taxes() {
	// if ( ! $this->exists() || empty( $this->tax_ids ) ) {
	// return array();
	// }
	//
	// return Tax::query(
	// array(
	// 'include' => $this->tax_ids,
	// )
	// );
	// }
}
