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

namespace EverAccounting\Admin;
use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit();

class Bills {
	/**
	 * Bills constructor.
	 */
	public function __construct() {
		add_action( 'admin_post_eaccounting_bill_action', array( $this, 'bill_action' ) );
		add_action( 'eaccounting_expenses_page_tab_bills', array( $this, 'render_tab' ), 20 );
	}

	public function bill_action() {
		$action  = eaccounting_clean( wp_unslash( $_REQUEST['bill_action'] ) );
		$bill_id = absint( wp_unslash( $_REQUEST['bill_id'] ) );
		$bill    = eaccounting_get_bill( $bill_id );

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'ea_bill_action' ) || ! current_user_can( 'ea_manage_bill' ) || ! $bill->exists() ) {
			wp_die( __( 'no cheating!', 'wp-ever-accounting' ) );
		}
		$redirect_url = add_query_arg(
			array(
				'page'    => 'ea-expenses',
				'tab'     => 'bills',
				'action'  => 'view',
				'bill_id' => $bill_id,
			),
			admin_url( 'admin.php' )
		);
		switch ( $action ) {
			case 'status_received':
				try {
					$bill->set_status( 'received' );
					$bill->save();
					eaccounting_admin_notices()->add_success( __( 'Bill status updated to received.', 'wp-ever-accounting' ) );
				} catch ( \Exception $e ) {
					/* translators: %s reason */
					eaccounting_admin_notices()->add_error( sprintf( __( 'Bill status was not changes : %s ', 'wp-ever-accounting' ), $e->getMessage() ) );
				}
				break;
			case 'status_cancelled':
				$bill->set_cancelled();
				break;
			case 'status_paid':
				$bill->set_paid();
				break;
			case 'delete':
				$bill->delete();
				$redirect_url = remove_query_arg( array( 'action', 'bill_id' ), $redirect_url );
				break;
		}

		if ( ! did_action( 'eaccounting_bill_action_' . sanitize_title( $action ) ) ) {
			do_action( 'eaccounting_bill_action_' . sanitize_title( $action ), $bill, $redirect_url );
		}

		wp_redirect( $redirect_url ); //phpcs:ignore
		exit();
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
			$this->edit_bill( $bill_id );
		} else {
			include dirname( __FILE__ ) . '/views/bills/list-bill.php';
		}
	}

	/**
	 * View bill.
	 *
	 * @param $bill_id
	 *
	 * @since 1.1.0
	 *
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
			'bills/view-bill',
			array(
				'bill'   => $bill,
				'action' => 'view',
			)
		);
	}

	/**
	 * @param $bill_id
	 *
	 * @param $bill_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function edit_bill( $bill_id = null ) {
		try {
			$bill = new Bill( $bill_id );
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
		eaccounting_get_admin_template(
			'bills/edit-bill',
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
	 *
	 * @param Bill $bill
	 *
	 * @since 1.1.0
	 *
	 */
	public static function bill_notes( $bill ) {
		if ( ! $bill->exists() ) {
			return;
		}
		eaccounting_get_admin_template( 'bills/bill-notes', array( 'bill' => $bill ) );
	}

	/**
	 * Get bill payments.
	 *
	 * @param Bill $bill
	 *
	 * @since 1.1.0
	 *
	 */
	public static function bill_payments( $bill ) {
		if ( ! $bill->exists() ) {
			return;
		}
		eaccounting_get_admin_template( 'bills/bill-payments', array( 'bill' => $bill ) );
	}
}

return new EverAccounting_Admin_Bills();
