<?php

namespace EverAccounting\Controllers;

defined( 'ABSPATH' ) || exit;

/**
 * Business controller.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Controllers
 */
class Business {

	/**
	 * Get the name of the company.
	 *
	 * @since 1.0.0
	 * @return string Company name.
	 */
	public function get_name() {
		return get_option( 'eac_company_name', '' );
	}

	/**
	 * Get the email of the company.
	 *
	 * @since 1.0.0
	 * @return string Company email.
	 */
	public function get_email() {
		return get_option( 'eac_company_email', '' );
	}

	/**
	 * Get the phone number of the company.
	 *
	 * @since 1.0.0
	 * @return string Company phone number.
	 */
	public function get_phone() {
		return get_option( 'eac_company_phone', '' );
	}

	/**
	 * Get the address of the company.
	 *
	 * @since 1.0.0
	 * @return string Company address.
	 */
	public function get_address() {
		return get_option( 'eac_company_address', '' );
	}

	/**
	 * Get the city of the company.
	 *
	 * @since 1.0.0
	 * @return string Company city.
	 */
	public function get_city() {
		return get_option( 'eac_company_city', '' );
	}

	/**
	 * Get the state of the company.
	 *
	 * @since 1.0.0
	 * @return string Company state.
	 */
	public function get_state() {
		return get_option( 'eac_company_state', '' );
	}

	/**
	 * Get the zip code of the company.
	 *
	 * @since 1.0.0
	 * @return string Company zip code.
	 */
	public function get_zip() {
		return get_option( 'eac_company_zip', '' );
	}

	/**
	 * Get the country of the company.
	 *
	 * @since 1.0.0
	 * @return string Company country.
	 */
	public function get_country() {
		return get_option( 'eac_company_country', '' );
	}

	/**
	 * Get the logo of the company.
	 *
	 * @since 1.0.0
	 * @return string Company logo.
	 */
	public function get_logo() {
		return get_option( 'eac_company_logo', '' );
	}

	/**
	 * Get currency.
	 *
	 * @since 1.0.0
	 * @return string Currency code.
	 */
	public function get_currency() {
		$currency = get_option( 'eac_base_currency', 'USD' );

		return empty( $currency ) ? 'USD' : $currency;
	}

	/**
	 * Get the company's tax number.
	 *
	 * @since 1.0.0
	 * @return string Company tax number.
	 */
	public function get_tax_number() {
		return get_option( 'eac_company_tax_number', '' );
	}

	/**
	 * Get the company's fiscal year start month.
	 *
	 * @since 1.0.0
	 * @return int Fiscal year start month.
	 */
	public function get_fiscal_start_month() {
		return get_option( 'eac_fiscal_year_start_month', 1 );
	}
}
