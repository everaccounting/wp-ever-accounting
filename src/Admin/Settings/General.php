<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class GeneralSettingsPage.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class General extends \EverAccounting\Admin\SettingsTab {

	/**
	 * GeneralSettingsPage constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', 'wp-ever-accounting' );

		parent::__construct();
	}

	/**
	 * Get own sections for this page.
	 * Derived classes should override this method if they define sections.
	 * There should always be one default section with an empty string as identifier.
	 *
	 * Example:
	 * return array(
	 *   ''        => __( 'General', 'wp-ever-accounting' ),
	 *   'foobars' => __( 'Foos & Bars', 'wp-ever-accounting' ),
	 * );
	 *
	 * @return array An associative array where keys are section identifiers and the values are translated section names.
	 */
	protected function get_own_sections() {
		$sections = array(
			''           => __( 'Company', 'wp-ever-accounting' ),
			'currencies' => __( 'Currencies', 'wp-ever-accounting' ),
			// 'defaults' => __( 'Defaults', 'wp-ever-accounting' ),
		);

		return $sections;
	}

	/**
	 * Get settings or the default section.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_settings_for_default_section() {
		$currencies_options = array();
		$currency_info = eac_get_currencies_info();
		foreach ( $currency_info as $currency_code => $currency ) {
			$currencies_options[ $currency_code ] = sprintf( '%s (%s)', $currency['name'], $currency['symbol'] );
		}

		return array(
			array(
				'title' => __( 'Company Information', 'wp-ever-accounting' ),
				'desc'  => __( 'General details about your company. These will be used in the records you create.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'general_settings',
			),
			array(
				'title'       => __( 'Company Name', 'wp-ever-accounting' ),
				'desc'        => __( 'The name of your company. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_name',
				'type'        => 'text',
				'placeholder' => 'e.g. XYZ Company',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Email', 'wp-ever-accounting' ),
				'desc'        => __( 'The email address of your company. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_email',
				'type'        => 'email',
				'placeholder' => get_option( 'admin_email' ),
				'default'     => get_option( 'admin_email' ),
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Phone', 'wp-ever-accounting' ),
				'desc'        => __( 'The phone number of your company. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_phone',
				'type'        => 'text',
				'placeholder' => 'e.g. +1 123 456 7890',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Logo', 'wp-ever-accounting' ),
				'desc'        => __( 'The logo of your company. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_logo',
				'type'        => 'text',
				'placeholder' => 'e.g. http://example.com/logo.png',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'VAT Number', 'wp-ever-accounting' ),
				'desc'        => __( 'The vat number of your company. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_vat_number',
				'type'        => 'text',
				'placeholder' => 'e.g. 123456789',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Tax Number', 'wp-ever-accounting' ),
				'desc'        => __( 'The tax number of your company. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_tax_number',
				'type'        => 'text',
				'placeholder' => 'e.g. 123456789',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'    => __( 'Base Currency', 'wp-ever-accounting' ),
				'id'       => 'eac_base_currency',
				'type'     => 'select',
				'default'  => 'USD',
				'class'    => 'eac-select2',
				'options'  => $currencies_options,
				'disabled' => ! empty( eac_get_transactions() ),
				'desc_tip' => __( 'Base currency can not be changed once you have recorded any transaction.', 'wp-ever-accounting' ),
			),
			array(
				'title'       => __( 'Financial Year Start', 'wp-ever-accounting' ),
				'desc'        => __( 'The start date of your financial year.', 'wp-ever-accounting' ),
				'id'          => 'eac_financial_year_start',
				'type'        => 'text',
				'placeholder' => 'e.g. 01-01',
				'default'     => '01-01',
				'desc_tip'    => true,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'general_settings',
			),
			array(
				'title' => __( 'Company Address', 'wp-ever-accounting' ),
				'desc'  => __( 'Business address details. The address will be used in the invoices, bills, and other records that you issue.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'company_address',
			),
			array(
				'title'       => __( 'Address Line 1', 'wp-ever-accounting' ),
				'desc'        => __( 'The street address of your company.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_address_1',
				'type'        => 'text',
				'placeholder' => 'e.g. 123 Main Street',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Address Line 2', 'wp-ever-accounting' ),
				'desc'        => __( 'An additional, optional address line for your company location.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_address_2',
				'type'        => 'text',
				'placeholder' => 'e.g. Suite 100',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'City', 'wp-ever-accounting' ),
				'desc'        => __( 'The city in which your company is located. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_city',
				'type'        => 'text',
				'placeholder' => 'e.g. Manhattan',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'State', 'wp-ever-accounting' ),
				'desc'        => __( 'The state in which your company is located.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_state',
				'type'        => 'text',
				'placeholder' => 'e.g. New York',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Postcode / ZIP', 'wp-ever-accounting' ),
				'desc'        => __( 'The postcode or ZIP code of your company if any. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_postcode',
				'type'        => 'text',
				'placeholder' => 'e.g. 10001',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Country', 'wp-ever-accounting' ),
				'desc'        => __( 'The country in which your company is located.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_country',
				'type'        => 'select',
				'options'     => eac_get_countries(),
				'class'       => 'eac-select2',
				'default'     => 'US',
				'placeholder' => __( 'Select a country&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'company_address',
			)
		);
	}

	/**
	 * Get defaults section settings array.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_settings_for_defaults_section() {
		$payment_accounts   = eac_get_accounts( array( 'include' => get_option( 'eac_default_payment_account' ) ) );
		$expense_accounts   = eac_get_accounts( array( 'include' => get_option( 'eac_default_expense_account' ) ) );
		$payment_categories = eac_get_categories( array( 'include' => get_option( 'eac_default_payment_category' ) ) );
		$expense_categories = eac_get_categories( array( 'include' => get_option( 'eac_default_expense_category' ) ) );

		return array(
			// Sales defaults section.
			array(
				'title' => __( 'Sales Defaults', 'wp-ever-accounting' ),
				'type'  => 'title',
				'desc'  => __( 'Default settings for sales.', 'wp-ever-accounting' ),
				'id'    => 'sales_defaults',
			),
			// Default payment account.
			array(
				'title'       => __( 'Default Payment Account', 'wp-ever-accounting' ),
				'desc'        => __( 'The default account to which the payments will be credited.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_payment_account',
				'type'        => 'select',
				'options'     => wp_list_pluck( $payment_accounts, 'formatted_name', 'id' ),
				'default'     => '',
				'placeholder' => __( 'Select an account&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
				'class'       => 'eac_select2',
				'attrs'       => array(
					'data-action' => 'eac_json_search',
					'data-type'   => 'account',
				),
			),
			// Default payment method.
			array(
				'title'       => __( 'Default Payment Method', 'wp-ever-accounting' ),
				'desc'        => __( 'The default payment method for sales.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_payment_method',
				'type'        => 'select',
				'options'     => eac_get_payment_methods(),
				'default'     => '',
				'placeholder' => __( 'Select a payment method&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			// Default category.
			array(
				'title'       => __( 'Default Category', 'wp-ever-accounting' ),
				'desc'        => __( 'The default category for sales.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_category',
				'type'        => 'select',
				'default'     => '',
				'options'     => wp_list_pluck( $payment_categories, 'formatted_name', 'id' ),
				'placeholder' => __( 'Select a category&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
				'class'       => 'eac_select2',
				'attrs'       => array(
					'data-action' => 'eac_json_search',
					'data-type'   => 'payment_category',
				),
			),
			// end of sales defaults section.
			array(
				'type' => 'sectionend',
				'id'   => 'sales_defaults',
			),
			// Purchase defaults section.
			array(
				'title' => __( 'Purchase Defaults', 'wp-ever-accounting' ),
				'type'  => 'title',
				'desc'  => __( 'Default settings for purchases.', 'wp-ever-accounting' ),
				'id'    => 'purchase_defaults',
			),
			// Default payment account.
			array(
				'title'       => __( 'Default expense Account', 'wp-ever-accounting' ),
				'desc'        => __( 'The default account to which the expense will be debited.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_expense_account',
				'type'        => 'select',
				'options'     => wp_list_pluck( $expense_accounts, 'formatted_name', 'id' ),
				'default'     => '',
				'placeholder' => __( 'Select an account&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
				'class'       => 'eac_select2',
				'attrs'       => array(
					'data-action' => 'eac_json_search',
					'data-type'   => 'account',
				),
			),
			// Default payment method.
			array(
				'title'       => __( 'Default Payment Method', 'wp-ever-accounting' ),
				'desc'        => __( 'The default payment method for purchases.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_expense_method',
				'type'        => 'select',
				'options'     => eac_get_payment_methods(),
				'default'     => '',
				'placeholder' => __( 'Select a payment method&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			// Default category.
			array(
				'title'       => __( 'Default Category', 'wp-ever-accounting' ),
				'desc'        => __( 'The default category for purchases.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_expense_category',
				'type'        => 'select',
				'options'     => wp_list_pluck( $expense_categories, 'formatted_name', 'id' ),
				'placeholder' => __( 'Select a category&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
				'class'       => 'eac_select2',
				'attrs'       => array(
					'data-action' => 'eac_json_search',
					'data-type'   => 'expense_category',
				),
			),
			// end of purchase defaults section.
			array(
				'type' => 'sectionend',
				'id'   => 'purchase_defaults',
			),
			// Misc defaults section.
			array(
				'title' => __( 'Misc Defaults', 'wp-ever-accounting' ),
				'type'  => 'title',
				'desc'  => __( 'Miscellaneous default settings.', 'wp-ever-accounting' ),
				'id'    => 'misc_defaults',
			),
			// Record per page.
			array(
				'title'       => __( 'Records per page', 'wp-ever-accounting' ),
				'desc'        => __( 'The number of records to show per page.', 'wp-ever-accounting' ),
				'id'          => 'eac_records_per_page',
				'type'        => 'number',
				'default'     => 20,
				'placeholder' => __( 'Enter a number&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			// end of misc defaults section.
			array(
				'type' => 'sectionend',
				'id'   => 'misc_defaults',
			),
		);
	}


}
