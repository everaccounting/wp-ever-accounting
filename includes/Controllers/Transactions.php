<?php

namespace EverAccounting\Controllers;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transactions.
 *
 * @since   1.0.0
 * @package EverAccounting\Controllers
 */
class Transactions {
	/**
	 * Transactions constructor.
	 */
	public function __construct() {
		add_action( 'ever_accounting_payment_init', array( __CLASS__, 'setup_payment' ) );
	}

	/**
	 * Initialize payment.
	 */
	public static function setup_payment() {
		$account_id     = get_option( 'eac_default_sales_account_id', 0 );
		$sales_category = get_option( 'eac_default_sales_category_id', 0 );
		$payment_method = get_option( 'eac_default_sales_payment_method', 'cash' );
		$account        = eac_get_account( $account_id );
		$category       = eac_get_category( $sales_category );

		if ( ! empty( $account ) ) {

		}
	}
}
