<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Account;
use EverAccounting\Models\Category;
use EverAccounting\Models\Currency;
use EverAccounting\Models\Customer;
use EverAccounting\Models\Expense;
use EverAccounting\Models\Invoice;
use EverAccounting\Models\Product;
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
		add_action( 'wp_ajax_eac_json_search', array( __CLASS__, 'json_search' ) );
		add_action( 'wp_ajax_eac_get_contact_details', array( __CLASS__, 'get_contact_details' ) );
		add_action( 'wp_ajax_eac_get_product_details', array( __CLASS__, 'get_product_details' ) );
		add_action( 'wp_ajax_eac_edit_customer', array( __CLASS__, 'edit_customer' ) );
		add_action( 'wp_ajax_eac_edit_vendor', array( __CLASS__, 'edit_vendor' ) );
		add_action( 'wp_ajax_eac_edit_account', array( __CLASS__, 'edit_account' ) );
		add_action( 'wp_ajax_eac_edit_product', array( __CLASS__, 'edit_product' ) );
		add_action( 'wp_ajax_eac_edit_category', array( __CLASS__, 'edit_category' ) );
		add_action( 'wp_ajax_eac_edit_currency', array( __CLASS__, 'edit_currency' ) );
		add_action( 'wp_ajax_eac_edit_payment', array( __CLASS__, 'edit_payment' ) );
		add_action( 'wp_ajax_eac_edit_expense', array( __CLASS__, 'edit_expense' ) );
		add_action( 'wp_ajax_eac_edit_transfer', array( __CLASS__, 'edit_transfer' ) );
		add_action( 'wp_ajax_eac_edit_tax', array( __CLASS__, 'edit_tax' ) );
		add_action( 'wp_ajax_eac_edit_invoice', array( __CLASS__, 'edit_invoice' ) );
		add_action( 'wp_ajax_eac_calculate_invoice', array( __CLASS__, 'calculate_invoice' ) );
		add_action( 'admin_post_eac_export_data', array( __CLASS__, 'export_data' ) );
	}

	/**
	 * Get modal content.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function get_html_response() {
		check_ajax_referer( 'eac_get_html_response' );
		$id        = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : 0;
		$html_type = isset( $_GET['html_type'] ) ? sanitize_text_field( wp_unslash( $_GET['html_type'] ) ) : '';
		ob_start();
		switch ( $html_type ) {
			case 'edit_category':
				$type     = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'item';
				$category = new Category( $id );
				$category->set_type( $type );
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
			case 'edit_product':
				$product = new Product( $id );
				require __DIR__ . '/views/products/product-form.php';
				break;
			case 'edit_currency':
				$currency = new Currency( $id );
				require __DIR__ . '/views/currencies/currency-form.php';
				break;
			case 'edit_tax':
				$tax = new Tax( $id );
				require __DIR__ . '/views/taxes/tax-form.php';
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
			case 'product':
				$items = eac_get_products( $args );
				$total = eac_get_products( $args, true );
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
	 * @since 1.1.6
	 * @return void
	 */
	public static function get_contact_details() {
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
	 * Get contact details.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public static function get_product_details() {
		$item_id = eac_get_input_var( 'product_id', 0, 'post' );
		$item    = eac_get_product( $item_id );
		if ( ! $item ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid product!', 'wp-ever-accounting' ),
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
	public static function edit_product() {
		check_admin_referer( 'eac_edit_product' );
		$posted  = eac_clean( wp_unslash( $_POST ) );
		$created = eac_insert_product( $posted );
		$referer = wp_get_referer();
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Product updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Product created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eac_clean( $referer ) );
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'product'  => $created->get_data(),
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
		$posted            = eac_clean( wp_unslash( $_POST ) );
		$global_currencies = eac_get_global_currencies();
		$code              = ! empty( $posted['code'] ) ? $posted['code'] : '';
		$name              = ! empty( $posted['name'] ) ? sanitize_text_field( $posted['name'] ) : '';
		$symbol            = ! empty( $posted['symbol'] ) ? sanitize_text_field( $posted['symbol'] ) : '';
		$position          = ! empty( $posted['position'] ) ? sanitize_text_field( $posted['position'] ) : '';
		$precision         = ! empty( $posted['precision'] ) ? absint( $posted['precision'] ) : '';

		if ( empty( $code ) || empty( $global_currencies[ $code ] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid currency code!', 'wp-ever-accounting' ),
				)
			);
		}

		$data = array(
			'code'      => $code,
			'name'      => $name,
			'symbol'    => $symbol,
			'position'  => $position,
			'precision' => $precision,
		);
		$data = wp_parse_args( $data, $global_currencies[ $code ] );


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

	/**
	 * Edit tax rate.
	 *
	 * @since 1.1.6
	 * @return void
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

		$message  = __( 'Tax rate updated successfully!', 'wp-ever-accounting' );
		$update   = ! empty( $posted['id'] );
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Tax rate created successfully!', 'wp-ever-accounting' );
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
	 * @since 1.1.6
	 * @return void
	 */
	public static function edit_invoice() {
		check_ajax_referer( 'eac_edit_invoice' );
		$referer  = wp_get_referer();
		$posted   = eac_clean( wp_unslash( $_POST ) );
		$posted   = wp_parse_args( $posted );
		$document = new Invoice( $posted['id'] );
		$document->set_props( $posted );

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
	 * @since 1.1.6
	 * @return void
	 */
	public static function calculate_invoice() {
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
		$document->set_props( $posted );
		$document->calculate_totals();
		include __DIR__ . '/views/invoices/invoice-form.php';
		exit();
	}

	/**
	 * Export data.
	 *
	 * @since 1.1.6
	 * @return void
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
}
