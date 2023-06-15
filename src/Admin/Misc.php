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
		add_action( 'ever_accounting_settings_tab_categories', array( __CLASS__, 'output_categories_tab' ) );
		add_action( 'ever_accounting_settings_tab_general', array( __CLASS__, 'output_currencies_tab' ) );
		add_action( 'ever_accounting_settings_tab_taxes', array( __CLASS__, 'output_tax_rates_tab' ) );
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
		$tabs['categories'] = __( 'Categories', 'wp-ever-accounting' );
		return $tabs;
	}

	/**
	 * Output the categories tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_categories_tab() {
		$action      = eac_get_input_var( 'action' );
		$category_id = eac_get_input_var( 'category_id' );
		if ( in_array( $action, array( 'edit', 'add' ), true ) ) {
			include dirname( __FILE__ ) . '/views/categories/edit-category.php';
		} else {
			include dirname( __FILE__ ) . '/views/categories/list-categories.php';
		}
	}

	/**
	 * Output the currencies tab.
	 *
	 * @param string $section Current section.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_currencies_tab( $section ) {
		if ( 'currencies' !== $section ) {
			return;
		}
		$action      = eac_get_input_var( 'action' );
		$currency_id = eac_get_input_var( 'currency_id' );
		if ( 'edit' === $action && ! eac_get_currency( $currency_id ) ) {
			wp_safe_redirect( remove_query_arg( array( 'action', 'currency_id' ) ) );
			exit();
		}
		if ( 'add' === $action ) {
			include dirname( __FILE__ ) . '/views/currencies/add-currency.php';
		} elseif ( 'edit' === $action ) {
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
	public static function output_tax_rates_tab( $section ) {
		if ( 'rates' !== $section ) {
			return;
		}

		$action = eac_get_input_var( 'action' );
		$tax_id = eac_get_input_var( 'tax_id' );
		$tax    = empty( $term_id ) ? false : eac_get_tax( $tax_id );

		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/taxes/edit-tax.php';
		} else {
			include dirname( __FILE__ ) . '/views/taxes/list-taxes.php';
		}
	}
}
