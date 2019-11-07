<?php
/**
 * Decimal price_currency
 * since 1.0.0
 * @return array|string
 */
function eaccounting_get_price_currency() {
	return eaccounting_get_option( 'currency', 'eaccounting_localization', 'USD' );
}

/**
 * Decimal price_currency_symbol
 * since 1.0.0
 * @return array|string
 */
function eaccounting_get_price_currency_symbol() {
	$symbol =  eaccounting_get_option( 'symbol', 'eaccounting_localization', 'USD' );
	$symbols =  eaccounting_get_currency_symbols();
	if(array_key_exists($symbol, $symbols)){
		return $symbols[$symbol];
	}
	return $symbols['USD'];
}

/**
 * Return the number of decimals after the decimal point.
 *
 * since 1.0.0
 * @return int
 */
function eaccounting_get_price_precision(){
	return eaccounting_get_option( 'precision', 'eaccounting_localization', '2' );
}
/**
 * Decimal sep
 * since 1.0.0
 * @return array|string
 */
function eaccounting_get_price_decimal_separator() {
	return eaccounting_get_option( 'decimal_separator', 'eaccounting_localization', '.' );
}

/**
 * Thousand sep
 * since 1.0.0
 * @return array|string
 */
function eaccounting_get_price_thousands_separator() {
	return eaccounting_get_option( 'thousands_separator', 'eaccounting_localization', ',' );
}

/**
 * Get the price format depending on the currency position.
 *
 * @return string
 */
function eaccounting_get_price_format() {
	$currency_pos = eaccounting_get_option( 'currency_pos', 'eaccounting_localization', 'right' );

	$format = '%1$s%2$s';

	switch ( $currency_pos ) {
		case 'left':
			$format = '%1$s%2$s';
			break;
		case 'right':
			$format = '%2$s%1$s';
			break;
		case 'left_space':
			$format = '%1$s&nbsp;%2$s';
			break;
		case 'right_space':
			$format = '%2$s&nbsp;%1$s';
			break;
	}

	return apply_filters( 'eaccounting_price_format', $format, $currency_pos );
}

/**
 * Returns a sanitized price by stripping out thousands separators.
 *
 * since 1.0.0
 *
 * @param $price
 *
 * @return string
 */
function eaccounting_sanitize_price( $price ) {
	$is_negative   = false;
	$thousands_sep = eaccounting_get_price_thousands_separator();
	$decimal_sep   = eaccounting_get_price_decimal_separator();

	// Sanitize the price
	if ( $decimal_sep == ',' && false !== ( $found = strpos( $price, $decimal_sep ) ) ) {
		if ( ( $thousands_sep == '.' || $thousands_sep == ' ' ) && false !== ( $found = strpos( $price, $thousands_sep ) ) ) {
			$price = str_replace( $thousands_sep, '', $price );
		} elseif ( empty( $thousands_sep ) && false !== ( $found = strpos( $price, '.' ) ) ) {
			$price = str_replace( '.', '', $price );
		}

		$price = str_replace( $decimal_sep, '.', $price );
	} elseif ( $thousands_sep == ',' && false !== ( $found = strpos( $price, $thousands_sep ) ) ) {
		$price = str_replace( $thousands_sep, '', $price );
	}

	if ( $price < 0 ) {
		$is_negative = true;
	}

	$price = preg_replace( '/[^0-9\.]/', '', $price );

	$precision = eaccounting_get_price_precision();
	$price   = number_format( (double) $price, $precision, '.', '' );

	if ( $is_negative ) {
		$price *= - 1;
	}

	/**
	 * Filter the sanitized price before returning
	 *
	 * @param string $price Price
	 *
	 * @since unknown
	 *
	 */
	return apply_filters( 'eaccounting_sanitize_price', $price );
}


/**
 * Returns a nicely formatted price.
 *
 * since 1.0.0
 *
 * @param      $price
 * @param bool $decimals
 *
 * @return string
 */
