<?php
/**
 * Payment Controller
 *
 * Handles payments's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       PaymentController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;


defined( 'ABSPATH' ) || exit;

/**
 * Class PaymentController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class ExpenseController extends Singleton {

	/**
	 * PaymentController constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_validate_payment_data', array( __CLASS__, 'validate_payment_data' ), 10, 2 );
	}

	/**
	 * Validate payment data.
	 *
	 * @since 1.1.0
	 * 
	 * @param array $data
	 * @param null $id
	 * @param \WP_Error $errors
	 * 
	 * @throws \Exception
	 */
	public static function validate_payment_data( $data, $id = null ) {

		$category = eaccounting_get_category( $data['category_id'] );
		if ( empty( $category ) || ! in_array( $category->get_type(), array( 'expense', 'other' ), true ) ) {
			throw new \Exception(  __( 'A valid income category is required.', 'wp-ever-accounting' ) );
		}

		$customer = eaccounting_get_customer( $data['contact_id'] );
		if ( ! empty( $data['contact_id'] ) && empty( $customer ) ) {
			throw new \Exception(  __( 'Customer is not valid.', 'wp-ever-accounting' ) );
		}
	}

}
