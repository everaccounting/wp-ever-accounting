<?php
/**
 * EverAccounting  AJAX Event Handlers.
 *
 * @class   EAccounting_Ajax
 * @package EAccounting_Ajax/Classes
 */
defined( 'ABSPATH' ) || exit();

class EAccounting_Ajax {

	/**
	 * EAccounting_Ajax constructor.
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_ajax' ), 0 );
		self::add_ajax_events();
	}

	/**
	 * Set EA AJAX constant and headers.
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
	 * Send headers for WC Ajax Requests.
	 *
	 * @since 1.0.2
	 */
	private static function ajax_headers() {
		if ( ! headers_sent() ) {
			send_origin_headers();
			send_nosniff_header();
			wc_nocache_headers();
			header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
			header( 'X-Robots-Tag: noindex' );
			status_header( 200 );
		} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			headers_sent( $file, $line );
			trigger_error( "eaccounting_ajax_headers cannot set headers - headers already sent by {$file} on line {$line}", E_USER_NOTICE ); // @codingStandardsIgnoreLine
		}
	}

	/**
	 * Check for WC Ajax request and fire action.
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
	 */
	public static function add_ajax_events() {
		$ajax_events_nopriv = array();

		foreach ( $ajax_events_nopriv as $ajax_event ) {
			add_action( 'wp_ajax_eaccounting_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			add_action( 'wp_ajax_nopriv_eaccounting_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			// WC AJAX can be used for frontend ajax requests.
			add_action( 'wc_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}

		$ajax_events = array(
			'item_status_update',
			'get_currency',
			'dropdown_search',
			'edit_currency',
		);

		foreach ( $ajax_events as $ajax_event ) {
			add_action( 'wp_ajax_eaccounting_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}
	}

	public static function item_status_update() {
		check_ajax_referer( 'ea_status_update', 'nonce' );
		$object_id   = ! empty( $_REQUEST['objectid'] ) ? absint( $_REQUEST['objectid'] ) : null;
		$object_type = ! empty( $_REQUEST['objecttype'] ) ? eaccounting_clean( $_REQUEST['objecttype'] ) : '';
		$enabled     = isset( $_REQUEST['enabled'] ) ? absint( $_REQUEST['enabled'] ) : 0;

		if ( empty( $object_id ) || empty( $object_type ) ) {
			wp_send_json_error( [ 'message' => __( 'No object type to update status', 'wp-ever-accounting' ) ] );
		}
		$result = new WP_Error( 'invalid_object', __( 'Invalid object type.', 'wp-ever-accounting' ) );
		switch ( $object_type ) {
			case 'currency':
				$result = eaccounting_insert_currency( [
					'id'      => $object_id,
					'enabled' => $enabled
				] );
				break;
			default:
				do_action( 'eaccounting_item_status_update_' . $object_type, $object_id, $enabled );
		}

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
			exit();
		}

		wp_send_json_success( [ 'message' => $result->is_enabled() ? sprintf( __( '%s enabled!', 'wp-ever-accounting' ), ucfirst( $object_type ) ) : sprintf( __( '%s disabled!', 'wp-ever-accounting' ), ucfirst( $object_type ) ) ] );
	}

	public static function dropdown_search() {
		check_ajax_referer( 'dropdown-search', 'nonce' );
		$search  = isset( $_REQUEST['search'] ) ? eaccounting_clean( $_REQUEST['search'] ) : '';
		$page    = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
		$more    = false;
		$results = array();
		$type    = isset( $_REQUEST['type'] ) ? eaccounting_clean( $_REQUEST['type'] ) : '';

		switch ( $type ) {
			case 'currency_code':
				$results = eaccounting()->currencies->get_currencies( [ 'search' => $search ] )->select( 'code as id, CONCAT(name,"(", symbol, ")") as text, rate ' )->where( 'enabled', 1 )->get();
				break;

			default:
				do_action( 'eaccounting_dropdown_search_' . eaccounting_clean( $type ) );
				break;
		}

		wp_send_json(
			array(
				'page'       => $page,
				'results'    => $results,
				'pagination' => array(
					'more' => $more
				)
			)
		);
	}

	public static function get_currency() {
		$posted = eaccounting_clean( $_REQUEST );
		$code   = ! empty( $posted['code'] ) ? $posted['code'] : false;
		if ( ! $code ) {
			wp_send_json_error( [
				'message' => __( 'No code received', 'wp-ever-accounting' ),
			] );
		}
		$currency = eaccounting_get_currency_by_code($code);
		if(empty($currency) || is_wp_error($currency)){
			wp_send_json_error( [
				'message' => __( 'Could not find the currency', 'wp-ever-accounting' ),
			] );
		}

		wp_send_json_success($currency->toArray());
	}

	/**
	 * Handle ajax action of creating/updating currencies.
	 * @return void
	 * @since 1.0.2
	 */
	public static function edit_currency() {
		check_ajax_referer( 'edit_currency', '_wpnonce' );
		$posted  = eaccounting_clean( $_REQUEST );
		$created = eaccounting_insert_currency( $posted );
		if ( is_wp_error( $created ) ) {
			wp_send_json_error( [
				'message' => $created->get_error_message()
			] );
		}

		$message  = __( 'Currency updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Currency created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( [ 'action' ], eaccounting_clean( $_REQUEST['_wp_http_referer'] ) );
		}

		wp_send_json_success( [
			'message'  => $message,
			'redirect' => $redirect,
			'item'     => $created->get_data()
		] );

		wp_die();
	}

}

return new EAccounting_Ajax();
