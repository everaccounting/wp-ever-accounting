<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Misc.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Misc extends \EverAccounting\Singleton {

	/**
	 * Misc constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_filter( 'ever_accounting_settings_tabs_array', array( __CLASS__, 'add_settings_tabs' ) );
		add_action( 'ever_accounting_settings_tab_currencies', array( __CLASS__, 'output_currencies_tab' ) );
		add_action( 'ever_accounting_settings_tab_tax', array( __CLASS__, 'output_tax_tab' ) );
	}

	/**
	 * Add the settings tab.
	 *
	 * @param array $tabs Settings tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function add_settings_tabs( $tabs ) {
		$tabs['currencies'] = __( 'Currencies', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * Output the currencies tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_currencies_tab() {
		$action = eac_get_input_var( 'action' );
		if ( in_array( $action, array( 'edit' ), true ) ) {
			$currency   = eac_get_input_var( 'currency' );
			$currencies = eac_get_currencies();
			if ( empty( $currency ) || ! isset( $currencies[ $currency ] ) ) {
				wp_safe_redirect( admin_url( 'admin.php?page=eac-settings&tab=currencies' ) );
				exit;
			}
			$currency_data = $currencies[ $currency ];
			include dirname( __FILE__ ) . '/views/currencies/edit-currency.php';
		} else {
			include dirname( __FILE__ ) . '/views/currencies/list-currencies.php';
		}
	}

	/**
	 * Output the tax tab.
	 *
	 * @param string $section Current section.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_tax_tab( $section ) {
		if ( 'taxes' !== $section ) {
			return;
		}
		$action   = eac_get_input_var( 'action' );
		$tax_id   = eac_get_input_var( 'tax_id' );
		$tax_rate = empty( $term_id ) ? false : eac_get_term( $term_id, 'income_cat' );

		return;
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/taxes/edit-tax.php';
		} else {
			include dirname( __FILE__ ) . '/views/taxes/list-taxes.php';
		}
	}
}
