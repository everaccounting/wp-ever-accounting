<?php

namespace EverAccounting\Admin\Sales;

use EverAccounting\Models\Payment;

defined( 'ABSPATH' ) || exit;

/**
 * Class Payments
 *
 * @package EverAccounting\Admin\Sales
 */
class Payments {

	/**
	 * Payments constructor.
	 */
	public function __construct() {
		add_filter( 'eac_sales_page_tabs', array( $this, 'register_tabs' ) );
		add_action( 'load_eac_sales_page_payments', array( __CLASS__, 'setup_table' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'eac_sales_page_payments', array( $this, 'render_table' ) );
		add_action( 'eac_sales_page_payments_add', array( $this, 'render_add' ) );
		add_action( 'eac_sales_page_payments_edit', array( $this, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_payment', array( $this, 'handle_edit' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function register_tabs( $tabs ) {
		$tabs['payments'] = __( 'Payments', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * setup table.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new Tables\PaymentsTable();
		$list_table->prepare_items();
		$screen->add_option(
			'per_page',
			array(
				'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
				'default' => 20,
				'option'  => "eac_{$list_table->_args['plural']}_per_page",
			)
		);
	}

	/**
	 * Set screen option.
	 *
	 * @param mixed  $status Status.
	 * @param string $option Option.
	 * @param mixed  $value Value.
	 *
	 * @since 3.0.0
	 * @return mixed
	 */
	public static function set_screen_option( $status, $option, $value ) {
		global $list_table;
		if ( "eac_{$list_table->_args['plural']}_per_page" === $option ) {
			return $value;
		}

		return $status;
	}


	/**
	 * Render table.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function render_table() {
		global $list_table;
		include __DIR__ . '/views/payments-list.php';
	}

	/**
	 * Render add.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function render_add() {
		$payment = new Payment();
		include __DIR__ . '/views/payment-add.php';
	}

	/**
	 * Render edit.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function render_edit() {
		$id      = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$payment = Payment::find( $id );
		if ( ! $payment ) {
			esc_html_e( 'The specified payment does not exist.', 'wp-ever-accounting' );

			return;
		}

		include __DIR__ . '/views/payment-edit.php';
	}

	/**
	 * Handle edit.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function handle_edit() {
		check_admin_referer( 'eac_edit_payment' );
		$referer = wp_get_referer();
		$data    = array(
			'id'             => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
			'date'           => isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '',
			'account_id'     => isset( $_POST['account_id'] ) ? absint( wp_unslash( $_POST['account_id'] ) ) : 0,
			'amount'         => isset( $_POST['amount'] ) ? floatval( wp_unslash( $_POST['amount'] ) ) : 0,
			'currency'       => isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : eac_base_currency(),
			'exchange_rate'  => isset( $_POST['exchange_rate'] ) ? floatval( wp_unslash( $_POST['exchange_rate'] ) ) : 1,
			'category_id'    => isset( $_POST['category_id'] ) ? absint( wp_unslash( $_POST['category_id'] ) ) : 0,
			'contact_id'     => isset( $_POST['contact_id'] ) ? absint( wp_unslash( $_POST['contact_id'] ) ) : 0,
			'attachment_id'  => isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0,
			'payment_method' => isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : '',
			'invoice_id'     => isset( $_POST['invoice_id'] ) ? absint( wp_unslash( $_POST['invoice_id'] ) ) : 0,
			'reference'      => isset( $_POST['reference'] ) ? sanitize_text_field( wp_unslash( $_POST['reference'] ) ) : '',
			'note'           => isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '',
			'status'         => isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active',
		);
		$payment = EAC()->payments->insert( $data );
		if ( is_wp_error( $payment ) ) {
			EAC()->flash->error( $payment->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Payment saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg(
				array(
					'action' => 'edit',
					'id'     => $payment->id,
				),
				$referer
			);
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}
}
