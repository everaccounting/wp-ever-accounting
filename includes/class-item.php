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
	 * Item data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'name'           => '',
		'sku'            => '',
		'thumbnail_id'   => null,
		'description'    => '',
		'sale_price'     => 0.0000,
		'purchase_price' => 0.0000,
		'quantity'       => 1,
		'category_id'    => null,
		'sales_tax'      => null,
		'purchase_tax'   => null,
		'enabled'        => 1,
		'creator_id'     => null,
		'date_created'   => null,
	);

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

		$item = new Item;
		$item->set_props( $_item );
		$item->object_read = true;

		return $item;
	}

	/**
	 * Item constructor.
	 *
	 * @param array|object $item Item Object or array data
	 *
	 * @since 1.2.1
	 */
	public function __construct( $item = null ) {
		parent::__construct();

	}

}
