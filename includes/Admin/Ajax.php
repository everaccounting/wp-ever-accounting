<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit;

/**
 * Class Ajax
 *
 * @package EverAccounting\Admin
 */
class Ajax {

	/**
	 * Ajax constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_eac_json_search', array( $this, 'handle_json_search' ) );
		add_action( 'wp_ajax_eac_add_note', array( $this, 'handle_add_note' ) );
		add_action( 'wp_ajax_eac_delete_note', array( $this, 'handle_delete_note' ) );
		add_action( 'wp_ajax_eac_add_invoice_payment', array( $this, 'add_invoice_payment' ) );
		add_action( 'wp_ajax_eac_get_bill_address', array( $this, 'get_bill_address' ) );
		add_action( 'wp_ajax_eac_get_recalculated_bill', array( $this, 'get_recalculated_bill' ) );
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
			'search'   => $term,
			'no_count' => true,
		);
		switch ( $type ) {
			case 'account':
				$accounts = EAC()->accounts->query( $args );
				$total    = EAC()->accounts->query( $args, true );
				$results  = array_map(
					function ( $item ) {
						$item->text = $item->formatted_name;

						return $item->to_array();
					},
					$accounts
				);
				break;
			case 'item':
				$items   = EAC()->items->query( $args );
				$total   = EAC()->items->query( $args, true );
				$results = array_map(
					function ( $item ) {
						$item->text = $item->formatted_name;

						return $item->to_array();
					},
					$items
				);
				break;
			case 'category':
				$args['type'] = isset( $_POST['subtype'] ) ? sanitize_text_field( wp_unslash( $_POST['subtype'] ) ) : '';
				$categories   = EAC()->categories->query( $args );
				$total        = EAC()->categories->query( $args, true );
				$results      = array_map(
					function ( $item ) {
						$item->text = $item->formatted_name;

						return $item->to_array();
					},
					$categories
				);
				break;

			case 'payment':
				$payments = EAC()->payments->query( $args );
				$total    = EAC()->payments->query( $args, true );
				$results  = array_map(
					function ( $item ) {
						$item->text = $item->amount;

						return $item->to_array();
					},
					$payments
				);
				break;

			case 'expense':
				$expenses = EAC()->expenses->query( $args );
				$total    = EAC()->expenses->query( $args, true );
				foreach ( $expenses as $expense ) {
					$results[] = array(
						'id'   => $expense->id,
						'text' => $expense->amount,
					);
				}
				break;

			case 'customer':
				$customers = EAC()->customers->query( $args );
				$total     = EAC()->customers->query( $args, true );
				$results   = array_map(
					function ( $item ) {
						$item->text = $item->formatted_name;

						return $item->to_array();
					},
					$customers
				);
				break;

			case 'vendor':
				$vendors = EAC()->vendors->query( $args );
				$total   = EAC()->vendors->query( $args, true );
				$results = array_map(
					function ( $item ) {
						$item->text = $item->formatted_name;

						return $item->to_array();
					},
					$vendors
				);
				break;
			case 'invoice':
				$args['status__not'] = 'paid';
				$invoices            = EAC()->invoices->query( $args );
				$total               = EAC()->invoices->query( $args, true );
				foreach ( $invoices as $invoice ) {
					$results[] = array(
						'id'   => $invoice->id,
						'text' => $invoice->number,
					);
				}
				break;
			case 'bill':
				$args['status__not'] = 'paid';
				$bills               = EAC()->bills->query( $args );
				$total               = EAC()->bills->query( $args, true );
				foreach ( $bills as $bill ) {
					$results[] = array(
						'id'   => $bill->id,
						'text' => $bill->number,
					);
				}
				break;
			case 'tax':
				$tax_rates = EAC()->taxes->query( $args );
				$total     = EAC()->taxes->query( $args, true );
				$results   = array_map(
					function ( $item ) {
						$item->text = $item->formatted_name;

						return $item->to_array();
					},
					$tax_rates
				);
				break;

			case 'page':
				// query pages.
				$wp_query = new \WP_Query(
					array(
						'post_type'      => 'page',
						'posts_per_page' => $limit,
						'paged'          => $page,
						's'              => $term,
					)
				);

				$pages = $wp_query->get_posts();
				$total = $wp_query->found_posts;
				foreach ( $pages as $_page ) {
					$results[] = array(
						'id'   => $_page->ID,
						'text' => empty( $_page->post_title ) ? __( '(No title)', 'wp-ever-accounting' ) : wp_strip_all_tags( $_page->post_title ),
					);
				}

				// reset post data.
				$wp_query->reset_postdata();
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
	 * Add note.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function handle_add_note() {
		check_ajax_referer( 'eac_add_note', 'nonce' );

		if ( ! current_user_can( 'manage_accounting' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			wp_die( - 1 );
		}

		$parent_id   = isset( $_POST['parent_id'] ) ? absint( wp_unslash( $_POST['parent_id'] ) ) : 0;
		$parent_type = isset( $_POST['parent_type'] ) ? sanitize_key( wp_unslash( $_POST['parent_type'] ) ) : '';
		$content     = isset( $_POST['content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['content'] ) ) : '';

		// If any of the required fields are empty, return an error.
		if ( empty( $parent_id ) || empty( $parent_type ) || empty( $content ) ) {
			wp_die( - 1 );
		}

		$note = EAC()->notes->insert(
			array(
				'parent_id'   => $parent_id,
				'parent_type' => $parent_type,
				'content'     => $content,
				'author_id'   => get_current_user_id(),
			)
		);

		// If error, return error.
		if ( is_wp_error( $note ) ) {
			wp_die( - 1 );
		}

		ob_start();
		include __DIR__ . '/views/note-item.php';
		$note_html = ob_get_clean();

		$x = new \WP_Ajax_Response();
		$x->add(
			array(
				'what' => 'note_html',
				'data' => $note_html,
			)
		);

		$x->send();
	}

	/**
	 * Delete note.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function handle_delete_note() {
		check_ajax_referer( 'eac_delete_note', 'nonce' );

		if ( ! current_user_can( 'manage_accounting' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			wp_die( - 1 );
		}

		$note_id = isset( $_POST['note_id'] ) ? absint( wp_unslash( $_POST['note_id'] ) ) : 0;
		$note    = EAC()->notes->get( $note_id );

		if ( ! $note ) {
			wp_die( - 1 );
		}

		$note->delete();

		wp_die( 1 );
	}

	/**
	 * Add invoice payment.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function add_invoice_payment() {
		check_ajax_referer( 'eac_add_invoice_payment' );

		if ( ! current_user_can( 'eac_manage_payment' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			wp_send_json_error( array( 'message' => __( 'You do not have permission to add payment.', 'wp-ever-accounting' ) ) );
		}

		$invoice_id = isset( $_POST['invoice_id'] ) ? absint( wp_unslash( $_POST['invoice_id'] ) ) : 0;
		$account_id = isset( $_POST['account_id'] ) ? absint( wp_unslash( $_POST['account_id'] ) ) : 0;
		$exchange   = isset( $_POST['exchange_rate'] ) ? floatval( wp_unslash( $_POST['exchange_rate'] ) ) : '';
		$date       = isset( $_POST['payment_date'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_date'] ) ) : '';
		$reference  = isset( $_POST['reference'] ) ? sanitize_text_field( wp_unslash( $_POST['reference'] ) ) : '';
		$note       = isset( $_POST['note'] ) ? sanitize_text_field( wp_unslash( $_POST['note'] ) ) : '';

		$invoice = EAC()->invoices->get( $invoice_id );
		if ( ! $invoice ) {
			wp_send_json_error( array( 'message' => __( 'Invoice not found.', 'wp-ever-accounting' ) ) );
		}

		$account = EAC()->accounts->get( $account_id );
		if ( ! $account ) {
			wp_send_json_error( array( 'message' => __( 'Account not found.', 'wp-ever-accounting' ) ) );
		}

		// convert the invoice amount to the account currency.
		$amount = eac_convert_currency( $invoice->total, $invoice->exchange_rate, $exchange );

		$payment = EAC()->payments->insert(
			array(
				'account_id'   => $account_id,
				'exchange'     => $exchange,
				'amount'       => $amount,
				'payment_date' => $date,
				'reference'    => $reference,
				'note'         => $note,
			)
		);

		if ( is_wp_error( $payment ) ) {
			wp_send_json_error( array( 'message' => $payment->get_error_message() ) );
		}

		$invoice->transaction_id = $payment->id;
		$invoice->status         = 'paid';
		$invoice->payment_date   = $date;
		$ret                     = $invoice->save();
		if ( is_wp_error( $ret ) ) {
			wp_send_json_error( array( 'message' => $ret->get_error_message() ) );
		}

		wp_send_json_success( array( 'message' => __( 'Payment added successfully.', 'wp-ever-accounting' ) ) );
	}


	/**
	 * Get bill billings.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function get_bill_address() {
		check_ajax_referer( 'eac_edit_bill' );

		if ( ! current_user_can( 'eac_manage_bill' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			wp_die( - 1 );
		}

		$vendor_id = isset( $_POST['contact_id'] ) ? absint( wp_unslash( $_POST['contact_id'] ) ) : 0;
		$vendor    = EAC()->vendors->get( $vendor_id );
		if ( ! $vendor ) {
			wp_die( - 1 );
		}
		$bill                     = new Bill();
		$bill->contact_id         = $vendor_id;
		$bill->contact_name       = $vendor->name;
		$bill->contact_email      = $vendor->email;
		$bill->contact_phone      = $vendor->phone;
		$bill->contact_address    = $vendor->address;
		$bill->contact_city       = $vendor->city;
		$bill->contact_state      = $vendor->state;
		$bill->contact_postcode   = $vendor->postcode;
		$bill->contact_country    = $vendor->country;
		$bill->contact_tax_number = $vendor->tax_number;

		ob_start();
		include __DIR__ . '/views/bill-address.php';
		$html = ob_get_clean();

		$x = new \WP_Ajax_Response();
		$x->add(
			array(
				'what' => 'billings_html',
				'data' => $html,
			)
		);

		$x->send();

		wp_die( 1 );
	}

	/**
	 * Get recalculated html.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function get_recalculated_bill() {
		check_ajax_referer( 'eac_edit_bill' );

		if ( ! current_user_can( 'eac_manage_bill' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			wp_die( - 1 );
		}

		$id                   = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$items                = isset( $_POST['items'] ) ? map_deep( wp_unslash( $_POST['items'] ), 'sanitize_text_field' ) : array();
		$bill                 = Bill::make( $_POST );
//		$bill->currency       = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : eac_base_currency();
//		$bill->exchange_rate  = isset( $_POST['exchange_rate'] ) ? floatval( wp_unslash( $_POST['exchange_rate'] ) ) : 1;
//		$bill->discount_type  = isset( $_POST['discount_type'] ) ? sanitize_text_field( wp_unslash( $_POST['discount_type'] ) ) : 'fixed';
//		$bill->discount_value = isset( $_POST['discount_value'] ) ? floatval( wp_unslash( $_POST['discount_value'] ) ) : 0;
		$bill->items          = array();
		$bill->set_items( $items );
		$bill->calculate_totals();

		$columns = EAC()->bills->get_columns();
		// if tax is not enabled and invoice has no tax, remove the tax column.
		if ( ! $bill->is_taxed() ) {
			unset( $columns['tax'] );
		}

		ob_start();
		include __DIR__ . '/views/bill-items.php';
		$items_html = ob_get_clean();

		ob_start();
		include __DIR__ . '/views/bill-totals.php';
		$totals_html = ob_get_clean();

		$x = new \WP_Ajax_Response();

		$x->add(
			array(
				'what' => 'items_html',
				'id'   => 'items_html',
				'data' => $items_html,
			)
		);
		$x->add(
			array(
				'what' => 'totals_html',
				'id'   => 'totals_html',
				'data' => $totals_html,
			)
		);

		$x->send();

		wp_die( 1 );
	}
}
