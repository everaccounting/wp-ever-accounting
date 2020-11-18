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
		add_filter( 'eaccounting_prepare_transfer_data', array( __CLASS__, 'prepare_transfer_data' ), 10, 2 );
		add_action( 'eaccounting_validate_transfer_data', array( __CLASS__, 'validate_transfer_data' ), 10, 3 );
	}

	/**
	 * Prepare transfer data before inserting into database.
	 *
	 * @since 1.1.0
	 *
	 * @param int   $id
	 * @param array $data
	 *
	 * @return array
	 */
	public static function prepare_transfer_data( $data, $id = null ) {
		if ( empty( $data['date_created'] ) ) {
			$data['date_created'] = current_time( 'mysql' );
		}
		if ( empty( $data['creator_id'] ) ) {
			$data['creator_id'] = eaccounting_get_current_user_id();
		}

		return eaccounting_clean( $data );
	}

	/**
	 * Validate transfer data.
	 *
	 * @since 1.1.0
	 *
	 * @param array     $data
	 * @param null      $id
	 * @param \WP_Error $errors
	 */
	public static function validate_transfer_data( $errors, $data, $id = null ) {
		if ( empty( $data['date'] ) ) {
			$errors->add( 'empty_prop', __( 'Transfer date is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['payment_method'] ) ) {
			$errors->add( 'empty_prop', __( 'Transfer method is required.', 'wp-ever-accounting' ) );
		}

		$from_account = eaccounting_get_account( $data['from_account_id'] );
		if ( empty( $from_account ) ) {
			$errors->add( 'empty_prop', __( 'Transfer from account is required.', 'wp-ever-accounting' ) );
		}

		$to_account = eaccounting_get_account( $data['to_account_id'] );
		if ( empty( $to_account ) ) {
			$errors->add( 'empty_prop', __( 'Transfer to account is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( eaccounting_sanitize_number( $data['amount'] ) ) ) {
			$errors->add( 'empty_prop', __( 'Transfer amount is required.', 'wp-ever-accounting' ) );
		}

		$transfer_category_id = Categories::instance()->get_var(
			'id',
			array(
				'name' => __( 'Transfer', 'wp-ever-accounting' ),
				'type' => 'other',
			)
		);

		if ( empty( $transfer_category_id ) ) {
			$transfer_category    = eaccounting_insert_category(
				array(
					'name' => __( 'Transfer', 'wp-ever-accounting' ),
					'type' => 'other',
				)
			);
			$transfer_category_id = ! is_wp_error( $transfer_category ) && $transfer_category->exists() ? $transfer_category->get_id() : null;
		}

		if ( empty( $transfer_category_id ) ) {
			$errors->add( 'empty_prop', __( 'Could not find Transfer category.', 'wp-ever-accounting' ) );
		}

		if ( $from_account && ! eaccounting_get_currency( $from_account->get_currency_code() ) ) {
			$errors->add( 'empty_prop', __( 'Currency of transfer from account is not available.', 'wp-ever-accounting' ) );
		}

		if ( $to_account && ! eaccounting_get_currency( $to_account->get_currency_code() ) ) {
			$errors->add( 'empty_prop', __( 'Currency of transfer to account is not available.', 'wp-ever-accounting' ) );
		}

		return $errors;
	}

}
