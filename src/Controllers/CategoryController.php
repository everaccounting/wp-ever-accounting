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
use EverAccounting\Abstracts\TransactionsRepository;
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
		add_filter( 'eaccounting_prepare_category_data', array( __CLASS__, 'prepare_category_data' ), 10, 2 );
		add_action( 'eaccounting_validate_category_data', array( __CLASS__, 'validate_category_data' ), 10, 3 );
		add_action( 'eaccounting_delete_category', array( __CLASS__, 'update_transaction_category' ) );
	}

	/**
	 * Prepare category data before inserting into database.
	 *
	 * @since 1.1.0
	 *
	 * @param int   $id
	 * @param array $data
	 *
	 * @return array
	 */
	public static function prepare_category_data( $data, $id = null ) {
		if ( empty( $data['date_created'] ) ) {
			$data['date_created'] = current_time( 'mysql' );
		}
		if ( empty( $data['color'] ) ) {
			$data['color'] = eaccounting_get_random_color();
		}

		return eaccounting_clean( $data );
	}


	/**
	 * Validate category data.
	 *
	 * @since 1.1.0
	 *
	 * @param array     $data
	 * @param null      $id
	 * @param \WP_Error $errors
	 */
	public static function validate_category_data( $errors, $data, $id = null ) {
		error_log($id);
		if ( empty( $data['name'] ) ) {
			$errors->add( 'empty_prop', __( 'Category name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['type'] ) ) {
			$errors->add( 'empty_prop', __( 'Category type is required.', 'wp-ever-accounting' ) );
		}

		if ( intval( $id ) !== (int) Categories::instance()->get_var(
			'id',
			array(
				'type' => $data['type'],
				'name' => $data['name'],
			)
		) ) {
			$errors->add( 'invalid_prop', __( 'Duplicate category.', 'wp-ever-accounting' ) );
		}

		return $errors;
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

		return $wpdb->update( TransactionsRepository::instance()->get_table(), array( 'category_id' => '' ), array( 'category_id', absint( $id ) ) );
	}

}
