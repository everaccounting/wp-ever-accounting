<?php
/**
 * EverAccounting  AJAX Event Handlers.
 *
 * @since       1.0.2
 * @package     EverAccounting
 * @class       EAccounting_Ajax
 */

namespace EverAccounting;


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
			'get_accounts',
			'get_customers',
			'get_vendors',
			'get_currencies',
			'get_income_categories',
			'get_expense_categories',
			'get_item_categories',
			'item_status_update',
			'get_currency',
			'get_account_currency',
			'get_country',
			'dropdown_search',
			'get_items',
			'edit_currency',
			'get_account',
			'edit_account',
			'edit_category',
			'edit_contact',
			'edit_vendor',
			'edit_customer',
			'edit_payment',
			'edit_revenue',
			'edit_transfer',
			'edit_invoice',
			'edit_item',
			'edit_tax',
			'invoice_calculate_totals',
			'add_invoice_payment',
			'upload_files',
		);

		foreach ( $ajax_events as $ajax_event ) {
			add_action( 'wp_ajax_eaccounting_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}
	}

	/**
	 * Get accounts.
	 *
	 * @since 1.1.0
	 */
	public static function get_accounts() {
		check_ajax_referer( 'ea_get_accounts', 'nonce' );
		$search = isset( $_REQUEST['search'] ) ? eaccounting_clean( $_REQUEST['search'] ) : '';
		$page   = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;

		return wp_send_json_success(
			eaccounting_get_accounts(
				array(
					'search' => $search,
					'page'   => $page,
					'return' => 'raw',
				)
			)
		);
	}

	/**
	 * Get customers.
	 *
	 * @since 1.1.0
	 */
	public static function get_customers() {
		check_ajax_referer( 'ea_get_customers', 'nonce' );
		$search = isset( $_REQUEST['search'] ) ? eaccounting_clean( $_REQUEST['search'] ) : '';
		$page   = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;

		return wp_send_json_success(
			eaccounting_get_customers(
				array(
					'search' => $search,
					'page'   => $page,
					'return' => 'raw',
				)
			)
		);
	}

	/**
	 * Get customers.
	 *
	 * @since 1.1.0
	 */
	public static function get_vendors() {
		check_ajax_referer( 'ea_get_vendors', 'nonce' );
		$search = isset( $_REQUEST['search'] ) ? eaccounting_clean( $_REQUEST['search'] ) : '';
		$page   = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;

		return wp_send_json_success(
			eaccounting_get_vendors(
				array(
					'search' => $search,
					'page'   => $page,
					'return' => 'raw',
				)
			)
		);
	}

	/**
	 * Get currencies.
	 *
	 * @since 1.1.0
	 */
	public static function get_currencies() {
		check_ajax_referer( 'ea_get_currencies', 'nonce' );
		$search = isset( $_REQUEST['search'] ) ? eaccounting_clean( $_REQUEST['search'] ) : '';
		$page   = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;

		return wp_send_json_success(
			eaccounting_get_currencies(
				array(
					'search' => $search,
					'page'   => $page,
					'return' => 'raw',
				)
			)
		);
	}

	/**
	 * Get income categories.
	 *
	 * @since 1.1.0
	 */
	public static function get_income_categories() {
		check_ajax_referer( 'ea_categories', 'nonce' );
		$search = isset( $_REQUEST['search'] ) ? eaccounting_clean( $_REQUEST['search'] ) : '';
		$page   = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;

		return wp_send_json_success(
			eaccounting_get_categories(
				array(
					'search' => $search,
					'type'   => 'income',
					'page'   => $page,
					'return' => 'raw',
				)
			)
		);
	}

	/**
	 * Get income categories.
	 *
	 * @since 1.1.0
	 */
	public static function get_expense_categories() {
		check_ajax_referer( 'ea_categories', 'nonce' );
		$search = isset( $_REQUEST['search'] ) ? eaccounting_clean( $_REQUEST['search'] ) : '';
		$page   = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;

		return wp_send_json_success(
			eaccounting_get_categories(
				array(
					'search' => $search,
					'type'   => 'expense',
					'page'   => $page,
					'return' => 'raw',
				)
			)
		);
	}

	/**
	 * Get income categories.
	 *
	 * @since 1.1.0
	 */
	public static function get_item_categories() {
		check_ajax_referer( 'ea_categories', 'nonce' );
		$search = isset( $_REQUEST['search'] ) ? eaccounting_clean( $_REQUEST['search'] ) : '';
		$page   = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;

		return wp_send_json_success(
			eaccounting_get_categories(
				array(
					'search' => $search,
					'type'   => 'item',
					'page'   => $page,
					'return' => 'raw',
				)
			)
		);
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
			wp_send_json_error( array( 'message' => __( 'No object type to update status', 'wp-ever-accounting' ) ) );
		}
		$result = new \WP_Error( 'invalid_object', __( 'Invalid object type.', 'wp-ever-accounting' ) );
		switch ( $object_type ) {
			case 'currency':
				self::check_permission( 'ea_manage_currency' );
				$result = eaccounting_insert_currency(
					array(
						'id'      => $object_id,
						'enabled' => $enabled,
					)
				);
				break;
			case 'category':
				self::check_permission( 'ea_manage_category' );
				$result = eaccounting_insert_category(
					array(
						'id'      => $object_id,
						'enabled' => $enabled,
					)
				);
				break;
			case 'account':
				self::check_permission( 'ea_manage_account' );
				$result = eaccounting_insert_account(
					array(
						'id'      => $object_id,
						'enabled' => $enabled,
					)
				);
				break;
			case 'customer':
				self::check_permission( 'ea_manage_customer' );
				$result = eaccounting_insert_customer(
					array(
						'id'      => $object_id,
						'enabled' => $enabled,
					)
				);
				break;
			case 'vendor':
				self::check_permission( 'ea_manage_vendor' );
				$result = eaccounting_insert_vendor(
					array(
						'id'      => $object_id,
						'enabled' => $enabled,
					)
				);
				break;
			case 'tax':
				//todo check and implement permission
				//self::check_permission( 'ea_manage_tax' );
				$result = eaccounting_insert_tax(
					array(
						'id'      => $object_id,
						'enabled' => $enabled,
					)
				);
				break;
			case 'item':
				//todo check and implement permission
				//self::check_permission( 'ea_manage_item' );
				$result = eaccounting_insert_item(
					array(
						'id'      => $object_id,
						'enabled' => $enabled,
					)
				);
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
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			exit();
		}

		wp_send_json_success( array( 'message' => $result->is_enabled() ? sprintf( __( '%s enabled!', 'wp-ever-accounting' ), ucfirst( $object_type ) ) : sprintf( __( '%s disabled!', 'wp-ever-accounting' ), ucfirst( $object_type ) ) ) );
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
			case 'tax':
				$items = eaccounting_get_taxes(
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

	public static function get_items() {
		check_ajax_referer( 'ea_get_items', 'nonce' );
		self::check_permission( 'manage_eaccounting' );
		$search = isset( $_REQUEST['search'] ) ? eaccounting_clean( $_REQUEST['search'] ) : '';
		return wp_send_json_success(
			eaccounting_get_items(
				array(
					'search' => $search,
					'return' => 'raw',
					'status' => 'active',
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
		self::check_permission( 'manage_eaccounting' );
		$posted = eaccounting_clean( $_REQUEST );
		$code   = ! empty( $posted['code'] ) ? $posted['code'] : false;
		if ( ! $code ) {
			wp_send_json_error(
				array(
					'message' => __( 'No code received', 'wp-ever-accounting' ),
				)
			);
		}
		$currency = eaccounting_get_currency( $code );
		if ( empty( $currency ) || is_wp_error( $currency ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Could not find the currency', 'wp-ever-accounting' ),
				)
			);
		}

		wp_send_json_success( $currency->get_data() );
	}

	/**
	 * Get currency data.
	 *
	 * @since 1.0.2
	 */
	public static function get_account_currency() {
		check_ajax_referer( 'ea_get_currency', '_wpnonce' );
		self::check_permission( 'manage_eaccounting' );
		$posted     = eaccounting_clean( $_REQUEST );
		$account_id = ! empty( $posted['account_id'] ) ? $posted['account_id'] : false;
		if ( ! $account_id ) {
			wp_send_json_error(
				array(
					'message' => __( 'No account id received', 'wp-ever-accounting' ),
				)
			);
		}
		$account = eaccounting_get_account( $account_id );
		if ( empty( $account ) || is_wp_error( $account ) || empty( $account->get_currency()->exists() ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Could not find the currency', 'wp-ever-accounting' ),
				)
			);
		}

		wp_send_json_success( $account->get_currency()->get_data() );
	}

	/**
	 * Handle ajax action of creating/updating currencies.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_currency() {
		check_ajax_referer( 'ea_edit_currency', '_wpnonce' );
		self::check_permission( 'ea_manage_currency' );
		$posted  = eaccounting_clean( $_REQUEST );
		$created = eaccounting_insert_currency( $posted );
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Currency updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Currency created successfully!', 'wp-ever-accounting' );
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
	 * Handle ajax action of creating/updating account.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_account() {
		check_ajax_referer( 'ea_edit_account', '_wpnonce' );
		self::check_permission( 'ea_manage_account' );
		$posted  = eaccounting_clean( $_REQUEST );
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
	 * Handle ajax action of creating/updating account.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function get_account() {
		check_ajax_referer( 'ea_get_account', '_wpnonce' );
		self::check_permission( 'manage_eaccounting' );
		$id      = empty( $_REQUEST['id'] ) ? null : absint( $_REQUEST['id'] );
		$account = eaccounting_get_account( $id );
		if ( $account ) {
			wp_send_json_success( $account->get_data() );
			wp_die();
		}

		wp_send_json_error( array() );

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
		self::check_permission( 'ea_manage_category' );
		$posted  = eaccounting_clean( $_REQUEST );
		$created = eaccounting_insert_category( $posted );
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Category updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Category created successfully!', 'wp-ever-accounting' );
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
	 * Handle ajax action of creating/updating account.
	 *
	 * @since 1.0.2
	 * @return void
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
	 * Handle ajax action of creating/updating vendor.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public static function edit_vendor() {
		self::verify_nonce( 'ea_edit_vendor' );
		self::check_permission( 'ea_manage_customer' );
		$posted = eaccounting_clean( $_REQUEST );

		$created = eaccounting_insert_vendor( $posted );
		if ( is_wp_error( $created ) || ! $created->exists() ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Vendor updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Vendor created successfully!', 'wp-ever-accounting' );
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
	 * Handle ajax action of creating/updating customer.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_customer() {
		self::verify_nonce( 'ea_edit_customer' );
		self::check_permission( 'ea_manage_customer' );
		$posted = eaccounting_clean( $_REQUEST );

		$created = eaccounting_insert_customer( $posted );
		if ( is_wp_error( $created ) || ! $created->exists() ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Customer updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Customer created successfully!', 'wp-ever-accounting' );
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
	 * Handle ajax action of creating/updating payment.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_payment() {
		self::verify_nonce( 'ea_edit_payment' );
		self::check_permission( 'ea_manage_payment' );
		$posted = eaccounting_clean( $_REQUEST );

		$created = eaccounting_insert_expense( $posted );
		if ( is_wp_error( $created ) || ! $created->exists() ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Payment updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Payment created successfully!', 'wp-ever-accounting' );
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
	 * Handle ajax action of creating/updating revenue.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_revenue() {
		self::verify_nonce( 'ea_edit_revenue' );
		self::check_permission( 'ea_manage_revenue' );
		$posted = eaccounting_clean( $_REQUEST );

		$created = eaccounting_insert_income( $posted );
		if ( is_wp_error( $created ) || ! $created->exists() ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Revenue updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Revenue created successfully!', 'wp-ever-accounting' );
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
	 * Handle ajax action of creating/updating transfer.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function edit_transfer() {
		self::verify_nonce( 'ea_edit_transfer' );
		self::check_permission( 'ea_manage_transfer' );
		$posted = eaccounting_clean( $_REQUEST );

		$created = eaccounting_insert_transfer( $posted );
		if ( is_wp_error( $created ) || ! $created->exists() ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Transfer updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Transfer created successfully!', 'wp-ever-accounting' );
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
	 * @since 1.1.0
	 * @return void
	 */
	public static function edit_invoice1() {
		self::verify_nonce( 'ea_edit_invoice' );
		//todo need to add and implement permission
		//self::check_permission( 'ea_manage_category' );
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

	public static function add_invoice_payment() {
		//self::verify_nonce( 'ea_edit_transfer' );
		//      self::check_permission( 'ea_add_invoice_payment' );
		$posted = eaccounting_clean( $_REQUEST );
		if ( empty( $posted['invoice_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invoice ID is empty', 'wp-ever-accounting' ),
				)
			);
		}

		try {
			$invoice = new Invoice( $posted['invoice_id'] );
			if ( ! $invoice->exists() ) {
				throw new Exception( 'invalid_invoice_id', __( 'Invalid Invoice Item', 'wp-ever-accounting' ) );
			}


			$income = new Income();
			$income->set_props( $posted );
			$income->set_document_id( $posted['invoice_id'] );
			$income->save();
			wp_send_json_success(
				array(
					'message' => __( 'Invoice Payment saved', 'wp-ever-accounting' ),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error(
				array(
					'message' => $e->getMessage(),
				)
			);
		}
	}

	/**
	 * Handle ajax action of creating/updating item.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public static function edit_item() {
		self::verify_nonce( 'ea_edit_item' );
		//todo check permission for item edit
		self::check_permission( 'ea_manage_category' );
		$posted  = eaccounting_clean( $_REQUEST );
		$created = eaccounting_insert_item( $posted );
		if ( is_wp_error( $created ) ) {
			wp_send_json_error(
				array(
					'message' => $created->get_error_message(),
				)
			);
		}

		$message  = __( 'Item updated successfully!', 'wp-ever-accounting' );
		$update   = empty( $posted['id'] ) ? false : true;
		$redirect = '';
		if ( ! $update ) {
			$message  = __( 'Item created successfully!', 'wp-ever-accounting' );
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
	 * @since 1.1.0
	 * @return void
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

	public static function invoice_calculate_totals() {
		//self::check_permission( 'eaccounting_edit_invoice' );
		$posted  = eaccounting_clean( $_REQUEST );
		$posted  = wp_parse_args( $posted, array( 'id' => null ) );
		$invoice = new Invoice( $posted['id'] );
		$invoice->set_props( $posted );
		$totals = $invoice->calculate_total();
		wp_send_json_success(
			array(
				'lines_html'  => eaccounting_get_admin_template_html( 'invoice/line-items', array( 'invoice' => $invoice ) ),
				'totals_html' => eaccounting_get_admin_template_html( 'invoice/totals', array( 'invoice' => $invoice ) ),
				'line'        => array_map( 'strval', $invoice->get_line_items() ),
				'totals'      => $totals,
			)
		);
	}

	public static function edit_invoice() {
		$posted  = eaccounting_clean( $_REQUEST );
		$posted  = wp_parse_args( $posted, array( 'id' => null ) );
		$invoice = new Invoice( $posted['id'] );
		$invoice->set_props( $posted );
		$invoice->save();
		$totals   = $invoice->calculate_total();
		$redirect = add_query_arg( array( 'action' => 'view' ), eaccounting_clean( $_REQUEST['_wp_http_referer'] ) );
		wp_send_json_success(
			array(
				'lines_html'  => eaccounting_get_admin_template_html( 'invoice/line-items', array( 'invoice' => $invoice ) ),
				'totals_html' => eaccounting_get_admin_template_html( 'invoice/totals', array( 'invoice' => $invoice ) ),
				'line'        => array_map( 'strval', $invoice->get_line_items() ),
				'redirect'    => $redirect,
				'totals'      => $totals,
			)
		);
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
