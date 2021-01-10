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
		add_action( 'eaccounting_delete_item', array( __CLASS__, 'update_invoice_item' ) );
		add_action( 'eaccounting_delete_item', array( __CLASS__, 'update_bill_item' ) );
	}

	/**
	 * Delete item id from invoice items.
	 *
	 * @since 1.0.2
	 * 
	 * @param $id
	 *
	 * @return bool
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
	 * @since 1.0.2
	 * 
	 * @param $id
	 *
	 * @return bool
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
