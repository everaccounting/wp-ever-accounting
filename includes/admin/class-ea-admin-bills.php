<?php
/**
 * Admin Bill Page
 *
 * Functions used for displaying bill related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.10
 */

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Bills {
	/**
	 * EAccounting_Admin_Bill constructor.
	 */
	public function __construct() {
		add_action( 'admin_post_eaccounting_bill_action', array( $this, 'bill_action' ) );
		add_action( 'eaccounting_expenses_page_tab_bills', array( $this, 'render_tab' ), 20 );
	}

	public function bill_action() {
		$action  = eaccounting_clean( wp_unslash( $_POST['bill_action'] ) );
		$bill_id = absint( wp_unslash( $_POST['bill_id'] ) );
		$bill    = eaccounting_get_bill( $bill_id );

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'ea_bill_action' ) || ! current_user_can( 'ea_manage_bill' ) || ! $bill->exists() ) {
			wp_die( 'no cheatin!' );
		}

		switch ( $action ) {
			case 'status_received':
				$bill->update_status( 'received' );
				break;
			case 'status_overdue':
				$bill->update_status( 'overdue' );
				$bill->save();
				break;
			case 'status_cancelled':
				$bill->update_status( 'cancelled' );
				$bill->save();
				break;
		}

		if ( ! did_action( 'eaccounting_bill_action_' . sanitize_title( $action ) ) ) {
			do_action( 'eaccounting_bill_action_' . sanitize_title( $action ), $bill );
		}

		wp_redirect( add_query_arg( array( 'action' => 'view' ), eaccounting_clean( $_REQUEST['_wp_http_referer'] ) ) );
	}

	/**
	 *
	 * @since 1.1.0
	 */
	public function render_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['bill_id'] ) ) {
			$bill_id = isset( $_GET['bill_id'] ) ? absint( $_GET['bill_id'] ) : null;
			$this->view_bill( $bill_id );
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$bill_id = isset( $_GET['bill_id'] ) ? absint( $_GET['bill_id'] ) : null;
			$this->fill_form( $bill_id );
		} else {
			include dirname( __FILE__ ) . '/views/bills/bills.php';
		}
	}

	/**
	 * View bill.
	 *
	 * @since 1.1.0
	 *
	 * @param $bill_id
	 */
	public function view_bill( $bill_id = null ) {
		try {
			$bill = new Bill( $bill_id );
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}

		if ( empty( $bill ) || ! $bill->exists() ) {
			wp_die( __( 'Sorry, Bill does not exist', 'wp-ever-accounting' ) );
		}

		eaccounting_get_admin_template(
			'bills/bill',
			array(
				'bill'   => $bill,
				'action' => 'view',
			)
		);
	}

	/**
	 * @param $bill_id
	 * @since 1.1.0
	 */
	public function fill_form( $bill_id = null ) {
		try {
			$bill = new Bill( $bill_id );
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
		eaccounting_get_admin_template(
			'bills/bill-form',
			array(
				'bill'   => $bill,
				'action' => 'edit',
			)
		);
	}

	/**
	 * Get bill notes.
	 *
	 * @param Bill $bill
	 * @since 1.1.0
	 */
	public static function bill_notes( $bill ) {
		if ( ! $bill->exists() ) {
			return;
		}
		eaccounting_get_admin_template( 'bills/bill-notes', array( 'bill' => $bill ) );
	}
}

return new EAccounting_Admin_Bills();
