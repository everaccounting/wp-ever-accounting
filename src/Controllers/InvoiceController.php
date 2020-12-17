<?php
/**
 * Invoice Controller
 *
 * Handles invoice's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       InvoiceController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;


defined( 'ABSPATH' ) || exit;

/**
 * Class InvoiceController
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
	 * @param array $data
	 * @param null $id
	 *
	 * @since 1.1.0
	 *
	 */
	public static function validate_invoice_data( $data, $id = null ) {
		global $wpdb;
		if ( empty( $data['invoice_number'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice Number is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['status'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice status is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['invoiced_at'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice date is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['due_date'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice due date is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['currency_code'] ) ) {
			throw new Exception( 'empty_prop', __( 'Currency code is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['currency_rate'] ) ) {
			throw new Exception( 'empty_prop', __( 'Currency rate is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['contact_id'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice contact id is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['contact_name'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice contact name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['category_id'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice category id is required.', 'wp-ever-accounting' ) );
		}

	}

	/**
	 * Validate invoice item data.
	 *
	 * @param array $data
	 * @param null $id
	 *
	 * @since 1.1.0
	 *
	 */
	public static function validate_invoice_item_data( $data, $id = null ) {
		global $wpdb;
		if ( empty( $data['document_id'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice item invoice_id is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['name'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice item name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['quantity'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice item quantity is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['price'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice item price is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['total'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice item total is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['tax'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice item tax is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['tax_id'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice item tax_id is required.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Validate invoice history data.
	 *
	 * @param array $data
	 * @param null $id
	 *
	 * @since 1.1.0
	 *
	 */
	public static function validate_invoice_history_data( $data, $id = null ) {
		global $wpdb;
		if ( empty( $data['invoice_id'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice history invoice_id is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['status'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice history status is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['notify'] ) ) {
			throw new Exception( 'empty_prop', __( 'Invoice history notify is required.', 'wp-ever-accounting' ) );
		}
	}
}
