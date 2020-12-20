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
			'add_invoice_payment',
			'edit_account',
		);

		foreach ( $ajax_events as $ajax_event ) {
			add_action( 'wp_ajax_eaccounting_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}
	}

	/**
	 * Add payment to invoice.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public static function add_invoice_payment() {
		self::verify_nonce( 'ea_add_invoice_payment' );
		self::check_permission( 'ea_manage_invoice' );
		$posted = eaccounting_clean( wp_unslash( $_REQUEST ) );

		try {
			$invoice = new Invoice( $posted['invoice_id'] );
			if ( ! $invoice->exists() ) {
				throw new \Exception( __( 'Invalid Invoice Item', 'wp-ever-accounting' ) );
			}
			$invoice->add_payment( $posted );
			wp_send_json_success(
				array(
					'message' => __( 'Invoice Payment saved', 'wp-ever-accounting' ),
				)
			);
		} catch ( \Exception $e ) {
			wp_send_json_error(
				array(
					'message' => $e->getMessage(),
				)
			);
		}
	}

	/**
	 * Handle ajax action of creating/updating account.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_account() {
		self::verify_nonce( 'ea_edit_account' );
		self::check_permission( 'ea_manage_account' );
		$posted  = eaccounting_clean( wp_unslash( $_REQUEST ) );
		$created = eaccounting_insert_account( $posted );
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Account updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Account created successfully!', 'wp-ever-accounting' );
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
	 * @since 1.0.2
	 *
	 * @param $action
	 *
	 * @param $action
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
