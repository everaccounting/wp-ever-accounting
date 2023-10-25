<?php

namespace EverAccounting\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Settings service.
 *
 * @since 1.1.6
 * @package EverAccounting\Services
 */
class Settings extends Service {

	/**
	 * Settings constructor.
	 *
	 * @since 1.1.6
	 */
	public function __construct() {
		add_filter( 'ever_accounting_settings_groups', [ $this, 'register_settings_groups' ] );
		add_filter( 'ever_accounting_settings_company', [ $this, 'register_company_settings' ] );
	}

	/**
	 * Get plugin settings.
	 *
	 * @since 1.1.6
	 *
	 * @return array
	 */
	public function get_settings() {
		return array(
			array(
				'name'        => 'company_name',
				'label'       => __( 'Company Name', 'wp-ever-accounting' ),
				'description' => __( 'The name of your company. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'type'        => 'text',
				'placeholder' => 'e.g. XYZ Company',
				'default'     => esc_html( get_bloginfo( 'name' ) ),
			),
			array(
				'name'        => 'company_email',
				'label'       => __( 'Email', 'wp-ever-accounting' ),
				'description' => __( 'The email address of your company. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'type'        => 'email',
				'default'     => get_option( 'admin_email' ),
			),
			array(
				'name'        => 'company_phone',
				'label'       => __( 'Phone', 'wp-ever-accounting' ),
				'description' => __( 'The phone number of your company. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'type'        => 'text',
				'default'     => '',
			),
			array(
				'name'        => 'company_vat_number',
				'label'       => __( 'VAT Number', 'wp-ever-accounting' ),
				'description' => __( 'The vat number of your company. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'type'        => 'text',
				'default'     => '',
			),
			array(
				'name'        => 'year_start_date',
				'label'       => __( 'Financial Year Start', 'wp-ever-accounting' ),
				'description' => __( 'The start date of your financial year.', 'wp-ever-accounting' ),
				'type'        => 'text',
				'default'     => '01-01',
			),
			array(
				'name'        => 'company_address_1',
				'label'       => __( 'Address Line 1', 'wp-ever-accounting' ),
				'description' => __( 'The street address of your company.', 'wp-ever-accounting' ),
				'type'        => 'text',
				'default'     => '',
			),
			array(
				'name'        => 'company_address_2',
				'label'       => __( 'Address Line 2', 'wp-ever-accounting' ),
				'description' => __( 'An additional, optional address line for your company location.', 'wp-ever-accounting' ),
				'type'        => 'text',
				'default'     => '',
			),
			array(
				'name'        => 'company_city',
				'label'       => __( 'City', 'wp-ever-accounting' ),
				'description' => __( 'The city in which your company is located. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'type'        => 'text',
				'default'     => '',
			),
			array(
				'name'        => 'company_state',
				'label'       => __( 'State', 'wp-ever-accounting' ),
				'description' => __( 'The state in which your company is located.', 'wp-ever-accounting' ),
				'type'        => 'text',
				'default'     => '',
			),
			array(
				'name'        => 'company_postcode',
				'label'       => __( 'Postcode / ZIP', 'wp-ever-accounting' ),
				'description' => __( 'The postcode or ZIP code of your company if any. This will be used in the invoice, bill, and other documents.', 'wp-ever-accounting' ),
				'type'        => 'text',
				'default'     => '',
			),
			array(
				'name'        => 'company_country',
				'label'       => __( 'Country', 'wp-ever-accounting' ),
				'description' => __( 'The country in which your company is located.', 'wp-ever-accounting' ),
				'type'        => 'select',
				'options'     => eac_get_countries(),
				'default'     => 'US',
			),
		);
	}
}
