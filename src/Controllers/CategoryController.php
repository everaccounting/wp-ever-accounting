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
use EverAccounting\Abstracts\Transactions;
use EverAccounting\Models\Category;
use EverAccounting\Repositories\Accounts;
use EverAccounting\Repositories\Categories;
use EverAccounting\Repositories\Currencies;

defined( 'ABSPATH' ) || exit;

/**
 * Class CategoryController
 *
 * @since   1.1.1
 *
 * @package EverAccounting\Controllers
 */
class CategoryController extends Singleton {
	/**
	 * CategoryController constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_pre_save_category', array( __CLASS__, 'validate_category_data' ), 10, 3 );
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
	public static function validate_category_data( $data, $id, $category ) {
		global $wpdb;

		if ( empty( $data['name'] ) ) {
			$category->error( 'empty_prop', __( 'Category name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['type'] ) ) {
			$category->error( 'empty_prop', __( 'Category type is required.', 'wp-ever-accounting' ) );
		}

		if ( $category->get_id() != (int) $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_categories WHERE type=%s AND name='%s'", $category->get_type(), $category->get_name() ) ) ) { // @codingStandardsIgnoreLine
			$category->error( 'duplicate_item', __( 'Duplicate category.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Delete category id from transactions.
	 *
	 * @since 1.0.2
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

		return $wpdb->update( Transactions::instance()->get_table(), array( 'category_id' => '' ), array( 'category_id', absint( $id ) ) );
	}

}
