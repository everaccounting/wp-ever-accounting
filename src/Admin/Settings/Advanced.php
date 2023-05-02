<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class Advanced.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class Advanced extends \EverAccounting\Admin\SettingsTab {
	/**
	 * Currency constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id    = 'advanced';
		$this->label = __( 'Advanced', 'ever-accounting' );

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
			'' => __( 'General', 'wp-ever-accounting' ),
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
			// address format option.
			array(
				'title' => __( 'Address Format', 'wp-ever-accounting' ),
				'desc'  => __( 'Choose the address formats as per your need.', 'wp-ever-accounting' ),
				'type'  => 'title',
			),

			// company address format.
			array(
				'title'   => __( 'Company Address Format', 'wp-ever-accounting' ),
				'desc'    => __( 'Choose the address format for company.', 'wp-ever-accounting' ),
				'id'      => 'eac_company_address_format',
				'default' => '{name}<br>{street}<br>{city} {state}<br>{country} {postcode}<br>{email}<br>{phone}',
				'type'    => 'editor',
				'desctip' => __( 'Available tags: {name}, {street}, {city}, {state}, {country}, {postcode}, {email}, {phone}', 'wp-ever-accounting' ),
			),

			// customer address format.
			array(
				'title'   => __( 'Customer Address Format', 'wp-ever-accounting' ),
				'desc'    => __( 'Choose the address format for customer.', 'wp-ever-accounting' ),
				'id'      => 'eac_customer_address_format',
				'default' => '{name}<br>{street}<br>{city} {state}<br>{country} {postcode}<br>{email}<br>{phone}',
				'type'    => 'editor',
				'desctip' => __( 'Available tags: {name}, {street}, {city}, {state}, {country}, {postcode}, {email}, {phone}', 'wp-ever-accounting' ),
			),

			// vendor address format.
			array(
				'title'   => __( 'Vendor Address Format', 'wp-ever-accounting' ),
				'desc'    => __( 'Choose the address format for vendor.', 'wp-ever-accounting' ),
				'id'      => 'eac_vendor_address_format',
				'default' => '{name}<br>{street}<br>{city} {state}<br>{country} {postcode}<br>{email}<br>{phone}',
				'type'    => 'editor',
				'desctip' => __( 'Available tags: {name}, {street}, {city}, {state}, {country}, {postcode}, {email}, {phone}', 'wp-ever-accounting' ),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'eac_general_settings',
			),
		);
	}
}