function eaccounting_format_price( $price, $decimals = true ) {
	$thousands_sep = eaccounting_get_price_thousands_separator();
	$decimal_sep   = eaccounting_get_price_decimal_separator();

	// Format the price
	if ( $decimal_sep == ',' && false !== ( $sep_found = strpos( $price, $decimal_sep ) ) ) {
		$whole  = substr( $price, 0, $sep_found );
		$part   = substr( $price, $sep_found + 1, ( strlen( $price ) - 1 ) );
		$price = $whole . '.' . $part;
	}

	// Strip , from the price (if set as the thousands separator)
	if ( $thousands_sep == ',' && false !== ( $found = strpos( $price, $thousands_sep ) ) ) {
		$price = str_replace( ',', '', $price );
	}

	// Strip ' ' from the price (if set as the thousands separator)
	if ( $thousands_sep == ' ' && false !== ( $found = strpos( $price, $thousands_sep ) ) ) {
		$price = str_replace( ' ', '', $price );
	}

	if ( empty( $price ) ) {
		$price = 0;
	}

	$precision = eaccounting_get_price_precision();
	$formatted = number_format( $price, $precision, $decimal_sep, $thousands_sep );

	return apply_filters( 'eaccounting_format_price', $formatted, $price, $decimals, $decimal_sep, $thousands_sep );
}


function eaccounting_price( $price, $currency = false ) {
	if(!$currency){
		$currency = eaccounting_get_price_currency();
	}
	$format = eaccounting_get_price_format();
	$currency_symbol = eaccounting_get_price_currency_symbol();

	return sprintf($format, eaccounting_format_price($price), $currency_symbol);
}

