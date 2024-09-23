<?php

namespace EverAccounting\Admin;

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
}
