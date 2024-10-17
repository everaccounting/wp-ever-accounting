<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

/**
 * Class Actions.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @subpackage Admin
 */
class Actions {

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_eac_json_search', array( $this, 'handle_json_search' ) );
		add_action( 'admin_post_eac_edit_invoice', array( $this, 'handle_edit_invoice' ) );
		add_action( 'admin_post_eac_add_invoice_payment', array( $this, 'handle_invoice_payment' ) );

		add_action( 'wp_ajax_eac_get_currency', array( $this, 'ajax_get_currency' ) );
		add_action( 'wp_ajax_eac_get_account', array( $this, 'ajax_get_account' ) );
		add_action( 'wp_ajax_eac_get_item', array( $this, 'ajax_get_item' ) );
		add_action( 'wp_ajax_eac_get_category', array( $this, 'ajax_get_category' ) );
		add_action( 'wp_ajax_eac_get_tax', array( $this, 'ajax_get_tax' ) );
		add_action( 'wp_ajax_eac_get_customer', array( $this, 'ajax_get_customer' ) );
		add_action( 'wp_ajax_eac_get_vendor', array( $this, 'ajax_get_vendor' ) );
		add_action( 'wp_ajax_eac_get_payment', array( $this, 'ajax_get_payment' ) );
		add_action( 'wp_ajax_eac_get_invoice', array( $this, 'ajax_get_invoice' ) );
		add_action( 'wp_ajax_eac_get_bill', array( $this, 'ajax_get_bill' ) );

		add_action( 'wp_ajax_eac_convert_currency', array( $this, 'ajax_convert_currency' ) );

		add_action( 'wp_ajax_eac_calculate_invoice', array( $this, 'ajax_calculate_invoice_totals' ) );
		add_action( 'wp_ajax_eac_add_invoice_payment', array( $this, 'ajax_add_invoice_payment' ) );

