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
				$results      = array_map(
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
