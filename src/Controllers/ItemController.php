<?php
/**
 * Item Controller
 *
 * Handles Item's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       ItemController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;
use EverAccounting\Core\Exception;
use EverAccounting\Models\Item;
use EverAccounting\Models\Tax;

defined( 'ABSPATH' ) || exit;

/**
 * Class ItemController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class ItemController extends Singleton {
	/**
	 * AccountController constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_pre_save_item', array( __CLASS__, 'validate_item_data' ), 10, 2 );
		add_action( 'eaccounting_delete_item', array( __CLASS__, 'update_invoice_item' ) );
		add_action( 'eaccounting_delete_item', array( __CLASS__, 'update_bill_item' ) );
	}

	/**
	 * Validate item data.
	 *
	 * @param array $data
	 * @param int $id
	 * @param Item $data
	 *
	 * @since 1.1.0
	 */
	public static function validate_item_data( $data, $id ) {
		global $wpdb;
		if ( empty( $data['name'] ) ) {
			throw new Exception( 'empty_prop', __( 'Item name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['sale_price'] ) ) {
			throw new Exception( 'empty_prop', __( 'Item Sale Price is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['purchase_price'] ) ) {
			throw new Exception( 'empty_prop', __( 'Item Purchase Price is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['quantity'] ) ) {
			throw new Exception( 'empty_prop', __( 'Item Quantity is required.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Delete item id from invoice items.
	 *
	 * @param $id
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public static function update_invoice_item( $id ) {
		global $wpdb;
		$id = absint( $id );
		if ( empty( $id ) ) {
			return false;
		}

		return $wpdb->update( $wpdb->prefix . 'ea_invoice_items', array( 'item_id' => '' ), array( 'item_id' => absint( $id ) ) );
	}

	/**
	 * Delete tax id from bill items.
	 *
	 * @param $id
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public static function update_bill_item( $id ) {
		global $wpdb;
		$id = absint( $id );
		if ( empty( $id ) ) {
			return false;
		}

		return $wpdb->update( $wpdb->prefix . 'ea_bill_items', array( 'item_id' => '' ), array( 'item_id' => absint( $id ) ) );
	}

}
