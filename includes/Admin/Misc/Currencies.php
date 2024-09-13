<?php

namespace EverAccounting\Admin\Misc;

use EverAccounting\Models\Currency;
use EverAccounting\Utilities\I18n;

defined( 'ABSPATH' ) || exit;


/**
 * Currencies class.
 *
 * @since 3.0.0
 * @package EverAccounting\Admin
 */
class Currencies {

	/**
	 * Currencies constructor.
	 */
	public function __construct() {
		add_filter( 'eac_misc_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'load_eac_misc_page_currencies_index', array( __CLASS__, 'setup_table' ) );
		add_action( 'eac_misc_page_currencies_index', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_misc_page_currencies_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_misc_page_currencies_edit', array( __CLASS__, 'render_edit' ) );
		add_action( 'admin_post_eac_add_currency', array( __CLASS__, 'handle_add' ) );
		add_action( 'admin_post_eac_edit_currency', array( __CLASS__, 'handle_edit_currency' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['currencies'] = __( 'Currencies', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * Set screen option.
	 *
	 * @param mixed  $status Status.
	 * @param string $option Option.
	 * @param mixed  $value Value.
	 *
	 * @since 3.0.0
	 * @return mixed
	 */
	public static function set_screen_option( $status, $option, $value ) {
		global $list_table;
		if ( "eac_{$list_table->_args['plural']}_per_page" === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * setup currencies list.
	 *
	 * @since 3.0.0
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new Tables\CurrenciesTable();
		$list_table->prepare_items();
		$screen->add_option( 'per_page', array(
			'label'   => __( 'Number of currencies per page:', 'wp-ever-accounting' ),
			'default' => 20,
			'option'  => "eac_{$list_table->_args['plural']}_per_page",
		) );
	}

	/**
	 * Render currencies table.
	 *
	 * @since 3.0.0
	 */
	public static function render_table() {
		global $list_table;
		include __DIR__ . '/views/currencies/table.php';
	}

	/**
	 * Render add category form.
	 *
	 * @since 3.0.0
	 */
	public static function render_add() {
		$currency = new Currency();
		include __DIR__ . '/views/currencies/add.php';
	}

	/**
	 * Render edit category form.
	 *
	 * @since 3.0.0
	 */
	public static function render_edit() {
		$id       = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$currency = Currency::find( $id );
		if ( ! $currency ) {
			esc_html_e( 'The specified currency does not exist.', 'wp-ever-accounting' );

			return;
		}

		include __DIR__ . '/views/currencies/edit.php';
	}

	/**
	 * Handle add currency.
	 *
	 * @since 3.0.0
	 */
	public static function handle_add() {
		check_admin_referer( 'eac_add_currency' );
		$referer       = wp_get_referer();
		$code          = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';
		$exchange_rate = isset( $_POST['exchange_rate'] ) ? doubleval( wp_unslash( $_POST['exchange_rate'] ) ) : 1;

		// if code is empty, return error.
		if ( empty( $code ) ) {
			EAC()->flash->error( __( 'Currency code is required.', 'wp-ever-accounting' ) );
			wp_safe_redirect( $referer );
			exit;
		}

		$currency = Currency::find( $code );
		if ( $currency ) {
			EAC()->flash->error( __( 'Currency already exists.', 'wp-ever-accounting' ) );
			wp_safe_redirect( $referer );
			exit;
		}
		$config                = I18n::get_currencies();
		$data                  = isset( $config[ $code ] ) ? $config[ $code ] : array();
		$data['code']          = $code;
		$data['exchange_rate'] = $exchange_rate;
		$currency              = Currency::insert( $data );
		if ( is_wp_error( $currency ) ) {
			EAC()->flash->error( $currency->get_error_message() );
			wp_safe_redirect( $referer );
			exit;
		}

		EAC()->flash->success( __( 'Currency added successfully.', 'wp-ever-accounting' ) );
		$referer = remove_query_arg( array( 'add' ), $referer );
		$referer = add_query_arg( ['view' => 'edit', 'id' => $currency->id ], $referer );
		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Edit currency.
	 *
	 * @since 3.0.0
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
		$decimals          = isset( $_POST['decimals'] ) ? absint( wp_unslash( $_POST['decimals'] ) ) : '';
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
				'decimals'          => $decimals,
				'position'           => $position,
				'status'             => $status,
			)
		);

		if ( is_wp_error( $currency ) ) {
			EAC()->flash->error( $currency->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Currency saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( ['view' => 'edit', 'id' => $currency->id ], $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}
}
