<?php

namespace EverAccounting\Admin;

use EverAccounting\Singleton;

defined( 'ABSPATH' ) || exit;


/**
 * Class Actions
 *
 * @since 1.1.6
 * @package EverAccounting
 */
class Actions extends Singleton {

	/**
	 * Actions constructor.
	 *
	 * @since 1.1.6
	 */
	protected function __construct() {
		// AJAX actions.
		add_action( 'wp_ajax_eac_json_search', array( __CLASS__, 'json_search' ) );
		add_action( 'wp_ajax_eac_edit_customer', array( __CLASS__, 'edit_customer' ) );
		add_action( 'wp_ajax_eac_edit_vendor', array( __CLASS__, 'edit_vendor' ) );
		add_action( 'wp_ajax_eac_edit_account', array( __CLASS__, 'edit_account' ) );
		add_action( 'wp_ajax_eac_edit_item', array( __CLASS__, 'edit_item' ) );
		add_action( 'wp_ajax_eac_edit_category', array( __CLASS__, 'edit_category' ) );
		add_action( 'wp_ajax_eac_edit_currency', array( __CLASS__, 'edit_currency' ) );
		add_action( 'wp_ajax_eac_edit_payment', array( __CLASS__, 'edit_payment' ) );
		add_action( 'wp_ajax_eac_edit_expense', array( __CLASS__, 'edit_expense' ) );
		add_action( 'wp_ajax_eac_edit_transfer', array( __CLASS__, 'edit_transfer' ) );
	}

	/**
	 * Json search.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function json_search() {
		check_ajax_referer( 'eac_json_search' );
		$type    = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$term    = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';
		$limit   = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 20;
		$page    = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$results = array();
		$total   = 0;

		$args = array(
			'limit'  => $limit,
			'offset' => ( $page - 1 ) * $limit,
			'status' => 'active',
			'search' => $term,
		);

		switch ( $type ) {
			case 'account':
				$accounts = eac_get_accounts( $args );
				$total    = eac_get_accounts( $args, true );
				foreach ( $accounts as $account ) {
					$results[] = array(
						'id'   => $account->get_id(),
						'text' => $account->get_formatted_name(),
					);
				}
				break;
			case 'item':
				$items = eac_get_items( $args );
				$total = eac_get_items( $args, true );
				foreach ( $items as $item ) {
					$results[] = array(
						'id'   => $item->get_id(),
						'text' => $item->get_formatted_name(),
					);
				}
				break;
			case 'category':
				$type         = isset( $_POST['subtype'] ) ? sanitize_text_field( wp_unslash( $_POST['subtype'] ) ) : 'item';
				$args['type'] = $type;
				$categories   = eac_get_categories( $args );
				$total        = eac_get_categories( $args, true );
				foreach ( $categories as $category ) {
					$results[] = array(
						'id'   => $category->get_id(),
						'text' => $category->get_formatted_name(),
					);
				}
				break;

			case 'currency':
				$currencies = eac_get_currencies( $args );
				$total      = eac_get_currencies( $args, true );
				foreach ( $currencies as $currency ) {
					$results[] = array(
						'id'   => $currency->get_code(),
						'text' => $currency->get_formatted_name(),
					);
				}
				break;

			case 'payment':
				$payments = eac_get_payments( $args );
				$total    = eac_get_payments( $args, true );
				foreach ( $payments as $payment ) {
					$results[] = array(
						'id'   => $payment->get_id(),
						'text' => $payment->get_amount(),
					);
				}
				break;

			case 'expense':
				$expenses = eac_get_expenses( $args );
				$total    = eac_get_expenses( $args, true );
				foreach ( $expenses as $expense ) {
					$results[] = array(
						'id'   => $expense->get_id(),
						'text' => $expense->get_amount(),
					);
				}
				break;

			case 'customer':
				$customers = eac_get_customers( $args );
				$total     = eac_get_customers( $args, true );
				foreach ( $customers as $customer ) {
					$results[] = array(
						'id'   => $customer->get_id(),
						'text' => $customer->get_formatted_name(),
					);
				}
				break;

			case 'vendor':
				$vendors = eac_get_vendors( $args );
				$total   = eac_get_vendors( $args, true );
				foreach ( $vendors as $vendor ) {
					$results[] = array(
						'id'   => $vendor->get_id(),
						'text' => $vendor->get_formatted_name(),
					);
				}
				break;
			case 'invoice':
				$invoices = eac_get_invoices( $args );
				$total    = eac_get_invoices( $args, true );
				foreach ( $invoices as $invoice ) {
					$results[] = array(
						'id'   => $invoice->get_id(),
						'text' => $invoice->get_invoice_number(),
					);
				}
				break;
			case 'bill':
				$bills = eac_get_bills( $args );
				$total = eac_get_bills( $args, true );
				foreach ( $bills as $bill ) {
					$results[] = array(
						'id'   => $bill->get_id(),
						'text' => $bill->get_bill_number(),
					);
				}
				break;
			default:
				$filtered = apply_filters(
					'ever_accounting_json_search',
					array(
						'results' => $results,
						'total'   => $total,
					),
					$type,
					$term,
					$limit,
					$page
				);

				$results = $filtered['results'];
				$total   = $filtered['total'];
				break;
		}

		wp_send_json(
			array(
				'results'    => $results,
				'total'      => $total,
				'page'       => $page,
				'pagination' => array(
					'more' => ( $page * $limit ) < $total,
				),
			)
		);
	}

	/**
	 * Edit customer.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function edit_customer() {
		check_admin_referer( 'eac_edit_customer' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$created = eac_insert_customer( $posted );
		$referer = wp_get_referer();
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Customer updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Customer created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		exit();
	}

	/**
	 * Edit account.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function edit_account() {
		check_admin_referer( 'eac_edit_account' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$created = eac_insert_account( $posted );
		$referer = wp_get_referer();
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Account updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Account created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		exit();
	}

	/**
	 * Edit item.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function edit_item() {
		check_admin_referer( 'eac_edit_item' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$created = eac_insert_item( $posted );
		$referer = wp_get_referer();
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Item updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Item created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		exit();
	}

	/**
	 * Edit category.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function edit_category() {
		check_admin_referer( 'eac-edit-category' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$created = eac_insert_category( $posted );
		$referer = wp_get_referer();
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Category updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Category created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		exit();
	}

	/**
	 * Edit currency.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function edit_currency() {
		check_admin_referer( 'eac_edit_currency' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$created = eac_insert_currency( $posted );
		$referer = wp_get_referer();
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Currency updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Currency created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		exit();
	}

	/**
	 * Edit Payment
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function edit_payment() {
		check_admin_referer( 'eac_edit_payment' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$created = eac_insert_payment( $posted );
		$referer = wp_get_referer();
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Payment updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Payment created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		exit();
	}

	/**
	 * Edit Expense
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function edit_expense() {
		check_admin_referer( 'eac_edit_expense' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$created = eac_insert_expense( $posted );
		$referer = wp_get_referer();
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Expense updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Expense created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		exit();
	}

	/**
	 * Edit vendor.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function edit_vendor() {
		check_admin_referer( 'eac_edit_vendor' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$created = eac_insert_vendor( $posted );
		$referer = wp_get_referer();
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Vendor updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Vendor created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		exit();
	}

	/**
	 * Edit transfer.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function edit_transfer() {
		check_admin_referer( 'eac_edit_transfer' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$created = eac_insert_transfer( $posted );
		$referer = wp_get_referer();
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Transfer updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Transfer created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		exit();
	}
}
