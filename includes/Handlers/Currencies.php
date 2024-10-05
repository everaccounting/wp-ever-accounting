<?php

namespace EverAccounting\Handlers;

defined( 'ABSPATH' ) || exit;

/**
 * Class Currencies.
 *
 * @since 1.0.0
 * @package EverAccounting\Controllers
 */
class Currencies {

	/**
	 * Constructor.
	 */
	public function __construct() {
		//add_filter( 'eac_currencies', array( $this, 'customized_currencies' ) );
	}

	/**
	 * Customized currencies.
	 *
	 * @param array $currencies Currencies.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function customized_currencies( $currencies ) {
		$customized = get_option('eac_currencies', array());
		// we will loop through the currencies and add the customized currencies properties to the currencies array.
		foreach ( $customized as $code => $currency ) {
			$currencies[ $code ] = wp_parse_args( $currency, $currencies[ $code ] );
		}

		return $currencies;
	}
}
