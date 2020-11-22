<?php
/**
 * Transfer Controller
 *
 * Handles transfer's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       TransferController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;
use EverAccounting\Core\Exception;
use EverAccounting\Repositories\Categories;

defined( 'ABSPATH' ) || exit;

/**
 * Class TransferController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class TransferController extends Singleton {

	/**
	 * RevenueController constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_pre_save_transfer', array( __CLASS__, 'validate_transfer_data' ), 10, 2 );
	}

	/**
	 * Validate transfer data.
	 *
	 * @since 1.1.0
	 *
	 * @param array     $data
	 * @param null      $id
	 * @param \WP_Error $errors
	 *
	 * @throws Exception
	 */
	public static function validate_transfer_data( $data, $id = null ) {
		global $wpdb;
		if ( empty( $data['date'] ) ) {
			throw new Exception( 'empty_prop', __( 'Transfer date is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['payment_method'] ) ) {
			throw new Exception( 'empty_prop', __( 'Transfer method is required.', 'wp-ever-accounting' ) );
		}

		$from_account = eaccounting_get_account( $data['from_account_id'] );
		if ( empty( $from_account ) ) {
			throw new Exception( 'empty_prop', __( 'Transfer from account is required.', 'wp-ever-accounting' ) );
		}

		$to_account = eaccounting_get_account( $data['to_account_id'] );
		if ( empty( $to_account ) ) {
			throw new Exception( 'empty_prop', __( 'Transfer to account is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( eaccounting_sanitize_number( $data['amount'] ) ) ) {
			throw new Exception( 'empty_prop', __( 'Transfer amount is required.', 'wp-ever-accounting' ) );
		}
		$category_id = $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_categories WHERE type=%s AND name=%s", 'other', __( 'Transfer', 'wp-ever-accounting' ) ) );
		if ( $category_id ) {
			$transfer_category = eaccounting_insert_category(
				array(
					'name' => __( 'Transfer', 'wp-ever-accounting' ),
					'type' => 'other',
				),
				false
			);

			$category_id = $transfer_category->exists() ? $transfer_category->get_id() : null;
		}

		if ( empty( $category_id ) ) {
			throw new Exception( 'empty_prop', __( 'Could not find Transfer category.', 'wp-ever-accounting' ) );
		}

		if ( $from_account && ! eaccounting_get_currency( $from_account->get_currency_code() ) ) {
			throw new Exception( 'empty_prop', __( 'Currency of transfer from account is not available.', 'wp-ever-accounting' ) );
		}

		if ( $to_account && ! eaccounting_get_currency( $to_account->get_currency_code() ) ) {
			throw new Exception( 'empty_prop', __( 'Currency of transfer to account is not available.', 'wp-ever-accounting' ) );
		}
	}

}
