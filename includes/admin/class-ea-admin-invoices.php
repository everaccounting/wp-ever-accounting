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

class EAccounting_Admin_Invoices {

	/**
	 * EAccounting_Admin_Invoices constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_sales_page_tab_invoices', array( $this, 'render_tab' ), 20 );
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
	 * @since 1.1.0
	 *
	 * @param $invoice_id
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
	 * @since 1.1.0
	 *
	 * @param Invoice $invoice
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
	 * @since 1.1.0
	 *
	 * @param Invoice $invoice
	 */
	public static function invoice_payments( $invoice ) {
		if ( ! $invoice->exists() ) {
			return;
		}
		eaccounting_get_admin_template( 'invoices/invoice-payments', array( 'invoice' => $invoice ) );
	}
}

return new \EAccounting_Admin_Invoices();
