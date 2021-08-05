<?php
/**
 * Handle the Item object.
 *
 * @package     EverAccounting
 * @class       Item
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Item object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property string $name
 * @property string $sku
 * @property string $description
 * @property float $sale_price
 * @property float $purchase_price
 * @property float $quantity
 * @property int $category_id
 * @property float $sales_tax
 * @property int $purchase_tax
 * @property float $thumbnail_id
 * @property boolean $enabled
 * @property int $creator_id
 * @property string $date_created
 */
class Item extends Data {
	/**
	 * Item id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Name of the item.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $name = '';

	/**
	 * Item SKU
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $sku = '';

	/**
	 * Item description
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $description = '';


	/**
	 * Item sale price.
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $sale_price = 0.00;


	/**
	 * Item purchase price
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $purchase_price = 0.00;

	/**
	 * Item stock quantity
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $quantity = 0;

	/**
	 * Item category id
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $category_id = 0;

	/**
	 * Item sales tax
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $sales_tax = 0.00;

	/**
	 * Item purchase tax
	 *
	 * @since 1.2.1
	 * @var float
	 */
	public $purchase_tax = 0.00;

	/**
	 * Item thumbnail id.
	 *
	 * @since 1.2.1
	 * @var null
	 */
	public $thumbnail_id = null;

	/**
	 * Item status
	 *
	 * @since 1.2.1
	 * @var bool
	 */
	public $enabled = true;

	/**
	 * Item creator user id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $creator_id = 0;

	/**
	 * Item created date.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $date_created = '0000-00-00 00:00:00';


	/**
	 * Retrieve Item instance.
	 *
	 * @param int $item_id Item id.
	 *
	 * @return Item|false Item object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	public static function get_instance( $item_id ) {
		global $wpdb;

		$item_id = (int) $item_id;
		if ( ! $item_id ) {
			return false;
		}

		$_item = wp_cache_get( $item_id, 'ea_items' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_items WHERE id = %d LIMIT 1", $item_id ) );

			if ( ! $_item ) {
				return false;
			}
			$_item = eaccounting_sanitize_item( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_items' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_item( $_item, 'raw' );
		}


		return new self( $_item );
	}

	/**
	 * Item constructor.
	 *
	 * @param array|object $item Item Object or array data
	 *
	 * @since 1.2.1
	 */
	public function __construct( $item ) {
		foreach ( get_object_vars( $item ) as $key => $value ) {
			$this->$key = $value;
		}
	}


	/**
	 * Filter the object based on context.
	 *
	 * @param string $context Filter.
	 *
	 * @return Note|Object
	 * @since 1.2.1
	 */
	public function filter( $context ) {
		if ( $this->context === $context ) {
			return $this;
		}

		if ( 'raw' === $context ) {
			return self::get_instance( $this->id );
		}

		return eaccounting_sanitize_item( $this, $context );
	}
}
