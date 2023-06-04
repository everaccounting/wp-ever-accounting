<?php

defined( 'ABSPATH' ) || exit;

/**
 * Get all EverAccounting screen ids.
 *
 * @since  1.0.2
 * @return array
 */
function eac_get_screen_ids() {
	$screen_id  = sanitize_title( __( 'Accounting', 'wp-ever-accounting' ) );
	$screen_ids = array(
		'toplevel_page_' . $screen_id,
		$screen_id . '_page_eac-transactions',
		$screen_id . '_page_eac-sales',
		$screen_id . '_page_eac-purchases',
		$screen_id . '_page_eac-banking',
		$screen_id . '_page_eac-products',
		$screen_id . '_page_eac-reports',
		$screen_id . '_page_eac-tools',
		$screen_id . '_page_eac-settings',
		$screen_id . '_page_eac-extensions',
		'toplevel_page_ever-accounting'
	);

	return apply_filters( 'ever_accounting_screen_ids', $screen_ids );
}


/**
 * Is admin page.
 *
 * @param string $page Page.
 *
 * @since 1.1.6
 * @return bool
 */
function eac_is_admin_page( $page = null ) {
	if ( ! is_admin() || ! did_action( 'wp_loaded' ) ) {
		$ret = false;
	}

	$current_page = eac_get_input_var( 'page' );
	if ( empty( $page ) && ! empty( $current_page ) ) {
		$page = eac_clean( $current_page );
	} else {
		$ret = false;
	}
	// When translate the page name becomes different so use translated.
	$screen_id = sanitize_title( esc_html__( 'Accounting', 'wp-ever-accounting' ) );
	$pages     = str_replace(
		array(
			'toplevel_page_',
			'accounting_page_',
			$screen_id . '_page_',
		),
		'',
		eac_get_screen_ids()
	);

	if ( ! empty( $page ) && in_array( $page, $pages, true ) ) {
		$ret = true;
	} else {
		$ret = in_array( $page, $pages, true );
	}

	return apply_filters( 'ever_accounting_is_admin_page', $ret );
}

/**
 * Get overview tabs.
 *
 * @since 1.1.6
 * @return array
 */
function eac_get_overview_tabs() {
	$tabs = array();
	if ( current_user_can( 'manage_accounting' ) ) {
		$tabs['overview'] = __( 'Overview', 'wp-ever-accounting' );
	}

	return apply_filters( 'ever_accounting_overview_tabs', $tabs );
}

/**
 * Get products tabs.
 *
 * @since 1.1.6
 * @return array
 */
function eac_get_products_tabs() {
	$tabs = array();
	if ( current_user_can( 'eac_manage_product' ) ) {
		$tabs['products'] = __( 'Products', 'wp-ever-accounting' );
	}
	$tabs['categories'] = __( 'Categories', 'wp-ever-accounting' );

	return apply_filters( 'ever_accounting_products_tabs', $tabs );
}

/**
 * Get Sales tabs.
 *
 * @since 1.1.6
 * @return array
 */
function eac_get_sales_tabs() {
	$tabs = array();
	if ( current_user_can( 'eac_manage_income' ) ) {
		$tabs['incomes'] = __( 'Incomes', 'wp-ever-accounting' );
	}
	if ( current_user_can( 'eac_manage_invoice' ) ) {
		$tabs['invoices'] = __( 'Invoices', 'wp-ever-accounting' );
	}
	if ( current_user_can( 'eac_manage_customer' ) ) {
		$tabs['customers'] = __( 'Customers', 'wp-ever-accounting' );
	}

	return apply_filters( 'ever_accounting_sales_tabs', $tabs );
}

/**
 * Get purchase tabs.
 *
 * @since 1.1.6
 * @return array
 */
function eac_get_purchase_tabs() {
	$tabs = array();
	if ( current_user_can( 'eac_manage_payment' ) ) {
		$tabs['expenses'] = __( 'Expenses', 'wp-ever-accounting' );
	}
	if ( current_user_can( 'eac_manage_bill' ) ) {
		$tabs['bills'] = __( 'Bills', 'wp-ever-accounting' );
	}
	if ( current_user_can( 'eac_manage_vendor' ) ) {
		$tabs['vendors'] = __( 'Vendors', 'wp-ever-accounting' );
	}

	return apply_filters( 'ever_accounting_purchase_tabs', $tabs );
}

/**
 * Get Banking tabs.
 *
 * @since 1.1.6
 * @return array
 */
function eac_get_banking_tabs() {
	$tabs = array();
	if ( current_user_can( 'eac_manage_payment' ) && current_user_can( 'eac_manage_expense' ) ) {
		$tabs['transactions'] = __( 'Transactions', 'wp-ever-accounting' );
	}
	if ( current_user_can( 'eac_manage_account' ) ) {
		$tabs['accounts'] = __( 'Accounts', 'wp-ever-accounting' );
	}
	if ( current_user_can( 'eac_manage_transfer' ) ) {
		$tabs['transfers'] = __( 'Transfers', 'wp-ever-accounting' );
	}

	return apply_filters( 'ever_accounting_banking_tabs', $tabs );
}

/**
 * Get Tools tabs.
 *
 * @since 1.1.6
 * @return array
 */
function eac_get_tools_tabs() {
	$tabs             = array();
	$tabs['import']   = __( 'Import', 'wp-ever-accounting' );
	$tabs['export']   = __( 'Export', 'wp-ever-accounting' );
	$tabs['elements'] = __( 'Elements', 'wp-ever-accounting' );
	$tabs['settings'] = __( 'Settings', 'wp-ever-accounting' );

	return apply_filters( 'ever_accounting_tools_tabs', $tabs );
}

/**
 * Get Reports tabs.
 *
 * @since 1.1.6
 * @return array
 */
function eac_get_reports_tabs() {
	$tabs = array(
		'sales'    => __( 'Sales', 'wp-ever-accounting' ),
		'expenses' => __( 'Expenses', 'wp-ever-accounting' ),
		'profits'  => __( 'Profits', 'wp-ever-accounting' ),
		'cashflow' => __( 'Cashflow', 'wp-ever-accounting' ),
	);

	return apply_filters( 'ever_accounting_reports_tabs', $tabs );
}
