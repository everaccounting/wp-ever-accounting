<?php
/**
 * Revenue Controller
 *
 * Handles expense's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       RevenueController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;

defined( 'ABSPATH' ) || exit;

/**
 * Class RevenueController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class InvoiceController extends Singleton {

	/**
	 * RevenueController constructor.
	 */
	public function __construct() {
		add_filter( 'eaccounting_prepare_invoice_data', array( __CLASS__, 'validate_invoice_data' ), 10, 2 );
		add_filter( 'eaccounting_prepare_invoice_item_data', array( __CLASS__, 'validate_invoice_item_data' ), 10, 2 );
		add_filter( 'eaccounting_prepare_invoice_history_data', array( __CLASS__, 'validate_invoice_history_data' ), 10, 2 );
	}

	/**
	 * Validate invoice data.
	 *
	 * @since 1.1.0
	 *
	 * @param array     $data
	 * @param null      $id
	 */
	public static function validate_expense_data( $data, $id = null ) {

	}

	/**
	 * Validate invoice data.
	 *
	 * @since 1.1.0
	 *
	 * @param array     $data
	 * @param null      $id
	 */
	public static function validate_invoice_item_data( $data, $id = null ) {

	}

	/**
	 * Validate invoice data.
	 *
	 * @since 1.1.0
	 *
	 * @param array     $data
	 * @param null      $id
	 */
	public static function validate_invoice_history_data( $data, $id = null ) {

	}

}
