<?php
/**
 * EverAccounting  AJAX Event Handlers.
 *
 * @since       1.0.2
 * @package     EverAccounting
 * @class       EAccounting_Ajax
 */

namespace EverAccounting;


use EverAccounting\Models\Account;
use EverAccounting\Models\Income;
use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Ajax
 *
 * @since 1.0.2
 */
class Ajax {

	/**
	 * EAccounting_Ajax constructor.
	 *
	 * @since 1.0.2
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_ajax' ), 0 );
		self::add_ajax_events();
	}

	/**
	 * Set EA AJAX constant and headers.
	 *
	 * @since 1.0.2
	 */
	public static function define_ajax() {
		// phpcs:disable
		if ( ! empty( $_GET['ea-ajax'] ) ) {
			eaccounting_maybe_define_constant( 'DOING_AJAX', true );
			eaccounting_maybe_define_constant( 'EACCOUNTING_DOING_AJAX', true );
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
			}
			$GLOBALS['wpdb']->hide_errors();
		}
		// phpcs:enable
	}


	/**
	 * Send headers for EverAccounting Ajax Requests.
	 *
	 * @since 1.0.2
	 */
	private static function ajax_headers() {
		if ( ! headers_sent() ) {
			send_origin_headers();
			send_nosniff_header();
			header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
			header( 'X-Robots-Tag: noindex' );
			status_header( 200 );
		} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			headers_sent( $file, $line );
			trigger_error( "eaccounting_ajax_headers cannot set headers - headers already sent by {$file} on line {$line}", E_USER_NOTICE ); // @codingStandardsIgnoreLine
		}
	}

	/**
	 * Check for EverAccounting Ajax request and fire action.
	 *
	 * @since 1.0.2
	 */
	public static function do_ajax() {
		global $wp_query;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['ea-ajax'] ) ) {
			$wp_query->set( 'ea-ajax', sanitize_text_field( wp_unslash( $_GET['ea-ajax'] ) ) );
		}

		$action = $wp_query->get( 'ea-ajax' );

		if ( $action ) {
			self::ajax_headers();
			$action = sanitize_text_field( $action );
			do_action( 'eaccounting_ajax_' . $action );
			wp_die();
		}
		// phpcs:enable
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 *
	 * @since 1.0.2
	 */
	public static function add_ajax_events() {
		$ajax_events_nopriv = array();

		foreach ( $ajax_events_nopriv as $ajax_event ) {
			add_action( 'wp_ajax_eaccounting_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			add_action( 'wp_ajax_nopriv_eaccounting_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			// EverAccounting AJAX can be used for frontend ajax requests.
			add_action( 'eaccounting_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}

		$ajax_events = array(
			//'get_accounts',
			//'get_customers',
			//'get_vendors',
			//'get_currencies',
			//'get_income_categories',
			//'get_expense_categories',
			//'get_item_categories',
			//'item_status_update',
			//'get_currency',
			//'get_account_currency',
			'get_country', // not needed
			'dropdown_search', //not needed
			//'get_items',
			//'edit_currency',
			//'get_account',
			//'edit_account',
			//'edit_category',
			'edit_contact',//not needed
			//'edit_vendor',
			//'edit_customer',
			//'edit_payment',
			//'edit_revenue',
			//'edit_transfer',
			//'edit_invoice',
			//'edit_item',
			'edit_tax', //not needed
			//'invoice_recalculate',
			//'add_invoice_payment',
			'upload_files', //not needed
		);

		foreach ( $ajax_events as $ajax_event ) {
			add_action( 'wp_ajax_eaccounting_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}
	}


	/**
	 * handle dropdown search.
	 *
	 * @since 1.0.2
	 */
	public static function dropdown_search() {
		check_ajax_referer( 'ea-dropdown-search', 'nonce' );
		self::check_permission( 'manage_eaccounting' );
		$search  = isset( $_REQUEST['search'] ) ? eaccounting_clean( $_REQUEST['search'] ) : '';
		$page    = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
		$results = array();
		$type    = isset( $_REQUEST['type'] ) ? eaccounting_clean( $_REQUEST['type'] ) : '';

		switch ( $type ) {
			case 'currency':
				$currencies = eaccounting_get_currencies(
					array(
						'search' => $search,
						'paged'  => $page,
						'status' => 'active',
						'return' => 'raw',
					)
				);
				foreach ( $currencies as $currency ) {
					$results[] = array(
						'text' => "$currency->name($currency->symbol)",
						'id'   => $currency->code,
					);
				}

				break;

			case 'account':
				$items = eaccounting_get_accounts(
					array(
						'search' => $search,
						'paged'  => $page,
						'status' => 'active',
						'return' => 'raw',
					)
				);
				foreach ( $items as $item ) {
					$results[] = array(
						'text' => "$item->name($item->currency_code)",
						'id'   => $item->id,
					);
				}
				break;

			case 'customer':
				$items = eaccounting_get_customers(
					array(
						'search' => $search,
						'paged'  => $page,
						'status' => 'active',
						'return' => 'raw',
					)
				);
				foreach ( $items as $item ) {
					$results[] = array(
						'text' => "$item->name",
						'id'   => $item->id,
					);
				}
				break;

			case 'vendor':
				$items = eaccounting_get_vendors(
					array(
						'search' => $search,
						'paged'  => $page,
						'status' => 'active',
						'return' => 'raw',
					)
				);
				foreach ( $items as $item ) {
					$results[] = array(
						'text' => "$item->name",
						'id'   => $item->id,
					);
				}
				break;
			case 'expense_category':
				$items = eaccounting_get_categories(
					array(
						'search' => $search,
						'paged'  => $page,
						'type'   => 'expense',
						'status' => 'active',
						'return' => 'raw',
					)
				);
				foreach ( $items as $item ) {
					$results[] = array(
						'text' => "$item->name",
						'id'   => $item->id,
					);
				}
				break;

			case 'income_category':
				$items = eaccounting_get_categories(
					array(
						'search' => $search,
						'paged'  => $page,
						'type'   => 'income',
						'status' => 'active',
						'return' => 'raw',
					)
				);
				foreach ( $items as $item ) {
					$results[] = array(
						'text' => "$item->name",
						'id'   => $item->id,
					);
				}
				break;

			default:
				do_action( 'eaccounting_dropdown_search_' . eaccounting_clean( $type ), $search, $page );
				break;
		}

		wp_send_json(
			array(
				'page'       => $page,
				'results'    => $results,
				'pagination' => array(
					'more' => false,
				),
			)
		);
	}



	/**
	 * Handle ajax action of creating/updating account.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public static function edit_contact() {
		self::verify_nonce( 'ea_edit_contact' );
		self::check_permission( 'ea_manage_customer' );
		$posted = eaccounting_clean( $_REQUEST );

		$created = eaccounting_insert_contact( $posted );
		if ( is_wp_error( $created ) || ! $created->exists() ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Contact updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Contact created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eaccounting_clean( $_REQUEST['_wp_http_referer'] ) );
		}

		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		wp_die();
	}


	/**
	 * Handle ajax action of creating/updating invoice.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public static function edit_invoice1() {
		self::verify_nonce( 'ea_edit_invoice' );
		self::check_permission( 'ea_manage_invoice' );
		$posted  = eaccounting_clean( $_REQUEST );
		$created = eaccounting_insert_invoice( $posted );
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Invoice updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Invoice created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eaccounting_clean( $_REQUEST['_wp_http_referer'] ) );
		}
		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		wp_die();
	}


	/**
	 * Handle ajax action of creating/updating tax.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public static function edit_tax() {
		self::verify_nonce( 'ea_edit_tax' );
		//todo check permission for taxes
		self::check_permission( 'ea_manage_category' );
		$posted  = eaccounting_clean( $_REQUEST );
		$created = eaccounting_insert_tax( $posted );
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Tax updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Tax created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( array( 'action' ), eaccounting_clean( $_REQUEST['_wp_http_referer'] ) );
		}
		wp_send_json_success(
			array(
				'message'  => $message,
				'redirect' => $redirect,
				'item'     => $created->get_data(),
			)
		);

		wp_die();
	}

	public static function upload_files() {
		self::verify_nonce( 'eaccounting_file_upload' );
		self::check_permission( 'manage_eaccounting' );

		if ( ! empty( $_FILES['upload'] ) ) {
			$file = eaccounting_upload_file( $_FILES['upload'] );
			if ( is_wp_error( $file ) ) {
				wp_send_json_error( array( 'message' => $file->get_error_message() ) );
			}

			wp_send_json_success( $file );
		}
	}

	/**
	 * Check permission
	 *
	 * since 1.0.2
	 *
	 * @param string $cap
	 */
	public static function check_permission( $cap = 'manage_eaccounting' ) {
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error( array( 'message' => __( 'Error: You are not allowed to do this.', 'wp-ever-accounting' ) ) );
		}
	}

	/**
	 * Verify our ajax nonce.
	 *
	 * @param $action
	 *
	 * @param $action
	 *
	 * @since 1.0.2
	 *
	 */
	public static function verify_nonce( $action ) {
		$nonce = '';
		if ( isset( $_REQUEST['_ajax_nonce'] ) ) {
			$nonce = $_REQUEST['_ajax_nonce'];
		} elseif ( isset( $_REQUEST['_wpnonce'] ) ) {
			$nonce = $_REQUEST['_wpnonce'];
		} elseif ( isset( $_REQUEST['nonce'] ) ) {
			$nonce = $_REQUEST['nonce'];
		}
		if ( false === wp_verify_nonce( $nonce, $action ) ) {
			wp_send_json_error( array( 'message' => __( 'Error: Cheatin&#8217; huh?.', 'wp-ever-accounting' ) ) );
			wp_die();
		}

	}
}

return new Ajax();