function eaccounting_get_currency_symbols() {
	$currency_symbols = array(
		'AED' => '&#1583;.&#1573;',
		'AFN' => '&#65;&#102;',
		'ALL' => '&#76;&#101;&#107;',
		'AMD' => '',
		'ANG' => '&#402;',
		'AOA' => '&#75;&#122;',
		'ARS' => '&#36;',
		'AUD' => '&#36;',
		'AWG' => '&#402;',
		'AZN' => '&#1084;&#1072;&#1085;',
		'BAM' => '&#75;&#77;',
		'BBD' => '&#36;',
		'BDT' => '&#2547;',
		'BGN' => '&#1083;&#1074;',
		'BHD' => '.&#1583;.&#1576;',
		'BIF' => '&#70;&#66;&#117;',
		'BMD' => '&#36;',
		'BND' => '&#36;',
		'BOB' => '&#36;&#98;',
		'BRL' => '&#82;&#36;',
		'BSD' => '&#36;',
		'BTN' => '&#78;&#117;&#46;',
		'BWP' => '&#80;',
		'BYR' => '&#112;&#46;',
		'BZD' => '&#66;&#90;&#36;',
		'CAD' => '&#36;',
		'CDF' => '&#70;&#67;',
		'CHF' => '&#67;&#72;&#70;',
		'CLF' => '',
		'CLP' => '&#36;',
		'CNY' => '&#165;',
		'COP' => '&#36;',
		'CRC' => '&#8353;',
		'CUP' => '&#8396;',
		'CVE' => '&#36;',
		'CZK' => '&#75;&#269;',
		'DJF' => '&#70;&#100;&#106;',
		'DKK' => '&#107;&#114;',
		'DOP' => '&#82;&#68;&#36;',
		'DZD' => '&#1583;&#1580;',
		'EGP' => '&#163;',
		'ETB' => '&#66;&#114;',
		'EUR' => '&#8364;',
		'FJD' => '&#36;',
		'FKP' => '&#163;',
		'GBP' => '&#163;',
		'GEL' => '&#4314;',
		'GHS' => '&#162;',
		'GIP' => '&#163;',
		'GMD' => '&#68;',
		'GNF' => '&#70;&#71;',
		'GTQ' => '&#81;',
		'GYD' => '&#36;',
		'HKD' => '&#36;',
		'HNL' => '&#76;',
		'HRK' => '&#107;&#110;',
		'HTG' => '&#71;',
		'HUF' => '&#70;&#116;',
		'IDR' => '&#82;&#112;',
		'ILS' => '&#8362;',
		'INR' => '&#8377;',
		'IQD' => '&#1593;.&#1583;',
		'IRR' => '&#65020;',
		'ISK' => '&#107;&#114;',
		'JEP' => '&#163;',
		'JMD' => '&#74;&#36;',
		'JOD' => '&#74;&#68;',
		'JPY' => '&#165;',
		'KES' => '&#75;&#83;&#104;',
		'KGS' => '&#1083;&#1074;',
		'KHR' => '&#6107;',
		'KMF' => '&#67;&#70;',
		'KPW' => '&#8361;',
		'KRW' => '&#8361;',
		'KWD' => '&#1583;.&#1603;',
		'KYD' => '&#36;',
		'KZT' => '&#1083;&#1074;',
		'LAK' => '&#8365;',
		'LBP' => '&#163;',
		'LKR' => '&#8360;',
		'LRD' => '&#36;',
		'LSL' => '&#76;',
		'LTL' => '&#76;&#116;',
		'LVL' => '&#76;&#115;',
		'LYD' => '&#1604;.&#1583;',
		'MAD' => '&#1583;.&#1605;.', //?
		'MDL' => '&#76;',
		'MGA' => '&#65;&#114;',
		'MKD' => '&#1076;&#1077;&#1085;',
		'MMK' => '&#75;',
		'MNT' => '&#8366;',
		'MOP' => '&#77;&#79;&#80;&#36;',
		'MRO' => '&#85;&#77;',
		'MUR' => '&#8360;',
		'MVR' => '.&#1923;',
		'MWK' => '&#77;&#75;',
		'MXN' => '&#36;',
		'MYR' => '&#82;&#77;',
		'MZN' => '&#77;&#84;',
		'NAD' => '&#36;',
		'NGN' => '&#8358;',
		'NIO' => '&#67;&#36;',
		'NOK' => '&#107;&#114;',
		'NPR' => '&#8360;',
		'NZD' => '&#36;',
		'OMR' => '&#65020;',
		'PAB' => '&#66;&#47;&#46;',
		'PEN' => '&#83;&#47;&#46;',
		'PGK' => '&#75;',
		'PHP' => '&#8369;',
		'PKR' => '&#8360;',
		'PLN' => '&#122;&#322;',
		'PYG' => '&#71;&#115;',
		'QAR' => '&#65020;',
		'RON' => '&#108;&#101;&#105;',
		'RSD' => '&#1044;&#1080;&#1085;&#46;',
		'RUB' => '&#1088;&#1091;&#1073;',
		'RWF' => '&#1585;.&#1587;',
		'SAR' => '&#65020;',
		'SBD' => '&#36;',
		'SCR' => '&#8360;',
		'SDG' => '&#163;',
		'SEK' => '&#107;&#114;',
		'SGD' => '&#36;',
		'SHP' => '&#163;',
		'SLL' => '&#76;&#101;',
		'SOS' => '&#83;',
		'SRD' => '&#36;',
		'STD' => '&#68;&#98;',
		'SVC' => '&#36;',
		'SYP' => '&#163;',
		'SZL' => '&#76;',
		'THB' => '&#3647;',
		'TJS' => '&#84;&#74;&#83;', //
		'TMT' => '&#109;',
		'TND' => '&#1583;.&#1578;',
		'TOP' => '&#84;&#36;',
		'TRY' => '&#8356;', //
		'TTD' => '&#36;',
		'TWD' => '&#78;&#84;&#36;',
		'TZS' => '',
		'UAH' => '&#8372;',
		'UGX' => '&#85;&#83;&#104;',
		'USD' => '&#36;',
		'UYU' => '&#36;&#85;',
		'UZS' => '&#1083;&#1074;',
		'VEF' => '&#66;&#115;',
		'VND' => '&#8363;',
		'VUV' => '&#86;&#84;',
		'WST' => '&#87;&#83;&#36;',
		'XAF' => '&#70;&#67;&#70;&#65;',
		'XCD' => '&#36;',
		'XDR' => '',
		'XOF' => '',
		'XPF' => '&#70;',
		'YER' => '&#65020;',
		'ZAR' => '&#82;',
		'ZMK' => '&#90;&#75;',
		'ZWL' => '&#90;&#36;',
	);

	return apply_filters( 'eaccounting_currency_symbols', $currency_symbols );
}
