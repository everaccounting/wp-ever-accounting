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
 * @property int $id ID of the item.
 * @property string $name Name of the item.
 * @property string $sku SKU of the item.
 * @property int $thumbnail_id Thumbnail ID of the item.
 * @property string $description Description of the item.
 * @property double $sale_price Sale price of the item.
 * @property double $purchase_price Purchase price of the item.
 * @property int $quantity Quantity of the item.
 * @property int $category_id Category ID of the item.
 * @property double $sales_tax Sales tax of the item.
 * @property double $purchase_tax Purchase tax of the item.
 * @property bool $enabled Whether the item is enabled or not.
 * @property int $creator_id Creator ID of the item.
 * @property string $date_created Date created of the item.
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
		'name',
		'sku',
		'thumbnail_id',
		'description',
		'sale_price',
		'purchase_price',
		'quantity',
		'category_id',
		'sales_tax',
		'purchase_tax',
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
	protected $attributes = array(
		'sale_price'     => 0.0000,
		'purchase_price' => 0.0000,
		'quantity'       => 1,
		'enabled'        => true,
	);

	/**
	 * Model's casts data.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	protected $casts = array(
		'id'             => 'int',
		'sale_price'     => 'double',
		'purchase_price' => 'double',
		'quantity'       => 'int',
		'category_id'    => 'int',
		'enabled'        => 'bool',
		'creator_id'     => 'int',
		'date_created'   => 'datetime',
	);

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'formatted_sale_price',
		'formatted_purchase_price',
	);

	/**
	 * Searchable properties.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'sku',
		'description',
	);

	/**
	 * Get formatted sale price.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_sale_price_attribute() {
		return eac_format_money( $this->sale_price );
	}

	/**
	 * Get formatted purchase price.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_purchase_price_attribute() {
		return eac_format_money( $this->purchase_price );
	}

	/**
	 * Get items category
	 *
	 * @return Relation
	 * @since 1.2.1
	 */
	public function category() {
		return $this->has_one( Category::class, 'category_id', 'id' );
	}

	/**
	 * Get items categories
	 *
	 * @param array $args Query arguments.
	 *
	 * @return mixed
	 * @since 1.2.1
	 */
	public function categories( $args = array() ) {
		$args['type']  = 'item';
		$args['limit'] = - 1;

		return eac_get_categories( $args );
	}

	/**
	 * Save the object to the database.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @since 1.0.0
	 */
	public function save() {
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Item name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->quantity ) ) {
			return new \WP_Error( 'missing_required', __( 'Item quantity is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->sale_price ) ) {
			return new \WP_Error( 'missing_required', __( 'Item sale price is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}
		if ( empty( $this->purchase_price ) ) {
			$this->purchase_price = $this->sale_price;
		}
		if ( empty( $this->creator_id ) && get_current_user_id() ) {
			$this->creator_id = get_current_user_id();
		}

		return parent::save();
	}
}