		// Export data.
		add_action( 'admin_post_eac_export_data', array( __CLASS__, 'export_data' ) );
	}

	/**
	 * Search items.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function handle_json_search() {
		check_ajax_referer( 'eac_search_action' );
		$type    = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$term    = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';
		$limit   = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 20;
		$page    = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$results = array();
		$total   = 0;

		$args = array(
			'limit'    => $limit,
			'page'     => $page,
			'status'   => 'active',
			'search'   => $term,
			'no_count' => true,
		);
		switch ( $type ) {
			case 'account':
				$accounts = EAC()->accounts->query( $args );
				$total    = EAC()->accounts->query( $args, true );
				$results  = array_map(
					function ( $account ) {
						return array(
							'id'   => $account->id,
							'text' => $account->formatted_name,
						);
					},
					$accounts
				);
				break;
			case 'item':
				$items   = EAC()->items->query( $args );
				$total   = EAC()->items->query( $args, true );
				$results = array_map(
					function ( $item ) {
						return array(
							'id'   => $item->id,
							'text' => $item->formatted_name,
						);
					},
					$items
				);
				break;
			case 'category':
				$args['type'] = isset( $_POST['subtype'] ) ? sanitize_text_field( wp_unslash( $_POST['subtype'] ) ) : '';
				$categories   = EAC()->categories->query( $args );
				$total        = EAC()->categories->query( $args, true );
				$results      = array_map(
					function ( $category ) {
						return array(
							'id'   => $category->id,
							'text' => $category->formatted_name,
						);
					},
					$categories
				);
				break;

			case 'payment':
				$payments = EAC()->payments->query( $args );
				$total    = EAC()->payments->query( $args, true );
				foreach ( $payments as $payment ) {
					$results[] = array(
						'id'   => $payment->get_id(),
						'text' => $payment->get_amount(),
					);
				}
				break;

			case 'expense':
				$expenses = EAC()->expenses->query( $args );
				$total    = EAC()->expenses->query( $args, true );
				foreach ( $expenses as $expense ) {
					$results[] = array(
						'id'   => $expense->get_id(),
						'text' => $expense->get_amount(),
					);
				}
				break;

			case 'customer':
				$customers = EAC()->customers->query( $args );
				$total     = EAC()->customers->query( $args, true );
				$results   = array_map(
					function ( $customer ) {
						return array(
							'id'   => $customer->id,
							'text' => $customer->formatted_name,
						);
					},
					$customers
				);
				break;

			case 'vendor':
				$vendors = EAC()->vendors->query( $args );
				$total   = EAC()->vendors->query( $args, true );
				$results = array_map(
					function ( $vendor ) {
						return array(
							'id'   => $vendor->id,
							'text' => $vendor->formatted_name,
						);
					},
					$vendors
				);
				break;
			case 'invoice':
				$invoices = EAC()->invoices->query( $args );
				$total    = EAC()->invoices->query( $args, true );
				foreach ( $invoices as $invoice ) {
					$results[] = array(
						'id'   => $invoice->get_id(),
						'text' => $invoice->get_formatted_name(),
					);
				}
				break;
			case 'bill':
				$bills = EAC()->bills->query( $args );
				$total = EAC()->bills->query( $args, true );
				foreach ( $bills as $bill ) {
					$results[] = array(
						'id'   => $bill->get_id(),
						'text' => $bill->get_bill_number(),
					);
				}
				break;
			case 'tax':
				$tax_rates = EAC()->taxes->query( $args );
				$total     = EAC()->taxes->query( $args, true );
				$results   = array_map(
					function ( $tax_rate ) {
						return array(
							'id'   => $tax_rate->id,
							'text' => $tax_rate->formatted_name,
						);
					},
					$tax_rates
				);
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
	 * Get currency.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_get_currency() {
		check_ajax_referer( 'eac_currency' );
		$currency = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';
		$currency = eac_get_currency( $currency );
		if ( ! $currency ) {
			wp_send_json_error( __( 'Currency not found.', 'wp-ever-accounting' ) );
		}
		wp_send_json_success( $currency->to_array() );
		exit;
	}

	/**
	 * Get account.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_get_account() {
		check_ajax_referer( 'eac_account' );
		$account_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$account    = eac_get_account( $account_id );
		if ( ! $account ) {
			wp_send_json_error( __( 'Account not found.', 'wp-ever-accounting' ) );
		}
		wp_send_json_success( $account->to_array() );
		exit;
	}

	/**
	 * Get item.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_get_item() {
		check_ajax_referer( 'eac_item' );
		$item_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$item    = eac_get_item( $item_id );
		if ( ! $item ) {
			wp_send_json_error( __( 'Item not found.', 'wp-ever-accounting' ) );
		}
		wp_send_json_success( $item->to_array() );
		exit;
	}

	/**
	 * Get category.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_get_category() {
		check_ajax_referer( 'eac_category' );
		$category_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$category    = eac_get_category( $category_id );
		if ( ! $category ) {
			wp_send_json_error( __( 'Category not found.', 'wp-ever-accounting' ) );
		}
		wp_send_json_success( $category->to_array() );
		exit;
	}

	/**
	 * Get tax.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_get_tax() {
		check_ajax_referer( 'eac_tax' );
		$tax_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$tax    = eac_get_tax( $tax_id );
		if ( ! $tax ) {
			wp_send_json_error( __( 'Tax not found.', 'wp-ever-accounting' ) );
		}
		wp_send_json_success( $tax->to_array() );
		exit;
	}

	/**
	 * Get customer.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_get_customer() {
		check_ajax_referer( 'eac_customer' );
		$customer_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$customer    = eac_get_customer( $customer_id );
		if ( ! $customer ) {
			wp_send_json_error( __( 'Customer not found.', 'wp-ever-accounting' ) );
		}
		wp_send_json_success( $customer->to_array() );
		exit;
	}

	/**
	 * Get vendor.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_get_vendor() {
		check_ajax_referer( 'eac_vendor' );
		$vendor_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$vendor    = eac_get_vendor( $vendor_id );
		if ( ! $vendor ) {
			wp_send_json_error( __( 'Vendor not found.', 'wp-ever-accounting' ) );
		}
		wp_send_json_success( $vendor->to_array() );
		exit;
	}

	/**
	 * Get payment.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_get_payment() {
		check_ajax_referer( 'eac_payment' );
		$payment_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$payment    = eac_get_payment( $payment_id );
		if ( ! $payment ) {
			wp_send_json_error( __( 'Payment not found.', 'wp-ever-accounting' ) );
		}
		wp_send_json_success( $payment->to_array() );
		exit;
	}

	/**
	 * Get expense.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_get_expense() {
		check_ajax_referer( 'eac_expense' );
		$expense_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$expense    = eac_get_expense( $expense_id );
		if ( ! $expense ) {
			wp_send_json_error( __( 'Expense not found.', 'wp-ever-accounting' ) );
		}
		wp_send_json_success( $expense->to_array() );
		exit;
	}

	/**
	 * Get invoice.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_get_invoice() {
		check_ajax_referer( 'eac_invoice' );
		$invoice_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$invoice    = eac_get_invoice( $invoice_id );
		if ( ! $invoice ) {
			wp_send_json_error( __( 'Invoice not found.', 'wp-ever-accounting' ) );
		}
		wp_send_json_success( $invoice->to_array() );
		exit;
	}

	/**
	 * Get bill.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_get_bill() {
		check_ajax_referer( 'eac_bill' );
		$bill_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$bill    = eac_get_bill( $bill_id );
		if ( ! $bill ) {
			wp_send_json_error( __( 'Bill not found.', 'wp-ever-accounting' ) );
		}
		wp_send_json_success( $bill->to_array() );
		exit;
	}

	/**
	 * Get converted amount.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_convert_currency() {
		check_ajax_referer( 'eac_currency' );
		$amount    = isset( $_POST['amount'] ) ? floatval( wp_unslash( $_POST['amount'] ) ) : 0;
		$from      = isset( $_POST['from'] ) ? sanitize_text_field( wp_unslash( $_POST['from'] ) ) : eac_base_currency();
		$to        = isset( $_POST['to'] ) ? sanitize_text_field( wp_unslash( $_POST['to'] ) ) : eac_base_currency();
		$converted = eac_convert_currency( $amount, $from, $to );
		wp_send_json_success( $converted );
		exit;
	}

	/**
	 * Calculate invoice total.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_calculate_invoice_totals() {
		check_ajax_referer( 'eac_edit_invoice' );
		$_POST['calculate_totals'] = 'yes';
		$document                  = $this->handle_edit_invoice();
		if ( ! $document instanceof Invoice ) {
			$document = new Invoice();
		}

		include __DIR__ . '/views/sales/invoices/form-main.php';
		exit();
	}

	/**
	 * Edit invoice.
	 *
	 * @since 1.2.0
	 * @return void|Invoice $document Invoice object.
	 */
	public function handle_edit_invoice() {
		check_ajax_referer( 'eac_edit_invoice' );
		$referer                      = wp_get_referer();
		$calculate_totals             = isset( $_POST['calculate_totals'] ) ? sanitize_text_field( wp_unslash( $_POST['calculate_totals'] ) ) : '';
		$items                        = isset( $_POST['items'] ) ? map_deep( wp_unslash( $_POST['items'] ), 'sanitize_text_field' ) : array();
		$id                           = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$document                     = Invoice::make( $id );
		$document->contact_id         = isset( $_POST['contact_id'] ) ? absint( wp_unslash( $_POST['contact_id'] ) ) : 0;
		$document->discount_amount    = isset( $_POST['discount_amount'] ) ? floatval( wp_unslash( $_POST['discount_amount'] ) ) : 0;
		$document->discount_type      = isset( $_POST['discount_type'] ) ? sanitize_text_field( wp_unslash( $_POST['discount_type'] ) ) : 'fixed';
		$document->issue_date         = isset( $_POST['issue_date'] ) ? sanitize_text_field( wp_unslash( $_POST['issue_date'] ) ) : '';
		$document->due_date           = isset( $_POST['due_date'] ) ? sanitize_text_field( wp_unslash( $_POST['due_date'] ) ) : '';
		$document->number             = isset( $_POST['number'] ) ? sanitize_text_field( wp_unslash( $_POST['number'] ) ) : '';
		$document->reference          = isset( $_POST['reference'] ) ? sanitize_text_field( wp_unslash( $_POST['reference'] ) ) : '';
		$document->currency           = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : eac_base_currency();
		$document->billing_name       = isset( $_POST['billing_name'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_name'] ) ) : '';
		$document->vat_exempt         = isset( $_POST['vat_exempt'] ) ? sanitize_text_field( wp_unslash( $_POST['vat_exempt'] ) ) : 'no';
		$document->billing_company    = isset( $_POST['billing_company'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_company'] ) ) : '';
		$document->billing_address_1  = isset( $_POST['billing_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_1'] ) ) : '';
		$document->billing_address_2  = isset( $_POST['billing_address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_address_2'] ) ) : '';
		$document->billing_city       = isset( $_POST['billing_city'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_city'] ) ) : '';
		$document->billing_state      = isset( $_POST['billing_state'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_state'] ) ) : '';
		$document->billing_postcode   = isset( $_POST['billing_postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_postcode'] ) ) : '';
		$document->billing_country    = isset( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : '';
		$document->billing_phone      = isset( $_POST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';
		$document->billing_email      = isset( $_POST['billing_email'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_email'] ) ) : '';
		$document->billing_vat_number = isset( $_POST['billing_vat_number'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_vat_number'] ) ) : '';

		// Empty taxes not submitted so if not set, set it to empty array.
		foreach ( $items as $key => $item ) {
			if ( ! isset( $item['taxes'] ) ) {
				$items[ $key ]['taxes'] = array();
			}
		}

		$document->set_items( $items );

		if ( 'yes' === $calculate_totals ) {
			$document->calculate_totals();

			return $document;
		}
		$saved = $document->save();
		if ( is_wp_error( $saved ) ) {
			EAC()->flash->error( $saved->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Invoice saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $document->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Handle invoice payment.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function handle_invoice_payment() {
		check_admin_referer( 'eac_invoice_payment' );
		$referer = wp_get_referer();
		$id      = isset( $_POST['invoice_id'] ) ? absint( wp_unslash( $_POST['invoice_id'] ) ) : 0;
		$invoice = eac_get_invoice( $id );
		if ( ! $invoice ) {
			EAC()->flash->error( __( 'Invoice not found.', 'wp-ever-accounting' ) );
			wp_safe_redirect( $referer );
			exit;
		}
		$data = array(
			'date'         => isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '',
			'account_id'   => isset( $_POST['account_id'] ) ? absint( wp_unslash( $_POST['account_id'] ) ) : 0,
			'amount'       => isset( $_POST['amount'] ) ? floatval( wp_unslash( $_POST['amount'] ) ) : 0,
			'payment_method' => isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : '',
			'note'         => isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '',
		);

		$is_error = $invoice->add_payment( $data );
		if ( is_wp_error( $is_error ) ) {
			EAC()->flash->error( $is_error->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Invoice payment added successfully.', 'wp-ever-accounting' ) );
		}

		wp_safe_redirect( $referer );
		exit;
	}


	/**
	 * Add invoice payment.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function ajax_add_invoice_payment() {
		check_ajax_referer( 'eac_invoice' );
		$invoice_id = isset( $_POST['invoice_id'] ) ? absint( wp_unslash( $_POST['invoice_id'] ) ) : 0;
		$date       = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
		$account_id = isset( $_POST['account_id'] ) ? absint( wp_unslash( $_POST['account_id'] ) ) : 0;
		$amount     = isset( $_POST['amount'] ) ? floatval( wp_unslash( $_POST['amount'] ) ) : 0;
		$mode       = isset( $_POST['mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mode'] ) ) : '';
		$note       = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';

		if ( is_wp_error( $payment ) ) {
			wp_send_json_error( $payment->get_error_message() );
		}
		wp_send_json_success( $payment->to_array() );
		exit;
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
				$exporter = new Tools\Exporters\Accounts();
				$exporter->process_step( 1 );
				$exporter->export();
				break;
			case 'customers':
				$exporter = new Tools\Exporters\Customers();
				$exporter->process_step( 1 );
				$exporter->export();
				break;
			case 'items':
				$exporter = new Tools\Exporters\Items();
				$exporter->process_step( 1 );
				$exporter->export();
				break;
			case 'categories':
				$exporter = new Tools\Exporters\Categories();
				$exporter->process_step( 1 );
				$exporter->export();
				break;
			case 'vendors':
				$exporter = new Tools\Exporters\Vendors();
				$exporter->process_step( 1 );
				$exporter->export();
				break;
		}

		wp_safe_redirect( wp_get_referer() );
		exit();
	}
}
