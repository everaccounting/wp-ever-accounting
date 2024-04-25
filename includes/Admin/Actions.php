<?php

namespace EverAccounting\Admin;

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
		add_action( 'admin_post_eac_edit_item', array( $this, 'handle_edit_item' ) );
		add_action( 'admin_post_eac_edit_revenue', array( $this, 'handle_edit_revenue' ) );
		add_action( 'admin_post_eac_edit_customer', array( $this, 'handle_edit_customer' ) );
		add_action( 'admin_post_eac_edit_vendor', array( $this, 'handle_edit_vendor' ) );
		add_action( 'admin_post_eac_edit_account', array( $this, 'handle_edit_account' ) );
		add_action( 'admin_post_eac_edit_category', array( $this, 'handle_edit_category' ) );
		add_action( 'admin_post_eac_edit_currency', array( $this, 'handle_edit_currency' ) );
		add_action( 'admin_post_eac_edit_tax', array( $this, 'handle_edit_tax' ) );
	}

	/**
	 * Search items.
	 *
	 * @return void
	 * @since 1.2.0
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
				$accounts = eac_get_accounts( $args );
				$total    = eac_get_accounts( $args, true );
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
				$items   = eac_get_items( $args );
				$total   = eac_get_items( $args, true );
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
				$categories   = eac_get_categories( $args );
				$total        = eac_get_categories( $args, true );
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
			case 'currency':
				$currencies = eac_get_currencies();
				$total      = eac_get_currencies();
				$results    = array_map(
					function ( $currency ) {
						return array(
							'id'   => $currency->id,
							'text' => $currency->formatted_name,
						);
					},
					$currencies
				);
				break;

			// case 'payment':
			// $payments = eac_get_payments( $args );
			// $total    = eac_get_payments( $args, true );
			// foreach ( $payments as $payment ) {
			// $results[] = array(
			// 'id'   => $payment->get_id(),
			// 'text' => $payment->get_amount(),
			// );
			// }
			// break;

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
				$vendors = eac_get_vendors( $args );
				$total   = eac_get_vendors( $args, true );
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
			// case 'invoice':
			// $invoices = eac_get_invoices( $args );
			// $total    = eac_get_invoices( $args, true );
			// foreach ( $invoices as $invoice ) {
			// $results[] = array(
			// 'id'   => $invoice->get_id(),
			// 'text' => $invoice->get_formatted_name(),
			// );
			// }
			// break;
			// case 'bill':
			// $bills = eac_get_bills( $args );
			// $total = eac_get_bills( $args, true );
			// foreach ( $bills as $bill ) {
			// $results[] = array(
			// 'id'   => $bill->get_id(),
			// 'text' => $bill->get_bill_number(),
			// );
			// }
			// break;
			case 'tax':
				$tax_rates = eac_get_taxes( $args );
				$total     = eac_get_taxes( $args, true );
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
	 * Edit item.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public static function handle_edit_item() {
		check_admin_referer( 'eac_edit_item' );
		$referer     = wp_get_referer();
		$id          = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$type        = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$price       = isset( $_POST['price'] ) ? floatval( wp_unslash( $_POST['price'] ) ) : 0;
		$cost        = isset( $_POST['cost'] ) ? floatval( wp_unslash( $_POST['cost'] ) ) : 0;
		$category_id = isset( $_POST['category_id'] ) ? absint( wp_unslash( $_POST['category_id'] ) ) : 0;
		$unit        = isset( $_POST['unit'] ) ? sanitize_text_field( wp_unslash( $_POST['unit'] ) ) : '';
		$tax_ids     = isset( $_POST['tax_ids'] ) ? array_map( 'absint', wp_unslash( $_POST['tax_ids'] ) ) : array();
		$desc        = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
		$status      = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		$item        = eac_insert_item(
			array(
				'id'          => $id,
				'name'        => $name,
				'type'        => $type,
				'price'       => $price,
				'cost'        => $cost,
				'category_id' => $category_id,
				'unit'        => $unit,
				'tax_ids'     => implode( ',', array_unique( array_filter( $tax_ids ) ) ),
				'description' => $desc,
				'status'      => $status,
			)
		);

		if ( is_wp_error( $item ) ) {
			EAC()->flash->error( $item->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Item saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $item->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit();
	}

	/**
	 * Edit revenue.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public static function handle_edit_revenue() {
		check_admin_referer( 'eac_edit_revenue' );
		$referer = wp_get_referer();
		$revenue = eac_insert_revenue(
			array(
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
			)
		);

		if ( is_wp_error( $revenue ) ) {
			EAC()->flash->error( $revenue->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Revenue saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $revenue->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Edit customer.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public static function handle_edit_customer() {
		check_admin_referer( 'eac_edit_customer' );
		$referer  = wp_get_referer();
		$customer = eac_insert_customer(
			array(
				'id'            => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
				'name'          => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'currency_code' => isset( $_POST['currency_code'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_code'] ) ) : eac_get_base_currency(),
				'email'         => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
				'phone'         => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
				'company'       => isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '',
				'website'       => isset( $_POST['website'] ) ? esc_url_raw( wp_unslash( $_POST['website'] ) ) : '',
				'vat_number'    => isset( $_POST['vat_number'] ) ? sanitize_text_field( wp_unslash( $_POST['vat_number'] ) ) : '',
				'vat_exempt'    => isset( $_POST['vat_exempt'] ) ? sanitize_text_field( wp_unslash( $_POST['vat_exempt'] ) ) : 'no', // phpcs:ignore
				'address_1'     => isset( $_POST['address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['address_1'] ) ) : '',
				'address_2'     => isset( $_POST['address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['address_2'] ) ) : '',
				'city'          => isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '',
				'state'         => isset( $_POST['state'] ) ? sanitize_text_field( wp_unslash( $_POST['state'] ) ) : '',
				'postcode'      => isset( $_POST['postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['postcode'] ) ) : '',
				'country'       => isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '',
				'status'        => isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active',
			)
		);

		if ( is_wp_error( $customer ) ) {
			EAC()->flash->error( $customer->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Customer saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $customer->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );

		}
		wp_safe_redirect( $referer );
		exit;
	}


	/**
	 * Edit vendor.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public static function handle_edit_vendor() {
		check_admin_referer( 'eac_edit_vendor' );
		$referer = wp_get_referer();
		$vendor  = eac_insert_vendor(
			array(
				'id'            => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
				'name'          => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'currency_code' => isset( $_POST['currency_code'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_code'] ) ) : eac_get_base_currency(),
				'email'         => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
				'phone'         => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
				'company'       => isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '',
				'website'       => isset( $_POST['website'] ) ? esc_url_raw( wp_unslash( $_POST['website'] ) ) : '',
				'vat_number'    => isset( $_POST['vat_number'] ) ? sanitize_text_field( wp_unslash( $_POST['vat_number'] ) ) : '',
				'vat_exempt'    => isset( $_POST['vat_exempt'] ) ? sanitize_text_field( wp_unslash( $_POST['vat_exempt'] ) ) : 'no', // phpcs:ignore
				'address_1'     => isset( $_POST['address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['address_1'] ) ) : '',
				'address_2'     => isset( $_POST['address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['address_2'] ) ) : '',
				'city'          => isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '',
				'state'         => isset( $_POST['state'] ) ? sanitize_text_field( wp_unslash( $_POST['state'] ) ) : '',
				'postcode'      => isset( $_POST['postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['postcode'] ) ) : '',
				'country'       => isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '',
				'status'        => isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active',
			)
		);

		if ( is_wp_error( $vendor ) ) {
			EAC()->flash->error( $vendor->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Vendor saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $vendor->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );

		}
		wp_safe_redirect( $referer );
		exit;
	}


	/**
	 * Edit account.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function handle_edit_account() {
		check_admin_referer( 'eac_edit_account' );
		$referer = wp_get_referer();
		$account = eac_insert_account(
			array(
				'id'              => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
				'name'            => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'number'          => isset( $_POST['number'] ) ? sanitize_text_field( wp_unslash( $_POST['number'] ) ) : '',
				'type'            => isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '',
				'currency_code'   => isset( $_POST['currency_code'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_code'] ) ) : '',
				'opening_balance' => isset( $_POST['opening_balance'] ) ? floatval( wp_unslash( $_POST['opening_balance'] ) ) : 0,
				'bank_name'       => isset( $_POST['bank_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_name'] ) ) : '',
				'bank_phone'      => isset( $_POST['bank_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_phone'] ) ) : '',
				'bank_address'    => isset( $_POST['bank_address'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_address'] ) ) : '',
				'status'          => isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active',
			)
		);

		if ( is_wp_error( $account ) ) {
			EAC()->flash->error( $account->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Account saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $account->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Edit category.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public static function handle_edit_category() {
		check_admin_referer( 'eac_edit_category' );
		$referer  = wp_get_referer();
		$id       = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$type     = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$desc     = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
		$status   = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		$category = eac_insert_category(
			array(
				'id'          => $id,
				'name'        => $name,
				'type'        => $type,
				'description' => $desc,
				'status'      => $status,
			)
		);

		if ( is_wp_error( $category ) ) {
			EAC()->flash->error( $category->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Category saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $category->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Edit currency.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public static function handle_edit_currency() {
		check_admin_referer( 'eac_edit_currency' );
		$referer            = wp_get_referer();
		$id                 = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$code               = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';
		$name               = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$symbol             = isset( $_POST['symbol'] ) ? sanitize_text_field( wp_unslash( $_POST['symbol'] ) ) : '';
		$exchange_rate      = isset( $_POST['exchange_rate'] ) ? doubleval( wp_unslash( $_POST['exchange_rate'] ) ) : 0;
		$thousand_separator = isset( $_POST['thousand_separator'] ) ? sanitize_text_field( wp_unslash( $_POST['thousand_separator'] ) ) : '';
		$decimal_separator  = isset( $_POST['decimal_separator'] ) ? sanitize_text_field( wp_unslash( $_POST['decimal_separator'] ) ) : '';
		$precision          = isset( $_POST['precision'] ) ? absint( wp_unslash( $_POST['precision'] ) ) : '';
		$position           = isset( $_POST['position'] ) ? sanitize_text_field( wp_unslash( $_POST['position'] ) ) : '';
		$status             = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		$currency           = eac_insert_currency(
			array(
				'id'                 => $id,
				'code'               => $code,
				'name'               => $name,
				'symbol'             => $symbol,
				'exchange_rate'      => $exchange_rate,
				'thousand_separator' => $thousand_separator,
				'decimal_separator'  => $decimal_separator,
				'precision'          => $precision,
				'position'           => $position,
				'status'             => $status,
			)
		);

		if ( is_wp_error( $currency ) ) {
			EAC()->flash->error( $currency->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Currency saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $currency->id, $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Edit tax.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public static function handle_edit_tax() {
		check_admin_referer( 'eac_edit_tax' );
		$referer     = wp_get_referer();
		$id          = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$rate        = isset( $_POST['rate'] ) ? doubleval( wp_unslash( $_POST['rate'] ) ) : '';
		$is_compound = isset( $_POST['is_compound'] ) ? sanitize_text_field( wp_unslash( $_POST['is_compound'] ) ) : '';
		$desc        = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
		$status      = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		if ( $is_compound ) {
			$is_compound = 'yes' === $is_compound ? true : false;
		}
		$tax = eac_insert_tax(
			array(
				'id'          => $id,
				'name'        => $name,
				'rate'        => $rate,
				'is_compound' => $is_compound,
				'description' => $desc,
				'status'      => $status,
			)
		);

		if ( is_wp_error( $tax ) ) {
			EAC()->flash->error( $tax->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Tax saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $tax->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}
}
