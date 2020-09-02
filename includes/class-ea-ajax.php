<?php
/**
 * EverAccounting  AJAX Event Handlers.
 *
 * @since       1.0.2
 * @package     EverAccounting
 * @class       EAccounting_Ajax
 */

namespace EverAccounting;

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
	 *
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

			// WC AJAX can be used for frontend ajax requests.
			add_action( 'wc_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}

		$ajax_events = array(
			'item_status_update',
			'get_currency',
			'dropdown_search',
			'edit_currency',
			'get_account',
			'edit_account',
			'edit_category',
			'edit_contact',
			'edit_payment',
			'edit_revenue',
			'edit_transfer',
			'do_ajax_export',
		);

		foreach ( $ajax_events as $ajax_event ) {
			add_action( 'wp_ajax_eaccounting_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}
	}

	/**
	 * Item status updater.
	 *
	 * @since 1.0.2
	 */
	public static function item_status_update() {
		check_ajax_referer( 'ea_status_update', 'nonce' );
		$object_id   = ! empty( $_REQUEST['objectid'] ) ? absint( $_REQUEST['objectid'] ) : null;
		$object_type = ! empty( $_REQUEST['objecttype'] ) ? eaccounting_clean( $_REQUEST['objecttype'] ) : '';
		$enabled     = isset( $_REQUEST['enabled'] ) ? absint( $_REQUEST['enabled'] ) : 0;

		if ( empty( $object_id ) || empty( $object_type ) ) {
			wp_send_json_error( [ 'message' => __( 'No object type to update status', 'wp-ever-accounting' ) ] );
		}
		$result = new \WP_Error( 'invalid_object', __( 'Invalid object type.', 'wp-ever-accounting' ) );
		switch ( $object_type ) {
			case 'currency':
				$result = eaccounting_insert_currency( [
					'id'      => $object_id,
					'enabled' => $enabled
				] );
				break;
			case 'category':
				$result = eaccounting_insert_category( [
					'id'      => $object_id,
					'enabled' => $enabled
				] );
				break;
			case 'account':
				$result = eaccounting_insert_account( [
					'id'      => $object_id,
					'enabled' => $enabled
				] );
				break;
			case 'customer':
			case 'vendor':
				$result = eaccounting_insert_contact( [
					'id'      => $object_id,
					'enabled' => $enabled
				] );
				break;
			default:
				/**
				 * Hook into this for any custom object handling
				 *
				 * @var int     $object_id ID of the object.
				 * @var boolean $enabled   status of the object.
				 */
				do_action( 'eaccounting_item_status_update_' . $object_type, $object_id, $enabled );
		}

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
			exit();
		}

		wp_send_json_success( [ 'message' => $result->is_enabled() ? sprintf( __( '%s enabled!', 'wp-ever-accounting' ), ucfirst( $object_type ) ) : sprintf( __( '%s disabled!', 'wp-ever-accounting' ), ucfirst( $object_type ) ) ] );
	}


	/**
	 * handle dropdown search.
	 *
	 * @since 1.0.2
	 */
	public static function dropdown_search() {
		check_ajax_referer( 'ea-dropdown-search', 'nonce' );
		$search  = isset( $_REQUEST['search'] ) ? eaccounting_clean( $_REQUEST['search'] ) : '';
		$page    = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
		$results = array();
		$type    = isset( $_REQUEST['type'] ) ? eaccounting_clean( $_REQUEST['type'] ) : '';

		switch ( $type ) {
			case 'currency':
				$results = Query_Currency::init()->where( [ 'search' => $search ] )->select( 'code as id, CONCAT (name,"(", symbol, ")") as text, rate ' )->where( 'enabled', 1 )->get();
				break;

			case 'account':
				$results = Query_Account::init()->where( [ 'search' => $search ] )->select( 'id, CONCAT(name," (", currency_code, ")") as text' )->where( 'enabled', 1 )->get();
				break;

			case 'customer':
				$results = Query_Contact::init()->where( [ 'search' => $search ] )->typeCustomer()->select( 'id, name as text' )->where( 'enabled', 1 )->get();
				break;

			case 'vendor':
				$results = Query_Contact::init()->where( [ 'search' => $search ] )->typeVendor()->select( 'id, name as text' )->where( 'enabled', 1 )->get();
				break;

			case 'expense_category':
				$results = Query_Category::init()->where( [ 'search' => $search ] )->where( 'type', 'expense' )->select( 'id, name as text' )->where( 'enabled', 1 )->get();
				break;

			case 'income_category':
				$results = Query_Category::init()->where( [ 'search' => $search ] )->where( 'type', 'income' )->select( 'id, name as text' )->where( 'enabled', 1 )->get();
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
					'more' => false
				)
			)
		);
	}

	/**
	 * Get currency data.
	 *
	 * @since 1.0.2
	 */
	public static function get_currency() {
		check_ajax_referer( 'ea_get_currency', '_wpnonce' );
		$posted = eaccounting_clean( $_REQUEST );
		$code   = ! empty( $posted['code'] ) ? $posted['code'] : false;
		if ( ! $code ) {
			wp_send_json_error( [
				'message' => __( 'No code received', 'wp-ever-accounting' ),
			] );
		}
		$currency = eaccounting_get_currency( $code );
		if ( empty( $currency ) || is_wp_error( $currency ) ) {
			wp_send_json_error( [
				'message' => __( 'Could not find the currency', 'wp-ever-accounting' ),
			] );
		}

		wp_send_json_success( $currency->get_data() );
	}

	/**
	 * Handle ajax action of creating/updating currencies.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_currency() {
		check_ajax_referer( 'ea_edit_currency', '_wpnonce' );
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

	/**
	 * Handle ajax action of creating/updating account.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_account() {
		check_ajax_referer( 'ea_edit_account', '_wpnonce' );
		$posted  = eaccounting_clean( $_REQUEST );
		$created = eaccounting_insert_account( $posted );
		if ( is_wp_error( $created ) ) {
			wp_send_json_error( [
				'message' => $created->get_error_message()
			] );
		}

		$message  = __( 'Account updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Account created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( [ 'action' ], eaccounting_clean( $_REQUEST['_wp_http_referer'] ) );
		}

		wp_send_json_success( [
			'message'  => $message,
			'redirect' => $redirect,
			'item'     => $created->get_data()
		] );

		wp_die();
	}


	/**
	 * Handle ajax action of creating/updating account.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function get_account() {
		check_ajax_referer( 'ea_get_account', '_wpnonce' );
		$id      = empty( $_REQUEST['id'] ) ? null : absint( $_REQUEST['id'] );
		$account = eaccounting_get_account( $id );
		if ( $account ) {
			wp_send_json_success( $account->get_data() );
			wp_die();
		}

		wp_send_json_error( [] );

		wp_die();
	}

	/**
	 * Handle ajax action of creating/updating account.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_category() {
		self::verify_nonce( 'ea_edit_category' );

		$posted  = eaccounting_clean( $_REQUEST );
		$created = eaccounting_insert_category( $posted );
		if ( is_wp_error( $created ) ) {
			wp_send_json_error( [
				'message' => $created->get_error_message()
			] );
		}

		$message  = __( 'Category updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Category created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( [ 'action' ], eaccounting_clean( $_REQUEST['_wp_http_referer'] ) );
		}

		wp_send_json_success( [
			'message'  => $message,
			'redirect' => $redirect,
			'item'     => $created->get_data()
		] );

		wp_die();
	}


	/**
	 * Handle ajax action of creating/updating account.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_contact() {
		self::verify_nonce( 'ea_edit_contact' );

		$posted = eaccounting_clean( $_REQUEST );

		$created = eaccounting_insert_contact( $posted );
		if ( is_wp_error( $created ) || ! $created->exists() ) {
			wp_send_json_error( [
				'message' => $created->get_error_message()
			] );
		}

		$message  = __( 'Contact updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Contact created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( [ 'action' ], eaccounting_clean( $_REQUEST['_wp_http_referer'] ) );
		}

		wp_send_json_success( [
			'message'  => $message,
			'redirect' => $redirect,
			'item'     => $created->get_data()
		] );

		wp_die();
	}

	/**
	 * Handle ajax action of creating/updating payment.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_payment() {
		self::verify_nonce( 'ea_edit_payment' );
		$posted = eaccounting_clean( $_REQUEST );

		$posted['type'] = 'expense';
		$created        = eaccounting_insert_transaction( $posted );
		if ( is_wp_error( $created ) || ! $created->exists() ) {
			wp_send_json_error( [
				'message' => $created->get_error_message()
			] );
		}

		$message  = __( 'Payment updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Payment created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( [ 'action' ], eaccounting_clean( $_REQUEST['_wp_http_referer'] ) );
		}

		wp_send_json_success( [
			'message'  => $message,
			'redirect' => $redirect,
			'item'     => $created->get_data()
		] );

		wp_die();
	}

	/**
	 * Handle ajax action of creating/updating revenue.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_revenue() {
		self::verify_nonce( 'ea_edit_revenue' );
		$posted = eaccounting_clean( $_REQUEST );

		$posted['type'] = 'income';
		$created        = eaccounting_insert_transaction( $posted );
		if ( is_wp_error( $created ) || ! $created->exists() ) {
			wp_send_json_error( [
				'message' => $created->get_error_message()
			] );
		}

		$message  = __( 'Revenue updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Revenue created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( [ 'action' ], eaccounting_clean( $_REQUEST['_wp_http_referer'] ) );
		}

		wp_send_json_success( [
			'message'  => $message,
			'redirect' => $redirect,
			'item'     => $created->get_data()
		] );

		wp_die();
	}

	/**
	 * Handle ajax action of creating/updating transfer.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_transfer() {
		self::verify_nonce( 'ea_edit_transfer' );
		$posted = eaccounting_clean( $_REQUEST );

		$created = eaccounting_insert_transfer( $posted );
		if ( is_wp_error( $created ) || ! $created->exists() ) {
			wp_send_json_error( [
				'message' => $created->get_error_message()
			] );
		}

		$message  = __( 'Transfer updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Transfer created successfully!', 'wp-ever-accounting' );
			$redirect = remove_query_arg( [ 'action' ], eaccounting_clean( $_REQUEST['_wp_http_referer'] ) );
		}

		wp_send_json_success( [
			'message'  => $message,
			'redirect' => $redirect,
			'item'     => $created->get_data()
		] );

		wp_die();
	}

	public static function do_ajax_export() {
		if ( ! isset( $_REQUEST['type'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'Export type must be present.', 'wp-ever-accounting' )
			) );
		}

		$type = sanitize_key( $_REQUEST['type'] );

//		self::verify_nonce( "{$type}_exporter_nonce" );

		if ( empty( $type ) || false === $batch = eaccounting()->utils->batch->get( $type ) ) {
			wp_send_json_error( array(
				'message' => sprintf( __( '%s is an invalid export type.', 'wp-ever-accounting' ), esc_html( $type ) )
			) );
		}

		$class      = isset( $batch['class'] ) ? $batch['class'] : '';
		$class_file = isset( $batch['file'] ) ? $batch['file'] : '';

		if ( empty( $class_file ) ) {
			wp_send_json_error( array(
				'message' => sprintf( __( 'An invalid file path is registered for the %1$s handler.', 'wp-ever-accounting' ), "<code>{$type}</code>" )
			) );
		} else {
			require_once $class_file;
		}

		if ( empty( $class ) || ! class_exists( $class ) ) {
			wp_send_json_error( array(
				'error' => sprintf( __( '%1$s is an invalid exporter handler for the %2$s . Please try again.', 'wp-ever-accounting' ),
					"<code>{$class}</code>",
					"<code>{$type}</code>"
				)
			) );
		}

		/**
		 * @var $exporter \EverAccounting\Abstracts\CSV_Batch_Exporter
		 */
		$exporter = new $class();

		if ( ! $exporter->can_export() ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have enough privileges to export this.', 'wp-ever-accounting' )
			) );
		}

		$step = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 1;
		if ( ! empty( $_POST['columns'] ) ) {
			$exporter->set_columns_to_export( wp_unslash( $_POST['columns'] ) );
		}

		if ( ! empty( $_POST['filename'] ) ) {
			$exporter->set_filename( wp_unslash( $_POST['filename'] ) );
		}

		$exporter->process_step( $step );

		$query_args = apply_filters(
			'eaccounting_export_get_ajax_query_args',
			array(
				'nonce'    => wp_create_nonce( 'ea-download-file' ),
				'action'   => 'eaccounting_download_export_file',
				'filename' => $exporter->get_filename(),
				'page'     => 'ea-tools',
				'export'   => $type,
				'tab'      => 'export'
			)
		);

		if ( 100 <= $exporter->get_percent_complete() ) {
			$total = $exporter->get_total_exported();
			wp_send_json_success(
				array(
					'step'       => 'done',
					'percentage' => 100,
					'message'    => sprintf( __( 'Total %d items exported', 'wp-ever-accounting' ), $total ),
					'url'        => add_query_arg( $query_args, eaccounting_admin_url( array( 'page' => 'ea-tools', 'tab' => 'export' ) ) ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'step'       => ++ $step,
					'percentage' => $exporter->get_percent_complete(),
				)
			);
		}
	}


	public static function do_ajax_import() {
		self::verify_nonce( 'ea_ajax_import' );

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		if ( empty( $_FILES['ea-import-file'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing import file. Please provide an import file.', 'wp-ever-accounting' ), 'request' => $_REQUEST ) );
		}

		$accepted_mime_types = array(
			'text/csv',
			'text/comma-separated-values',
			'text/plain',
			'text/anytext',
			'text/*',
			'text/plain',
			'text/anytext',
			'text/*',
			'application/csv',
			'application/excel',
			'application/vnd.ms-excel',
			'application/vnd.msexcel',
		);
		if ( empty( $_FILES['ea-import-file']['type'] ) || ! in_array( strtolower( $_FILES['ea-import-file']['type'] ), $accepted_mime_types ) ) {
			wp_send_json_error( array( 'message' => __( 'The file you uploaded does not appear to be a CSV file.', 'wp-ever-accounting' ), 'request' => $_REQUEST ) );
		}

		if ( ! file_exists( $_FILES['ea-import-file']['tmp_name'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Something went wrong during the upload process, please try again.', 'wp-ever-accounting' ), 'request' => $_REQUEST ) );
		}

		// Let WordPress import the file. We will remove it after import is complete
		$import_file = wp_handle_upload( $_FILES['ea-import-file'], array( 'test_form' => false ) );

		if ( $import_file && empty( $import_file['error'] ) ) {

		}
		wp_send_json_error( array( 'message' => $import_file['error'] ) );
		exit();
	}


	/**
	 * Check permission
	 *
	 * since 1.0.2
	 */
	public static function check_permission() {
		if ( ! current_user_can( 'manage_options' ) ) {
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
	 */
	public static function verify_nonce( $action ) {
		$nonce = '';
		if ( isset( $_REQUEST['_ajax_nonce'] ) ) {
			$nonce = $_REQUEST['_ajax_nonce'];
		} elseif ( isset( $_REQUEST['_wpnonce'] ) ) {
			$nonce = $_REQUEST['_wpnonce'];
		}
		if ( false == wp_verify_nonce( $nonce, $action ) ) {
			wp_send_json_error( array( 'message' => __( 'Error: Cheatin&#8217; huh?.', 'wp-ever-accounting' ) ) );
			wp_die();
		}

	}
}

return new Ajax();
