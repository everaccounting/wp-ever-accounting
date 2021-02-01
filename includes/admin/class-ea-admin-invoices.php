<?php
/**
 * Admin Invoice Page
 *
 * Functions used for displaying invoice related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.10
 */

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit();

class EverAccounting_Admin_Invoices {

	/**
	 * EverAccounting_Admin_Invoices constructor.
	 */
	public function __construct() {
		add_action( 'admin_post_eaccounting_invoice_action', array( $this, 'invoice_action' ) );
		add_action( 'eaccounting_sales_page_tab_invoices', array( $this, 'render_tab' ), 20 );
	}

	public function invoice_action() {
		$action     = eaccounting_clean( wp_unslash( $_REQUEST['invoice_action'] ) );
		$invoice_id = absint( wp_unslash( $_REQUEST['invoice_id'] ) );
		$invoice    = eaccounting_get_invoice( $invoice_id );

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'ea_invoice_action' ) || ! current_user_can( 'ea_manage_invoice' ) || ! $invoice->exists() ) {
			wp_die( __( 'no cheating!', 'wp-ever-accounting' ) );
		}
		$redirect_url = add_query_arg(
			array(
				'page'       => 'ea-sales',
				'tab'        => 'invoices',
				'action'     => 'view',
				'invoice_id' => $invoice_id,
			),
			admin_url( 'admin.php' )
		);
		switch ( $action ) {
			case 'status_pending':
				try {
					$invoice->set_status( 'pending' );
					$invoice->save();
					eaccounting_admin_notices()->add_success( __( 'Invoice status updated to pending.', 'wp-ever-accounting' ) );
				} catch ( Exception $e ) {
					/* translators: %s reason */
					eaccounting_admin_notices()->add_error( sprintf( __( 'Invoice status was not changes : %s ', 'wp-ever-accounting' ), $e->getMessage() ) );
				}
				break;
			case 'status_cancelled':
				$invoice->set_cancelled();
				break;
			case 'status_refunded':
				$invoice->set_refunded();
				break;
			case 'status_paid':
				$invoice->set_paid();
				break;
			case 'delete':
				$invoice->delete();
				$redirect_url = remove_query_arg( array( 'action', 'invoice_id' ), $redirect_url );
				break;
		}

		if ( ! did_action( 'eaccounting_invoice_action_' . sanitize_title( $action ) ) ) {
			do_action( 'eaccounting_invoice_action_' . sanitize_title( $action ), $invoice, $redirect_url );
		}
		wp_redirect( $redirect_url ); //phpcs:ignore
		exit();
	}

	/**
	 * Render tab.
	 *
	 * @since 1.1.0
	 */
	public function render_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['invoice_id'] ) ) {
			$invoice_id = isset( $_GET['invoice_id'] ) ? absint( $_GET['invoice_id'] ) : null;
			$this->view_invoice( $invoice_id );
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$invoice_id = isset( $_GET['invoice_id'] ) ? absint( $_GET['invoice_id'] ) : null;
			$this->edit_invoice( $invoice_id );
		} else {
			include dirname( __FILE__ ) . '/views/invoices/list-invoice.php';
		}
	}


	/**
	 * View invoice.
	 *
	 * @param $invoice_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function view_invoice( $invoice_id = null ) {
		try {
			$invoice = new Invoice( $invoice_id );
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}

		if ( empty( $invoice ) || ! $invoice->exists() ) {
			wp_die( __( 'Sorry, Invoice does not exist', 'wp-ever-accounting' ) );
		}

		eaccounting_get_admin_template(
			'invoices/view-invoice',
			array(
				'invoice' => $invoice,
				'action'  => 'view',
			)
		);
	}

	/**
	 * @param $invoice_id
	 *
	 * @param $invoice_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function edit_invoice( $invoice_id = null ) {
		try {
			$invoice = new Invoice( $invoice_id );
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
		eaccounting_get_admin_template(
			'invoices/edit-invoice',
			array(
				'invoice' => $invoice,
				'action'  => 'edit',
			)
		);
	}

	/**
	 * Get invoice notes.
	 *
	 * @param Invoice $invoice
	 *
	 * @param Invoice $invoice
	 *
	 * @since 1.1.0
	 *
	 */
	public static function invoice_notes( $invoice ) {
		if ( ! $invoice->exists() ) {
			return;
		}
		eaccounting_get_admin_template( 'invoices/invoice-notes', array( 'invoice' => $invoice ) );
	}

	/**
	 * Get invoice payments.
	 *
	 * @param Invoice $invoice
	 *
	 * @since 1.1.0
	 *
	 */
	public static function invoice_payments( $invoice ) {
		if ( ! $invoice->exists() ) {
			return;
		}
		eaccounting_get_admin_template( 'invoices/invoice-payments', array( 'invoice' => $invoice ) );
	}
}

return new \EverAccounting_Admin_Invoices();
