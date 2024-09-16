<?php

namespace EverAccounting\Admin\Settings;

use EverAccounting\Utilities\I18n;

defined( 'ABSPATH' ) || exit;

/**
 * Class GeneralSettingsPage.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class General extends Page {

	/**
	 * GeneralSettingsPage constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id    = '';
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
		return array();
	}

	/**
	 * Get settings or the default section.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_settings_for_default_section() {
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
				'default'     => esc_html( get_bloginfo( 'name' ) ),
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
				'title'        => __( 'Currency', 'wp-ever-accounting' ),
				'id'           => 'eac_base_currency',
				'type'         => 'select',
				'default'      => 'USD',
				'class'        => 'eac_select2',
				'options'      => array( eac_get_currency( get_option( 'eac_base_currency' ) ) ),
				'option_key'   => 'code',
				'option_value' => 'formatted_name',
				'value'        => get_option( 'eac_base_currency', 'USD' ),
				'disabled'     => ! empty( eac_get_transactions() ),
				'desc_tip'     => __( 'Base currency can not be changed once you have recorded any transaction.', 'wp-ever-accounting' ),
				'data-action'  => 'eac_json_search',
				'data-type'    => 'currency',
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
				'title'       => __( 'Financial Year Start', 'wp-ever-accounting' ),
				'desc'        => __( 'The start date of your financial year.', 'wp-ever-accounting' ),
				'id'          => 'eac_year_start_date',
				'type'        => 'text',
				'placeholder' => 'e.g. 01-01',
				'default'     => '01-01',
				'desc_tip'    => true,
				'class'       => 'eac_datepicker',
				'data-format' => 'mm-dd',
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
				'title'       => __( 'Address', 'wp-ever-accounting' ),
				'desc'        => __( 'The street address of your company.', 'wp-ever-accounting' ),
				'id'          => 'eac_company_address',
				'type'        => 'text',
				'placeholder' => 'e.g. 123 Main Street',
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
				'options'     => I18n::get_countries(),
				'class'       => 'eac_select2',
				'default'     => 'US',
				'placeholder' => __( 'Select a country&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			array(
				'type' => 'sectionend',
				'id'   => 'company_address',
			),
		);
	}
}
