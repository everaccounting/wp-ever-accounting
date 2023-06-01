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
			''         => __( 'General', 'wp-ever-accounting' ),
			'defaults' => __( 'Defaults', 'wp-ever-accounting' )
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
		return array(
			// general settings section
			array(
				'title' => __( 'General Settings', 'wp-ever-accounting' ),
				'desc'  => __( 'General details about your business. These will be used in the records you create.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'general_settings',
			),
			array(
				'title'       => __( 'Business Name', 'wp-ever-accounting' ),
				'desc'        => __( 'The name of your business. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_business_name',
				'type'        => 'text',
				'placeholder' => 'e.g. XYZ Company',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Email', 'wp-ever-accounting' ),
				'desc'        => __( 'The email address of your business. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_business_email',
				'type'        => 'email',
				'placeholder' => get_option( 'admin_email' ),
				'default'     => get_option( 'admin_email' ),
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Phone', 'wp-ever-accounting' ),
				'desc'        => __( 'The phone number of your business. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_business_phone',
				'type'        => 'text',
				'placeholder' => 'e.g. +1 123 456 7890',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Tax Number', 'wp-ever-accounting' ),
				'desc'        => __( 'The tax number of your business. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_business_tax_number',
				'type'        => 'text',
				'placeholder' => 'e.g. 123456789',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Logo', 'wp-ever-accounting' ),
				'desc'        => __( 'The logo of your business. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_business_logo',
				'type'        => 'text',
				'placeholder' => 'e.g. http://example.com/logo.png',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'general_settings',
			),
			array(
				'title' => __( 'Business Address', 'wp-ever-accounting' ),
				'desc'  => __( 'Business address details. The address will be used in the invoices, bills, and other records that you issue.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'business_address',
			),
			array(
				'title'       => __( 'Street', 'wp-ever-accounting' ),
				'desc'        => __( 'The street address of your business. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_business_street',
				'type'        => 'text',
				'placeholder' => 'e.g. 123 Main Street',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'City', 'wp-ever-accounting' ),
				'desc'        => __( 'The city in which your business is located. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_business_city',
				'type'        => 'text',
				'placeholder' => 'e.g. New York',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'State', 'wp-ever-accounting' ),
				'desc'        => __( 'The state in which your business is located.', 'wp-ever-accounting' ),
				'id'          => 'eac_business_state',
				'type'        => 'text',
				'placeholder' => 'e.g. New York',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Postcode / ZIP', 'wp-ever-accounting' ),
				'desc'        => __( 'The postcode or ZIP code of your business if any. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'id'          => 'eac_business_postcode',
				'type'        => 'text',
				'placeholder' => 'e.g. 10001',
				'default'     => '',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Country', 'wp-ever-accounting' ),
				'desc'        => __( 'The country in which your business is located.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_country',
				'type'        => 'select',
				'options'     => eac_get_countries(),
				'default'     => 'US',
				'placeholder' => __( 'Select a country&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'business_address',
			),
			array(
				'title' => __( 'Other Settings', 'wp-ever-accounting' ),
				'desc'  => __( 'Company specific other settings.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'other_settings',
			),
			array(
				'title'    => __( 'Base Currency', 'wp-ever-accounting' ),
				'id'       => 'eac_base_currency',
				'type'     => 'currency',
				'default'  => 'USD',
				'desc_tip' => true,
				'desc'  => __( 'Overview, and Reports will be shown in this currency. You can change the currency for each invoice, bill, and other records.', 'wp-ever-accounting' ),
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
				'title'   => __( 'Enable Taxes', 'wp-ever-accounting' ),
				'desc'    => __( 'Enable tax rates and calculations.', 'wp-ever-accounting' ),
				'id'      => 'eac_enabled_tax',
				'type'    => 'checkbox',
				'default' => 'no',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'other_settings',
			),
		);
	}

	/**
	 * Get defaults section settings array.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_settings_for_defaults_section() {
		return array(
			array(
				'title' => __( 'Defaults settings', 'wp-ever-accounting' ),
				'desc'  => __( 'These defaults will be used in particular cases.e.g. when creating a new invoice, bill, etc.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'defaults',
			),
			array(
				'title'       => __( 'Default Payment Account', 'wp-ever-accounting' ),
				'desc'        => __( 'The default account to be used in the payment.', 'wp-ever-accounting' ),
				'id'          => 'eac_payment_account',
				'placeholder' => __( 'Select an account&hellip;', 'wp-ever-accounting' ),
				'type'        => 'account',
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Default Expense Account', 'wp-ever-accounting' ),
				'desc'        => __( 'The default account to be used in the expense.', 'wp-ever-accounting' ),
				'id'          => 'eac_expense_account',
				'placeholder' => __( 'Select an account&hellip;', 'wp-ever-accounting' ),
				'type'        => 'account',
				'desc_tip'    => true,
			),
			array(
				'title'    => __( 'Payment Category', 'wp-ever-accounting' ),
				'desc'     => __( 'The default income category to be used in the payment.', 'wp-ever-accounting' ),
				'id'       => 'eac_payment_category',
				'type'     => 'category',
				'subtype'  => 'payment',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Expense Category', 'wp-ever-accounting' ),
				'desc'     => __( 'The default expense category to be used in the expense.', 'wp-ever-accounting' ),
				'id'       => 'eac_expense_category',
				'type'     => 'category',
				'subtype'  => 'expense',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Payment Method', 'wp-ever-accounting' ),
				'desc'     => __( 'The default payment method to be in the payment.', 'wp-ever-accounting' ),
				'id'       => 'eac_payment_method',
				'type'     => 'select',
				'options'  => eac_get_payment_methods(),
				'default'  => '',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Expense Method', 'wp-ever-accounting' ),
				'desc'     => __( 'The default expense method to be used in the expense.', 'wp-ever-accounting' ),
				'id'       => 'eac_expense_method',
				'type'     => 'select',
				'options'  => eac_get_payment_methods(),
				'default'  => '',
				'desc_tip' => true,
			),
			array(
				'title'    => __( 'Records per page', 'wp-ever-accounting' ),
				'desc'     => __( 'The number of records to be displayed per page in the list table.', 'wp-ever-accounting' ),
				'id'       => 'eac_records_per_page',
				'type'     => 'number',
				'default'  => '20',
				'desc_tip' => true,
				'attrs'    => array(
					'min'  => 1,
					'step' => 1,
					'max'  => '100',
				),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'defaults',
			),
		);
	}


}
