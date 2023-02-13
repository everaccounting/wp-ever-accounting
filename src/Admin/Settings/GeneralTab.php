<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class GeneralSettingsPage.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class GeneralTab extends Tab {

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
	 * Get settings or the default section.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_settings_for_default_section() {
		return [
			[
				'title' => __( 'General Settings', 'wp-ever-accounting' ),
				'type'  => 'title',
				'desc'  => esc_html__('Company details will be used in the related documents.', 'wp-ever-accounting'),
				'id'    => 'general_options',
			],
			[
				'title'    => __( 'Company Name', 'wp-ever-accounting' ),
				'desc'     => __( 'The name of your company.', 'wp-ever-accounting' ),
				'id'       => 'ea_company_name',
				'type'     => 'text',
				'placeholder' => 'e.g. My Company',
				'default'  => '',
				'desc_tip' => true,
			],
			[
				'title'    => __( 'Email', 'wp-ever-accounting' ),
				'desc'     => __( 'The email address of your company.', 'wp-ever-accounting' ),
				'id'       => 'ea_company_email',
				'type'     => 'email',
				'placeholder' => 'john@company.com',
				'default'  => get_option( 'admin_email' ),
				'desc_tip' => true,
			],
			[
				'title'    => __( 'Phone', 'wp-ever-accounting' ),
				'desc'     => __( 'The phone number of your company.', 'wp-ever-accounting' ),
				'id'       => 'ea_company_phone',
				'type'     => 'text',
				'placeholder' => 'e.g. +1 123 456 7890',
				'default'  => '',
				'desc_tip' => true,
			],
			[
				'title'    => __( 'Address', 'wp-ever-accounting' ),
				'desc'     => __( 'The address of your company.', 'wp-ever-accounting' ),
				'id'       => 'ea_company_address',
				'type'     => 'text',
				'placeholder' => 'e.g. 123 Main Street',
				'default'  => '',
				'desc_tip' => true,
			],
			[
				'title'    => __( 'City', 'wp-ever-accounting' ),
				'desc'     => __( 'The city of your company.', 'wp-ever-accounting' ),
				'id'       => 'ea_company_city',
				'type'     => 'text',
				'placeholder' => 'e.g. New York',
				'default'  => '',
				'desc_tip' => true,
			],
			[
				'title'    => __( 'State', 'wp-ever-accounting' ),
				'desc'     => __( 'The state of your company.', 'wp-ever-accounting' ),
				'id'       => 'ea_company_state',
				'type'     => 'text',
				'placeholder' => 'e.g. NY',
				'default'  => '',
				'desc_tip' => true,
			],
			[
				'title'    => __( 'Zip', 'wp-ever-accounting' ),
				'desc'     => __( 'The zip code of your company.', 'wp-ever-accounting' ),
				'id'       => 'ea_company_zip',
				'type'     => 'text',
				'placeholder' => 'e.g. 10001',
				'default'  => '',
				'desc_tip' => true,
			],
			[
				'title'    => __( 'Country', 'wp-ever-accounting' ),
				'desc'     => __( 'The country of your company.', 'wp-ever-accounting' ),
				'id'       => 'ea_company_country',
				'type'     => 'text',
				'placeholder' => 'e.g. United States',
				'default'  => '',
				'desc_tip' => true,
			],
			[
				'title'    => __( 'VAT', 'wp-ever-accounting' ),
				'desc'     => __( 'The VAT number of your company.', 'wp-ever-accounting' ),
				'id'       => 'ea_company_vat',
				'type'     => 'text',
				'placeholder' => 'e.g. 123456789',
				'default'  => '',
				'desc_tip' => true,
			],
			[
				'type' => 'sectionend',
				'id'   => 'general_options',
			],
		];
	}

}
