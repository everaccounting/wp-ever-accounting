<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Account;
use EverAccounting\Models\Bill;
use EverAccounting\Models\Category;
use EverAccounting\Models\Currency;
use EverAccounting\Models\Customer;
use EverAccounting\Models\Expense;
use EverAccounting\Models\Invoice;
use EverAccounting\Models\Item;
use EverAccounting\Models\Payment;
use EverAccounting\Models\Tax;
use EverAccounting\Models\Transfer;
use EverAccounting\Models\Vendor;
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
		add_action( 'wp_ajax_eac_get_html_response', array( __CLASS__, 'get_html_response' ) );
		add_action( 'wp_ajax_eac_json_search', array( __CLASS__, 'eac_json_search' ) );
		add_action( 'wp_ajax_eac_get_contact', array( __CLASS__, 'get_contact' ) );
		add_action( 'wp_ajax_eac_get_account', array( __CLASS__, 'get_account' ) );
		add_action( 'wp_ajax_eac_get_currency', array( __CLASS__, 'get_currency' ) );
		add_action( 'wp_ajax_eac_get_item_detail', array( __CLASS__, 'get_item_detail' ) );
		add_action( 'wp_ajax_eac_edit_customer', array( __CLASS__, 'edit_customer' ) );
		add_action( 'wp_ajax_eac_edit_vendor', array( __CLASS__, 'edit_vendor' ) );
		add_action( 'wp_ajax_eac_edit_account', array( __CLASS__, 'edit_account' ) );
		add_action( 'wp_ajax_eac_edit_item', array( __CLASS__, 'edit_item' ) );
		add_action( 'wp_ajax_eac_edit_category', array( __CLASS__, 'edit_category' ) );
		add_action( 'wp_ajax_eac_edit_currency', array( __CLASS__, 'edit_currency' ) );
		add_action( 'wp_ajax_eac_edit_payment', array( __CLASS__, 'edit_payment' ) );
		add_action( 'wp_ajax_eac_edit_expense', array( __CLASS__, 'edit_expense' ) );
		add_action( 'wp_ajax_eac_edit_transfer', array( __CLASS__, 'edit_transfer' ) );
		add_action( 'wp_ajax_eac_edit_tax', array( __CLASS__, 'edit_tax' ) );
		add_action( 'wp_ajax_eac_edit_invoice', array( __CLASS__, 'edit_invoice' ) );
		add_action( 'wp_ajax_eac_calculate_bill_totals', array( __CLASS__, 'calculate_bill_totals' ) );
		add_action( 'wp_ajax_eac_calculate_invoice_totals', array( __CLASS__, 'calculate_invoice_totals' ) );
		add_action( 'admin_post_eac_export_data', array( __CLASS__, 'export_data' ) );

		add_action( 'ever_accounting_before_invoice_actions', array( __CLASS__, 'before_invoice_actions' ), 10, 2 );
	}

	/**
	 * Get modal content.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function get_html_response() {
		check_ajax_referer( 'eac_get_html_response' );
		$id        = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : 0;
		$html_type = isset( $_GET['html_type'] ) ? sanitize_text_field( wp_unslash( $_GET['html_type'] ) ) : '';
		ob_start();
		switch ( $html_type ) {
			case 'edit_item_category':
				$category       = new Category( $id );
				$category->type = 'item';
				require __DIR__ . '/views/categories/category-form.php';
				break;
			case 'edit_payment_category':
				$category       = new Category( $id );
				$category->type = 'payment';
				require __DIR__ . '/views/categories/category-form.php';
				break;
			case 'edit_expense_category':
				$category       = new Category( $id );
				$category->type = 'expense';
				require __DIR__ . '/views/categories/category-form.php';
				break;
			case 'edit_category':
				$category = new Category( $id );
				require __DIR__ . '/views/categories/category-form.php';
				break;
			case 'edit_account':
				$account = new Account( $id );
				require __DIR__ . '/views/accounts/account-form.php';
				break;
			case 'edit_transfer':
				$transfer = new Transfer( $id );
				require __DIR__ . '/views/transfers/transfer-form.php';
				break;
			case 'edit_vendor':
				$vendor = new Vendor( $id );
				require __DIR__ . '/views/vendors/vendor-form.php';
				break;
			case 'edit_customer':
				$customer = new Customer( $id );
				require __DIR__ . '/views/customers/customer-form.php';
				break;
			case 'edit_payment':
				$payment = new Payment( $id );
				require __DIR__ . '/views/payments/payment-form.php';
				break;
			case 'edit_expense':
				$expense = new Expense( $id );
				require __DIR__ . '/views/expenses/expense-form.php';
				break;
			case 'edit_item':
				$item = new Item( $id );
				require __DIR__ . '/views/items/item-form.php';
				break;
			case 'edit_currency':
				$currency = new Currency( $id );
				require __DIR__ . '/views/currencies/currency-form.php';
				break;
			case 'edit_tax':
				$tax = new Tax( $id );
				require __DIR__ . '/views/taxes/tax-form.php';
				break;
			case 'invoice_payment':
				$document = new Invoice( $id );
				require __DIR__ . '/views/invoices/invoice-payment.php';
				break;
			case 'send_invoice':
				$document = new Invoice( $id );
				require __DIR__ . '/views/invoices/send-invoice.php';
				break;
			default:
				do_action( 'eac_get_html_response', $html_type );
				break;
		}
		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_die();
	}

	/**
	 * Json search.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function eac_json_search() {
		check_ajax_referer( 'eac_json_search' );
		$type    = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$term    = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';
		$limit   = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 20;
		$page    = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$results = array();
		$total   = 0;

		$args = array(
			'limit'    => $limit,
			'offset'   => ( $page - 1 ) * $limit,
			'status'   => 'active',
			'search'   => $term,
			'no_count' => true,
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
			case 'item_category':
				$args['type'] = 'item';
				$categories   = eac_get_categories( $args );
				$total        = eac_get_categories( $args, true );
				foreach ( $categories as $category ) {
					$results[] = array(
						'id'   => $category->get_id(),
						'text' => $category->get_formatted_name(),
					);
				}
				break;
			case 'payment_category':
				$args['type'] = 'payment';
				$categories   = eac_get_categories( $args );
				$total        = eac_get_categories( $args, true );
				foreach ( $categories as $category ) {
					$results[] = array(
						'id'   => $category->get_id(),
						'text' => $category->get_formatted_name(),
					);
				}
				break;
			case 'expense_category':
				$args['type'] = 'expense';
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
				$currencies = eac_get_currencies();
				$total      = eac_get_currencies();
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
						'text' => $invoice->get_formatted_name(),
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
			case 'tax':
				$tax_rates = eac_get_taxes( $args );
				$total     = eac_get_taxes( $args, true );
				foreach ( $tax_rates as $tax_rate ) {
					$results[] = array(
						'id'   => $tax_rate->get_id(),
						'text' => $tax_rate->get_formatted_name(),
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
	 * Get contact details.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function get_contact() {
		$contact_id = eac_get_input_var( 'contact_id', 0, 'post' );
		$contact    = eac_get_contact( $contact_id );
		if ( ! $contact ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid contact!', 'wp-ever-accounting' ),
				)
			);
		}

		wp_send_json_success(
			$contact->get_data()
		);
	}

	/**
	 * Get account details.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function get_account() {
		check_ajax_referer( 'eac_get_account' );
		$account_id = eac_get_input_var( 'account_id', 0, 'post' );
		$account    = eac_get_account( $account_id );
		if ( ! $account ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid account!', 'wp-ever-accounting' ),
				)
			);
		}

		wp_send_json_success(
			$account->get_data()
		);
	}

	/**
	 * Get currency details.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function get_currency() {
		check_ajax_referer( 'eac_get_currency' );
		$currency_code = eac_get_input_var( 'currency_code', '', 'post' );
		$currency      = eac_get_currency( $currency_code );
		if ( ! $currency ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid currency!', 'wp-ever-accounting' ),
				)
			);
		}

		wp_send_json_success(
			$currency->get_data()
		);
	}

	/**
	 * Get contact details.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function get_item_details() {
		$item_id = eac_get_input_var( 'item_id', 0, 'post' );
		$item    = eac_get_item( $item_id );
		if ( ! $item ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid item!', 'wp-ever-accounting' ),
				)
			);
		}

		wp_send_json_success(
			$item->get_data()
		);
	}

	/**
	 * Edit customer.
	 *
	 * @return void
	 * @since 1.1.6
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
	 * @return void
	 * @since 1.1.6
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
	 * @return void
	 * @since 1.1.6
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
	 * @return void
	 * @since 1.1.6
	 */
	public static function edit_category() {
		check_admin_referer( 'eac_edit_category' );
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
	 * @return void
	 * @since 1.1.6
	 */
	public static function edit_currency() {
		check_admin_referer( 'eac_edit_currency' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$referer = wp_get_referer();
		if ( is_wp_error( eac_insert_currency( $posted ) ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Something went wrong!', 'wp-ever-accounting' ),
				)
			);
		}

		$message  = __( 'Currency updated successfully!', 'wp-ever-accounting' );
		$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
			)
		);

		exit();
	}

	/**
	 * Edit payment
	 *
	 * @return void
	 * @since 1.1.6
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
	 * @return void
	 * @since 1.1.6
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
	 * @return void
	 * @since 1.1.6
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
	 * @return void
	 * @since 1.1.6
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

	/**
	 * Edit tax rate.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function edit_tax() {
		check_admin_referer( 'eac_edit_tax' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$created = eac_insert_tax( $posted );
		$referer = wp_get_referer();
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Tax updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Tax created successfully!', 'wp-ever-accounting' );
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
	 * Edit document.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function edit_invoice() {
		check_ajax_referer( 'eac_edit_invoice' );
		$referer  = wp_get_referer();
		$posted   = eac_clean( wp_unslash( $_POST ) );
		$posted   = wp_parse_args( $posted );
		$document = new Invoice( $posted['id'] );
		$document->set_data( $posted );

		$message  = __( 'Invoice updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Invoice created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );
		}

		$saved = $document->save();
		if ( is_wp_error( $saved ) ) {
			wp_send_json_error(
				array(
					'message' => $saved->get_error_message(),
				)
			);
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $document->get_data(),
			)
		);
	}

	/**
	 * Calculate invoice totals.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function calculate_invoice_totals() {
		check_ajax_referer( 'eac_edit_invoice' );
		$posted = wp_parse_args( $_POST );
		$posted = eac_clean( $posted );
		if ( ! isset( $posted['items'] ) ) {
			$posted['items'] = array();
		}
		foreach ( $posted['items'] as $key => $item ) {
			if ( ! isset( $item['tax_ids'] ) ) {
				$posted['items'][ $key ]['tax_ids'] = '';
			}
		}
		$document = new Invoice( $posted['id'] );
		$document->set_data( $posted );
		$document->calculate_totals();
		include __DIR__ . '/views/invoices/invoice-form.php';
		exit();
	}

	/**
	 * Calculate invoice totals.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function calculate_bill_totals() {
		check_ajax_referer( 'eac_edit_bill' );
		$posted = wp_parse_args( $_POST );
		$posted = eac_clean( $posted );
		if ( ! isset( $posted['items'] ) ) {
			$posted['items'] = array();
		}
		foreach ( $posted['items'] as $key => $item ) {
			if ( ! isset( $item['tax_ids'] ) ) {
				$posted['items'][ $key ]['tax_ids'] = '';
			}
		}
		$document = new Bill( $posted['id'] );
		$document->set_data( $posted );
		$document->calculate_totals();
		include __DIR__ . '/views/bills/bill-form.php';
		exit();
	}

	/**
	 * Export data.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function export_data() {
		check_admin_referer( 'eac_export_data' );
		$posted      = eac_clean( wp_unslash( $_POST ) );
		$export_type = isset( $posted['export_type'] ) ? $posted['export_type'] : '';

		// if export type not set, die.
		if ( empty( $export_type ) ) {
			wp_die( esc_html__( 'Export type not found!', 'wp-ever-accounting' ) );
		}

		switch ( $export_type ) {
			case 'accounts':
				$exporter = new Exporters\Accounts();
				$exporter->process_step( 1 );
				$exporter->export();
				break;
			case 'customers':
				$exporter = new Exporters\Customers();
				$exporter->process_step( 1 );
				$exporter->export();
				break;
		}

		wp_safe_redirect( wp_get_referer() );
		exit();
	}

	/**
	 * Before invoice actions.
	 *
	 * @param int     $invoice_id Invoice ID.
	 * @param Invoice $invoice Invoice object.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function before_invoice_actions( $invoice_id, $invoice ) {
//		if( $invoice->is_status('draft')){
			echo sprintf( '<a href="%s" class="button">%s</a>', esc_url( eac_action_url( array( 'action' => 'mark_sent', 'id' => $invoice_id ), false ) ), esc_html__( 'Mark as Sent', 'wp-ever-accounting' ) );
			echo sprintf( '<a href="%s" class="button button-primary">%s</a>', esc_url( eac_action_url( array( 'action' => 'get_html_response', 'html_type' => 'send_invoice', 'id' => $invoice_id ), true ) ), esc_html__( 'Send Invoice', 'wp-ever-accounting' ) );
//		}
		if ( $invoice->is_status( 'sent' ) && ! $invoice->is_paid() ) {
			echo sprintf( '<a href="%s" class="button">%s</a>', esc_url( eac_action_url( array( 'action' => 'record_payment', 'id' => $invoice_id ), false ) ), esc_html__( 'Record Payment', 'wp-ever-accounting' ) );
		}
	}
}
