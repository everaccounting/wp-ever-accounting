<?php
/**
 * Category Controller
 *
 * Handles category's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       CategoryController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;
use EverAccounting\Core\Exception;
use EverAccounting\Models\Category;

defined( 'ABSPATH' ) || exit;

/**
 * Class CategoryController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class CategoryController extends Singleton {
	/**
	 * CategoryController constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_pre_save_category', array( __CLASS__, 'validate_category_data' ), 10, 2 );
		add_action( 'eaccounting_delete_category', array( __CLASS__, 'update_transaction_category' ) );
	}

	/**
	 * Validate category data.
	 *
	 * @since 1.1.0
	 *
	 * @param array    $data
	 * @param null     $id
	 * @param Category $category
	 *
	 * @throws \EverAccounting\Core\Exception
	 */
	public static function validate_category_data( $data, $id ) {
		global $wpdb;

		if ( empty( $data['name'] ) ) {
			throw new Exception( 'empty_prop', __( 'Category name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['type'] ) ) {
			throw new Exception( 'empty_prop', __( 'Category type is required.', 'wp-ever-accounting' ) );
		}

		if ( $id != (int) $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_categories WHERE type=%s AND name='%s'", eaccounting_clean( $data['type'] ), eaccounting_clean( $data['name'] ) ) ) ) { // @codingStandardsIgnoreLine
			throw new Exception( 'duplicate_item', __( 'Duplicate category.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Delete category id from transactions.
	 *
	 * @since 1.1.0
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	function update_transaction_category( $id ) {
		global $wpdb;
		$id = absint( $id );
		if ( empty( $id ) ) {
			return false;
		}

		return $wpdb->update( $wpdb->prefix . 'ea_transactions', array( 'category_id' => '' ), array( 'category_id', absint( $id ) ) );
	}

}
