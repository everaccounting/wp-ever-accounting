<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Expense;

defined( 'ABSPATH' ) || exit;

/**
 * Class Expenses
 *
 * @since 3.0.0
 * @package EverAccounting\Admin\Purchases
 */
class Expenses {

	/**
	 * Expenses constructor.
	 */
	public function __construct() {
		add_filter( 'eac_purchases_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'load_eac_purchases_page_expenses', array( __CLASS__, 'setup_table' ) );
		add_action( 'eac_purchases_page_expenses', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_purchases_page_expenses_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_purchases_page_expenses_edit', array( __CLASS__, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_expense', array( __CLASS__, 'handle_edit' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['expenses'] = __( 'Expenses', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * setup expenses list.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new ListTables\Expenses();
		$list_table->prepare_items();
		$screen->add_option(
			'per_page',
			array(
				'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
				'default' => 20,
				'option'  => 'eac_expenses_per_page',
			)
		);
	}

	/**
	 * Render table.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_table() {
		global $list_table;
		include __DIR__ . '/views/expense-list.php';
	}

	/**
	 * Render add form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_add() {
		$expense = new Expense();
		include __DIR__ . '/views/expense-add.php';
	}

	/**
	 * Render edit expense form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_edit() {
		$id      = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$expense = Expense::find( $id );
		if ( ! $expense ) {
			esc_html_e( 'The specified expense does not exist.', 'wp-ever-accounting' );

			return;
		}
		include __DIR__ . '/views/expense-edit.php';
	}

	/**
	 * Handle edit.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function handle_edit() {
		check_admin_referer( 'eac_edit_expense' );
		$referer = wp_get_referer();
		$data    = array(
			'id'            => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
			'date'          => isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '',
			'account_id'    => isset( $_POST['account_id'] ) ? absint( wp_unslash( $_POST['account_id'] ) ) : 0,
			'amount'        => isset( $_POST['amount'] ) ? floatval( wp_unslash( $_POST['amount'] ) ) : 0,
			'exchange_rate' => isset( $_POST['exchange_rate'] ) ? floatval( wp_unslash( $_POST['exchange_rate'] ) ) : 1,
			'category_id'   => isset( $_POST['category_id'] ) ? absint( wp_unslash( $_POST['category_id'] ) ) : 0,
			'contact_id'    => isset( $_POST['contact_id'] ) ? absint( wp_unslash( $_POST['contact_id'] ) ) : 0,
			'attachment_id' => isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0,
			'mode'          => isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : '',
			'invoice_id'    => isset( $_POST['invoice_id'] ) ? absint( wp_unslash( $_POST['invoice_id'] ) ) : 0,
			'reference'     => isset( $_POST['reference'] ) ? sanitize_text_field( wp_unslash( $_POST['reference'] ) ) : '',
			'note'          => isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '',
			'status'        => isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active',
		);
		$expense = EAC()->expenses->insert( $data );
		if ( is_wp_error( $expense ) ) {
			EAC()->flash->error( $expense->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Expense saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg(
				array(
					'action' => 'edit',
					'id'     => $expense->id,
				),
				$referer
			);
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}
}
