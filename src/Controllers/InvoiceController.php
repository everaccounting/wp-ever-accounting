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
use EverAccounting\Admin\Admin_Notices;
use EverAccounting\Core\Emails;
use EverAccounting\Core\Mailer;

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
		add_action( 'admin_post_eaccounting_invoice_action', array( __CLASS__, 'invoice_action' ) );
	}

	/**
	 * Handle invoice actions.
	 *
	 * @since 1.1.0
	 */
	public static function invoice_action() {
		$action     = eaccounting_clean( wp_unslash( $_POST['invoice_action'] ) );
		$invoice_id = absint( wp_unslash( $_POST['invoice_id'] ) );
		$invoice    = eaccounting_get_invoice( $invoice_id );

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'ea_invoice_action' ) || ! current_user_can( 'ea_manage_invoice' ) || ! $invoice->exists() ) {
			wp_die( 'no cheatin!' );
		}

		switch ( $action ) {
			case 'mark_pending':
				$invoice->set_status( 'pending' );
				$invoice->save();
				break;
			case 'mark_paid':
				$invoice->set_paid();
				break;
			case 'mark_refunded':
				$invoice->set_refunded();
				break;
			case 'mark_overdue':
				$invoice->set_status( 'overdue' );
				$invoice->save();
				break;
			case 'mark_cancelled':
				$invoice->set_status( 'cancelled' );
				$invoice->save();
				break;
			case 'send_customer_invoice':
				Emails::send_customer_invoice( $invoice );
				$invoice->set_status( 'sent' );
				$invoice->save();
				break;
		}

		if ( ! did_action( 'eaccounting_invoice_action_' . sanitize_title( $action ) ) ) {
			do_action( 'eaccounting_invoice_action_' . sanitize_title( $action ), $invoice );
		}

		wp_redirect( add_query_arg( array( 'action' => 'view' ), eaccounting_clean( $_REQUEST['_wp_http_referer'] ) ) );
	}

}
