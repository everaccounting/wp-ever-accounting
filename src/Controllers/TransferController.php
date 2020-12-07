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
		if ( empty( $data['date'] ) ) {
			throw new Exception( 'empty_prop', __( 'Transfer date is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['payment_method'] ) ) {
			throw new Exception( 'empty_prop', __( 'Transfer method is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['from_account_id'] )) {
			throw new Exception( 'invalid_account', __( 'From account is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['from_account_id'] )) {
			throw new Exception( 'invalid_account', __( 'To account is required', 'wp-ever-accounting' ) );
		}
	}

}
