<?php

class EA_General_Settings extends \Ever_Accounting\Admin\Settings_API {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', 'text_domain' );
	}


	public function get_sections() {
		return array(
			'general'  => __( 'General', 'text_domain' ),
			'advanced' => __( 'Advanced', 'text_domain' ),
		);
	}

	public function get_settings( $section = '' ) {
		return array(
			array(
				'title' => __( 'Store Address', 'text_domain' ),
				'type'  => 'title',
				'desc'  => __( 'This is where your business is located. Tax rates and shipping rates will use this address.', 'text_domain' ),
				'id'    => 'store_address',
			),

			array(
				'title'    => __( 'Address line 1', 'text_domain' ),
				'desc'     => __( 'The street address for your business location.', 'text_domain' ),
				'id'       => 'woocommerce_store_address',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Address line 2', 'text_domain' ),
				'desc'     => __( 'An additional, optional address line for your business location.', 'text_domain' ),
				'id'       => 'woocommerce_store_address_2',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'City', 'text_domain' ),
				'desc'     => __( 'The city in which your business is located.', 'text_domain' ),
				'id'       => 'woocommerce_store_city',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Country / State', 'text_domain' ),
				'desc'     => __( 'The country and state or province, if any, in which your business is located.', 'text_domain' ),
				'id'       => 'woocommerce_default_country',
				'default'  => 'US:CA',
				'type'     => 'single_select_country',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Postcode / ZIP', 'text_domain' ),
				'desc'     => __( 'The postal code, if any, in which your business is located.', 'text_domain' ),
				'id'       => 'woocommerce_store_postcode',
				'css'      => 'min-width:50px;',
				'default'  => '',
				'type'     => 'text',
				'desc_tip' => true,
			),

			array(
				'type' => 'sectionend',
				'id'   => 'store_address',
			),

			array(
				'title' => __( 'General options', 'text_domain' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'general_options',
			),

			array(
				'title'    => __( 'Selling location(s)', 'text_domain' ),
				'desc'     => __( 'This option lets you limit which countries you are willing to sell to.', 'text_domain' ),
				'id'       => 'woocommerce_allowed_countries',
				'default'  => 'all',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'desc_tip' => true,
				'options'  => array(
					'all'        => __( 'Sell to all countries', 'text_domain' ),
					'all_except' => __( 'Sell to all countries, except for&hellip;', 'text_domain' ),
					'specific'   => __( 'Sell to specific countries', 'text_domain' ),
				),
			),

			array(
				'title'   => __( 'Sell to all countries, except for&hellip;', 'text_domain' ),
				'desc'    => '',
				'id'      => 'woocommerce_all_except_countries',
				'css'     => 'min-width: 350px;',
				'default' => '',
				'type'    => 'multi_select_countries',
			),

			array(
				'title'   => __( 'Sell to specific countries', 'text_domain' ),
				'desc'    => '',
				'id'      => 'woocommerce_specific_allowed_countries',
				'css'     => 'min-width: 350px;',
				'default' => '',
				'type'    => 'multi_select_countries',
			),

			array(
				'title'    => __( 'Shipping location(s)', 'text_domain' ),
				'desc'     => __( 'Choose which countries you want to ship to, or choose to ship to all locations you sell to.', 'text_domain' ),
				'id'       => 'woocommerce_ship_to_countries',
				'default'  => '',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'desc_tip' => true,
				'options'  => array(
					''         => __( 'Ship to all countries you sell to', 'text_domain' ),
					'all'      => __( 'Ship to all countries', 'text_domain' ),
					'specific' => __( 'Ship to specific countries only', 'text_domain' ),
					'disabled' => __( 'Disable shipping &amp; shipping calculations', 'text_domain' ),
				),
			),

			array(
				'title'   => __( 'Ship to specific countries', 'text_domain' ),
				'desc'    => '',
				'id'      => 'woocommerce_specific_ship_to_countries',
				'css'     => '',
				'default' => '',
				'type'    => 'multi_select_countries',
			),

			array(
				'title'    => __( 'Default customer location', 'text_domain' ),
				'id'       => 'woocommerce_default_customer_address',
				'desc_tip' => __( 'This option determines a customers default location. The MaxMind GeoLite Database will be periodically downloaded to your wp-content directory if using geolocation.', 'text_domain' ),
				'default'  => 'base',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					''                 => __( 'No location by default', 'text_domain' ),
					'base'             => __( 'Shop base address', 'text_domain' ),
					'geolocation'      => __( 'Geolocate', 'text_domain' ),
					'geolocation_ajax' => __( 'Geolocate (with page caching support)', 'text_domain' ),
				),
			),

			array(
				'title'    => __( 'Enable taxes', 'text_domain' ),
				'desc'     => __( 'Enable tax rates and calculations', 'text_domain' ),
				'id'       => 'woocommerce_calc_taxes',
				'default'  => 'no',
				'type'     => 'checkbox',
				'desc_tip' => __( 'Rates will be configurable and taxes will be calculated during checkout.', 'text_domain' ),
			),

			array(
				'title'           => __( 'Enable coupons', 'text_domain' ),
				'desc'            => __( 'Enable the use of coupon codes', 'text_domain' ),
				'id'              => 'woocommerce_enable_coupons',
				'default'         => 'yes',
				'type'            => 'checkbox',
				'checkboxgroup'   => 'start',
				'show_if_checked' => 'option',
				'desc_tip'        => __( 'Coupons can be applied from the cart and checkout pages.', 'text_domain' ),
			),

			array(
				'desc'            => __( 'Calculate coupon discounts sequentially', 'text_domain' ),
				'id'              => 'woocommerce_calc_discounts_sequentially',
				'default'         => 'no',
				'type'            => 'checkbox',
				'desc_tip'        => __( 'When applying multiple coupons, apply the first coupon to the full price and the second coupon to the discounted price and so on.', 'text_domain' ),
				'show_if_checked' => 'yes',
				'checkboxgroup'   => 'end',
				'autoload'        => false,
			),

			array(
				'type' => 'sectionend',
				'id'   => 'general_options',
			),

			array(
				'title' => __( 'Currency options', 'text_domain' ),
				'type'  => 'title',
				'desc'  => __( 'The following options affect how prices are displayed on the frontend.', 'text_domain' ),
				'id'    => 'pricing_options',
			),

			array(
				'title'    => __( 'Currency position', 'text_domain' ),
				'desc'     => __( 'This controls the position of the currency symbol.', 'text_domain' ),
				'id'       => 'woocommerce_currency_pos',
				'class'    => 'wc-enhanced-select',
				'default'  => 'left',
				'type'     => 'select',
				'options'  => array(
					'left'        => __( 'Left', 'text_domain' ),
					'right'       => __( 'Right', 'text_domain' ),
					'left_space'  => __( 'Left with space', 'text_domain' ),
					'right_space' => __( 'Right with space', 'text_domain' ),
				),
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Thousand separator', 'text_domain' ),
				'desc'     => __( 'This sets the thousand separator of displayed prices.', 'text_domain' ),
				'id'       => 'woocommerce_price_thousand_sep',
				'css'      => 'width:50px;',
				'default'  => ',',
				'type'     => 'text',
				'desc_tip' => true,
			),

			array(
				'title'    => __( 'Decimal separator', 'text_domain' ),
				'desc'     => __( 'This sets the decimal separator of displayed prices.', 'text_domain' ),
				'id'       => 'woocommerce_price_decimal_sep',
				'css'      => 'width:50px;',
				'default'  => '.',
				'type'     => 'text',
				'desc_tip' => true,
			),

			array(
				'title'             => __( 'Number of decimals', 'text_domain' ),
				'desc'              => __( 'This sets the number of decimal points shown in displayed prices.', 'text_domain' ),
				'id'                => 'woocommerce_price_num_decimals',
				'css'               => 'width:50px;',
				'default'           => '2',
				'desc_tip'          => true,
				'type'              => 'number',
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1,
				),
			),

			array(
				'type' => 'section_end',
				'id'   => 'pricing_options',
			),
		);
	}
}

return new EA_General_Settings();
