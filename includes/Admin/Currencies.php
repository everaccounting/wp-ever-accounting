<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Currencies
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 */
class Currencies {

	/**
	 * Currencies constructor.
	 */
	public function __construct() {
		add_action( 'eac_settings_general_tab_currencies', array( __CLASS__, 'render' ) );
		add_action( 'admin_post_eac_add_currency', array( __CLASS__, 'handle_add' ) );
	}

	/**
	 * Render the currencies settings.
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		$currencies = eac_get_currencies();
		$currency   = isset( $_GET['currency'] ) ? strtoupper( sanitize_key( wp_unslash( $_GET['currency'] ) ) ) : '';
		if ( eac_base_currency() !== $currency && array_key_exists( $currency, $currencies ) ) {
			$currency = $currencies[ $currency ];
			include __DIR__ . '/views/currency-edit.php';
		} else {
			$list_table = new ListTables\Currencies();
			$list_table->prepare_items();
			include __DIR__ . '/views/currency-list.php';
		}
	}

	/**
	 * Handle add currency.
	 *
	 * @since 1.0.0
	 */
	public static function handle_add() {
		$referer = wp_get_referer();
		if ( ! check_admin_referer( 'eac_add_currency' ) || ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to perform this action.', 'wp-ever-accounting' ) );
		}
		$currency = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : '';
		$rate     = isset( $_POST['rate'] ) ? floatval( wp_unslash( $_POST['rate'] ) ) : '';
		if ( empty( $currency ) || empty( $rate ) ) {
			wp_safe_redirect( $referer );
			exit;
		}
		$currencies              = get_option( 'eac_currencies', array() );
		$currency                = strtoupper( $currency );
		$currencies[ $currency ] = array(
			'rate' => $rate,
		);
		update_option( 'eac_currencies', $currencies );
		EAC()->flash->success( esc_html__( 'Currency added successfully.', 'wp-ever-accounting' ) );
		wp_safe_redirect( $referer );
		exit;
	}
}
