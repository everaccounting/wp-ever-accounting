<?php

namespace EverAccounting\Admin;

use EverAccounting\Admin\ListTables\PaymentsTable;
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
		add_filter( 'eac_sales_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'load_eac_sales_page_payments_home', array( __CLASS__, 'setup_table' ) );
		add_action( 'eac_sales_page_payments_home', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_sales_page_payments_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_sales_page_payments_edit', array( __CLASS__, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_payment', array( __CLASS__, 'handle_edit' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['payments'] = __( 'Payments', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * setup payments list.
	 *
	 * @since 1.0.0
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new PaymentsTable();
		$list_table->prepare_items();
		$screen->add_option( 'per_page', array(
			'label'   => __( 'Payments', 'wp-ever-accounting' ),
			'default' => 20,
			'option'  => 'eac_payments_per_page',
		) );
	}

	/**
	 * Render payments table.
	 *
	 * @since 1.0.0
	 */
	public static function render_table() {
		global $list_table;
		?>
		<h1 class="wp-heading-inline">
			<?php esc_html_e( 'Payments', 'wp-ever-accounting' ); ?>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-sales&tab=payments&action=add' ) ); ?>" class="button button-small">
				<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
			</a>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-tools' ) ); ?>" class="button button-small">
				<?php esc_html_e( 'Import', 'wp-ever-accounting' ); ?>
			</a>
			<?php if ( $list_table->get_request_search() ) : ?>
				<?php // translators: %s: search query. ?>
				<span class="subtitle"><?php echo esc_html( sprintf( __( 'Search results for "%s"', 'wp-ever-accounting' ), esc_html( $list_table->get_request_search() ) ) ); ?></span>
			<?php endif; ?>
		</h1>

		<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
			<?php $list_table->views(); ?>
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' ); ?>
			<?php $list_table->display(); ?>
			<input type="hidden" name="page" value="eac-sales"/>
			<input type="hidden" name="tab" value="payments"/>
		</form>
		<?php
	}

	/**
	 * Render payments add.
	 *
	 * @since 1.0.0
	 */
	public static function render_add() {
		?>
		<h1 class="wp-heading-inline">
			<?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?>
			<a href="<?php echo esc_attr( remove_query_arg( 'add' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
				<span class="dashicons dashicons-undo"></span>
			</a>
		</h1>
		<?php
		$payment = new Payment();
		include __DIR__ . '/views/form.php';
	}

	/**
	 * Render payments edit.
	 *
	 * @since 1.0.0
	 */
	public static function render_edit() {
		$id      = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$payment = Payment::find( $id );
		if ( ! $payment ) {
			esc_html_e( 'The specified payment does not exist.', 'wp-ever-accounting' );

			return;
		}
		?>
		<h1 class="wp-heading-inline">
			<?php esc_html_e( 'Edit Payment', 'wp-ever-accounting' ); ?>
			<a href="<?php echo esc_attr( remove_query_arg( 'edit' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
				<span class="dashicons dashicons-undo"></span>
			</a>
		</h1>
		<?php
		include __DIR__ . '/views/form.php';
	}

	/**
	 * Handle edit payment.
	 *
	 * @since 1.0.0
	 */
	public static function handle_edit() {
		check_admin_referer( 'eac_edit_payment' );
		$referer = wp_get_referer();
		$data    = array(
			'id'             => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
			'date'           => isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '',
			'account_id'     => isset( $_POST['account_id'] ) ? absint( wp_unslash( $_POST['account_id'] ) ) : 0,
			'amount'         => isset( $_POST['amount'] ) ? floatval( wp_unslash( $_POST['amount'] ) ) : 0,
			'category_id'    => isset( $_POST['category_id'] ) ? absint( wp_unslash( $_POST['category_id'] ) ) : 0,
			'contact_id'     => isset( $_POST['contact_id'] ) ? absint( wp_unslash( $_POST['contact_id'] ) ) : 0,
			'payment_method' => isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : '',
			'invoice_id'     => isset( $_POST['invoice_id'] ) ? absint( wp_unslash( $_POST['invoice_id'] ) ) : 0,
			'reference'      => isset( $_POST['reference'] ) ? sanitize_text_field( wp_unslash( $_POST['reference'] ) ) : '',
			'note'           => isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '',
			'status'         => isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active',
		);
		$payment = eac_insert_payment( $data );
		if ( is_wp_error( $payment ) ) {
			EAC()->flash->error( $payment->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Payment saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $payment->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}
}
