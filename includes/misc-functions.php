<?php
defined( 'ABSPATH' ) || exit();

/**
 * Generate a random color
 * since 1.0.0
 * @return string
 */
function eaccounting_get_random_hex_color() {
	return '#' . str_pad( dechex( mt_rand( 0, 0xFFFFFF ) ), 6, '0', STR_PAD_LEFT );
}

/**
 * Get all countries
 * since 1.0.0
 *
 * @return array
 */
function eaccounting_get_countries() {
	$countries = array(
		"AF" => "Afghanistan",
		"AL" => "Albania",
		"DZ" => "Algeria",
		"AS" => "American Samoa",
		"AD" => "Andorra",
		"AO" => "Angola",
		"AI" => "Anguilla",
		"AQ" => "Antarctica",
		"AG" => "Antigua and Barbuda",
		"AR" => "Argentina",
		"AM" => "Armenia",
		"AW" => "Aruba",
		"AU" => "Australia",
		"AT" => "Austria",
		"AZ" => "Azerbaijan",
		"BS" => "Bahamas",
		"BH" => "Bahrain",
		"BD" => "Bangladesh",
		"BB" => "Barbados",
		"BY" => "Belarus",
		"BE" => "Belgium",
		"BZ" => "Belize",
		"BJ" => "Benin",
		"BM" => "Bermuda",
		"BT" => "Bhutan",
		"BO" => "Bolivia",
		"BA" => "Bosnia and Herzegovina",
		"BW" => "Botswana",
		"BV" => "Bouvet Island",
		"BR" => "Brazil",
		"IO" => "British Indian Ocean Territory",
		"BN" => "Brunei Darussalam",
		"BG" => "Bulgaria",
		"BF" => "Burkina Faso",
		"BI" => "Burundi",
		"KH" => "Cambodia",
		"CM" => "Cameroon",
		"CA" => "Canada",
		"CV" => "Cape Verde",
		"KY" => "Cayman Islands",
		"CF" => "Central African Republic",
		"TD" => "Chad",
		"CL" => "Chile",
		"CN" => "China",
		"CX" => "Christmas Island",
		"CC" => "Cocos (Keeling) Islands",
		"CO" => "Colombia",
		"KM" => "Comoros",
		"CG" => "Congo",
		"CD" => "Congo, the Democratic Republic of the",
		"CK" => "Cook Islands",
		"CR" => "Costa Rica",
		"CI" => "Cote D'Ivoire",
		"HR" => "Croatia",
		"CU" => "Cuba",
		"CY" => "Cyprus",
		"CZ" => "Czech Republic",
		"DK" => "Denmark",
		"DJ" => "Djibouti",
		"DM" => "Dominica",
		"DO" => "Dominican Republic",
		"EC" => "Ecuador",
		"EG" => "Egypt",
		"SV" => "El Salvador",
		"GQ" => "Equatorial Guinea",
		"ER" => "Eritrea",
		"EE" => "Estonia",
		"ET" => "Ethiopia",
		"FK" => "Falkland Islands (Malvinas)",
		"FO" => "Faroe Islands",
		"FJ" => "Fiji",
		"FI" => "Finland",
		"FR" => "France",
		"GF" => "French Guiana",
		"PF" => "French Polynesia",
		"TF" => "French Southern Territories",
		"GA" => "Gabon",
		"GM" => "Gambia",
		"GE" => "Georgia",
		"DE" => "Germany",
		"GH" => "Ghana",
		"GI" => "Gibraltar",
		"GR" => "Greece",
		"GL" => "Greenland",
		"GD" => "Grenada",
		"GP" => "Guadeloupe",
		"GU" => "Guam",
		"GT" => "Guatemala",
		"GN" => "Guinea",
		"GW" => "Guinea-Bissau",
		"GY" => "Guyana",
		"HT" => "Haiti",
		"HM" => "Heard Island and Mcdonald Islands",
		"VA" => "Holy See (Vatican City State)",
		"HN" => "Honduras",
		"HK" => "Hong Kong",
		"HU" => "Hungary",
		"IS" => "Iceland",
		"IN" => "India",
		"ID" => "Indonesia",
		"IR" => "Iran, Islamic Republic of",
		"IQ" => "Iraq",
		"IE" => "Ireland",
		"IL" => "Israel",
		"IT" => "Italy",
		"JM" => "Jamaica",
		"JP" => "Japan",
		"JO" => "Jordan",
		"KZ" => "Kazakhstan",
		"KE" => "Kenya",
		"KI" => "Kiribati",
		"KP" => "Korea, Democratic People's Republic of",
		"KR" => "Korea, Republic of",
		"KW" => "Kuwait",
		"KG" => "Kyrgyzstan",
		"LA" => "Lao People's Democratic Republic",
		"LV" => "Latvia",
		"LB" => "Lebanon",
		"LS" => "Lesotho",
		"LR" => "Liberia",
		"LY" => "Libyan Arab Jamahiriya",
		"LI" => "Liechtenstein",
		"LT" => "Lithuania",
		"LU" => "Luxembourg",
		"MO" => "Macao",
		"MK" => "Macedonia, the Former Yugoslav Republic of",
		"MG" => "Madagascar",
		"MW" => "Malawi",
		"MY" => "Malaysia",
		"MV" => "Maldives",
		"ML" => "Mali",
		"MT" => "Malta",
		"MH" => "Marshall Islands",
		"MQ" => "Martinique",
		"MR" => "Mauritania",
		"MU" => "Mauritius",
		"YT" => "Mayotte",
		"MX" => "Mexico",
		"FM" => "Micronesia, Federated States of",
		"MD" => "Moldova, Republic of",
		"MC" => "Monaco",
		"MN" => "Mongolia",
		"MS" => "Montserrat",
		"MA" => "Morocco",
		"MZ" => "Mozambique",
		"MM" => "Myanmar",
		"NA" => "Namibia",
		"NR" => "Nauru",
		"NP" => "Nepal",
		"NL" => "Netherlands",
		"AN" => "Netherlands Antilles",
		"NC" => "New Caledonia",
		"NZ" => "New Zealand",
		"NI" => "Nicaragua",
		"NE" => "Niger",
		"NG" => "Nigeria",
		"NU" => "Niue",
		"NF" => "Norfolk Island",
		"MP" => "Northern Mariana Islands",
		"NO" => "Norway",
		"OM" => "Oman",
		"PK" => "Pakistan",
		"PW" => "Palau",
		"PS" => "Palestinian Territory, Occupied",
		"PA" => "Panama",
		"PG" => "Papua New Guinea",
		"PY" => "Paraguay",
		"PE" => "Peru",
		"PH" => "Philippines",
		"PN" => "Pitcairn",
		"PL" => "Poland",
		"PT" => "Portugal",
		"PR" => "Puerto Rico",
		"QA" => "Qatar",
		"RE" => "Reunion",
		"RO" => "Romania",
		"RU" => "Russian Federation",
		"RW" => "Rwanda",
		"SH" => "Saint Helena",
		"KN" => "Saint Kitts and Nevis",
		"LC" => "Saint Lucia",
		"PM" => "Saint Pierre and Miquelon",
		"VC" => "Saint Vincent and the Grenadines",
		"WS" => "Samoa",
		"SM" => "San Marino",
		"ST" => "Sao Tome and Principe",
		"SA" => "Saudi Arabia",
		"SN" => "Senegal",
		"CS" => "Serbia and Montenegro",
		"SC" => "Seychelles",
		"SL" => "Sierra Leone",
		"SG" => "Singapore",
		"SK" => "Slovakia",
		"SI" => "Slovenia",
		"SB" => "Solomon Islands",
		"SO" => "Somalia",
		"ZA" => "South Africa",
		"GS" => "South Georgia and the South Sandwich Islands",
		"ES" => "Spain",
		"LK" => "Sri Lanka",
		"SD" => "Sudan",
		"SR" => "Suriname",
		"SJ" => "Svalbard and Jan Mayen",
		"SZ" => "Swaziland",
		"SE" => "Sweden",
		"CH" => "Switzerland",
		"SY" => "Syrian Arab Republic",
		"TW" => "Taiwan, Province of China",
		"TJ" => "Tajikistan",
		"TZ" => "Tanzania, United Republic of",
		"TH" => "Thailand",
		"TL" => "Timor-Leste",
		"TG" => "Togo",
		"TK" => "Tokelau",
		"TO" => "Tonga",
		"TT" => "Trinidad and Tobago",
		"TN" => "Tunisia",
		"TR" => "Turkey",
		"TM" => "Turkmenistan",
		"TC" => "Turks and Caicos Islands",
		"TV" => "Tuvalu",
		"UG" => "Uganda",
		"UA" => "Ukraine",
		"AE" => "United Arab Emirates",
		"GB" => "United Kingdom",
		"US" => "United States",
		"UM" => "United States Minor Outlying Islands",
		"UY" => "Uruguay",
		"UZ" => "Uzbekistan",
		"VU" => "Vanuatu",
		"VE" => "Venezuela",
		"VN" => "Viet Nam",
		"VG" => "Virgin Islands, British",
		"VI" => "Virgin Islands, U.s.",
		"WF" => "Wallis and Futuna",
		"EH" => "Western Sahara",
		"YE" => "Yemen",
		"ZM" => "Zambia",
		"ZW" => "Zimbabwe"
	);

	return apply_filters( 'eaccounting_countries', $countries );
}

/**
 * Currencies list
 * since 1.0.0
 * @return array
 */
function eaccounting_get_currencies() {
	return apply_filters( 'eaccounting_currencies', array(
		'ALL' => 'Albania Lek',
		'AFN' => 'Afghanistan Afghani',
		'ARS' => 'Argentina Peso',
		'AWG' => 'Aruba Guilder',
		'AUD' => 'Australia Dollar',
		'AZN' => 'Azerbaijan New Manat',
		'BSD' => 'Bahamas Dollar',
		'BBD' => 'Barbados Dollar',
		'BDT' => 'Bangladeshi taka',
		'BYR' => 'Belarus Ruble',
		'BZD' => 'Belize Dollar',
		'BMD' => 'Bermuda Dollar',
		'BOB' => 'Bolivia Boliviano',
		'BAM' => 'Bosnia and Herzegovina Convertible Marka',
		'BWP' => 'Botswana Pula',
		'BGN' => 'Bulgaria Lev',
		'BRL' => 'Brazil Real',
		'BND' => 'Brunei Darussalam Dollar',
		'KHR' => 'Cambodia Riel',
		'CAD' => 'Canada Dollar',
		'KYD' => 'Cayman Islands Dollar',
		'CLP' => 'Chile Peso',
		'CNY' => 'China Yuan Renminbi',
		'COP' => 'Colombia Peso',
		'CRC' => 'Costa Rica Colon',
		'HRK' => 'Croatia Kuna',
		'CUP' => 'Cuba Peso',
		'CZK' => 'Czech Republic Koruna',
		'DKK' => 'Denmark Krone',
		'DOP' => 'Dominican Republic Peso',
		'XCD' => 'East Caribbean Dollar',
		'EGP' => 'Egypt Pound',
		'SVC' => 'El Salvador Colon',
		'EEK' => 'Estonia Kroon',
		'EUR' => 'Euro Member Countries',
		'FKP' => 'Falkland Islands (Malvinas) Pound',
		'FJD' => 'Fiji Dollar',
		'GHC' => 'Ghana Cedis',
		'GIP' => 'Gibraltar Pound',
		'GTQ' => 'Guatemala Quetzal',
		'GGP' => 'Guernsey Pound',
		'GYD' => 'Guyana Dollar',
		'HNL' => 'Honduras Lempira',
		'HKD' => 'Hong Kong Dollar',
		'HUF' => 'Hungary Forint',
		'ISK' => 'Iceland Krona',
		'INR' => 'India Rupee',
		'IDR' => 'Indonesia Rupiah',
		'IRR' => 'Iran Rial',
		'IMP' => 'Isle of Man Pound',
		'ILS' => 'Israel Shekel',
		'JMD' => 'Jamaica Dollar',
		'JPY' => 'Japan Yen',
		'JEP' => 'Jersey Pound',
		'KZT' => 'Kazakhstan Tenge',
		'KPW' => 'Korea (North) Won',
		'KRW' => 'Korea (South) Won',
		'KGS' => 'Kyrgyzstan Som',
		'LAK' => 'Laos Kip',
		'LVL' => 'Latvia Lat',
		'LBP' => 'Lebanon Pound',
		'LRD' => 'Liberia Dollar',
		'LTL' => 'Lithuania Litas',
		'MKD' => 'Macedonia Denar',
		'MYR' => 'Malaysia Ringgit',
		'MUR' => 'Mauritius Rupee',
		'MXN' => 'Mexico Peso',
		'MNT' => 'Mongolia Tughrik',
		'MZN' => 'Mozambique Metical',
		'NAD' => 'Namibia Dollar',
		'NPR' => 'Nepal Rupee',
		'ANG' => 'Netherlands Antilles Guilder',
		'NZD' => 'New Zealand Dollar',
		'NIO' => 'Nicaragua Cordoba',
		'NGN' => 'Nigeria Naira',
		'NOK' => 'Norway Krone',
		'OMR' => 'Oman Rial',
		'PKR' => 'Pakistan Rupee',
		'PAB' => 'Panama Balboa',
		'PYG' => 'Paraguay Guarani',
		'PEN' => 'Peru Nuevo Sol',
		'PHP' => 'Philippines Peso',
		'PLN' => 'Poland Zloty',
		'QAR' => 'Qatar Riyal',
		'RON' => 'Romania New Leu',
		'RUB' => 'Russia Ruble',
		'SHP' => 'Saint Helena Pound',
		'SAR' => 'Saudi Arabia Riyal',
		'RSD' => 'Serbia Dinar',
		'SCR' => 'Seychelles Rupee',
		'SGD' => 'Singapore Dollar',
		'SBD' => 'Solomon Islands Dollar',
		'SOS' => 'Somalia Shilling',
		'ZAR' => 'South Africa Rand',
		'LKR' => 'Sri Lanka Rupee',
		'SEK' => 'Sweden Krona',
		'CHF' => 'Switzerland Franc',
		'SRD' => 'Suriname Dollar',
		'SYP' => 'Syria Pound',
		'TWD' => 'Taiwan New Dollar',
		'THB' => 'Thailand Baht',
		'TTD' => 'Trinidad and Tobago Dollar',
		'TRY' => 'Turkey Lira',
		'TRL' => 'Turkey Lira',
		'TVD' => 'Tuvalu Dollar',
		'UAH' => 'Ukraine Hryvna',
		'GBP' => 'United Kingdom Pound',
		'UGX' => 'Uganda Shilling',
		'USD' => 'United States Dollar',
		'UYU' => 'Uruguay Peso',
		'UZS' => 'Uzbekistan Som',
		'VEF' => 'Venezuela Bolivar',
		'VND' => 'Viet Nam Dong',
		'YER' => 'Yemen Rial',
		'ZWD' => 'Zimbabwe Dollar'
	) );
}

/**
 * Get all currencies
 * since 1.0.0
 * @return array
 */
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


/**
 * Get payment methods
 * since 1.0.0
 * @return array
 */
function eaccounting_get_payment_methods() {
	return apply_filters( 'eaccounting_payment_methods', [
		'cash'          => __( 'Cash', 'wp-ever-accounting' ),
		'bank_transfer' => __( 'Bank Transfer', 'wp-ever-accounting' ),
		'check'         => __( 'Check', 'wp-ever-accounting' ),
	] );
}


/**
 * Get total income
 * @return string|null
 * @since 1.0.0
 */
function eaccounting_get_total_profit() {
	return eaccounting_get_total_income() - eaccounting_get_total_expense();
}

/**
 * Get dates in range
 * @since 1.0.0
 * @param string $period
 * @param array $args
 *
 * @return array
 */
function eaccounting_get_dates_from_period( $period = 'last_30_days', $args = array() ) {
	$dates        = array();
	$current_time = current_time( 'timestamp' );

	switch ( $period ) :
		case 'this_month' :
			$dates['m_start']  = date( 'n', $current_time );
			$dates['m_end']    = date( 'n', $current_time );
			$dates['day']      = 1;
			$dates['day_end']  = cal_days_in_month( CAL_GREGORIAN, $dates['m_end'], $dates['year'] );
			$dates['year']     = date( 'Y' );
			$dates['year_end'] = date( 'Y' );
			break;

		case 'last_month' :
			if ( date( 'n' ) == 1 ) {
				$dates['m_start']  = 12;
				$dates['m_end']    = 12;
				$dates['year']     = date( 'Y', $current_time ) - 1;
				$dates['year_end'] = date( 'Y', $current_time ) - 1;
			} else {
				$dates['m_start']  = date( 'n' ) - 1;
				$dates['m_end']    = date( 'n' ) - 1;
				$dates['year_end'] = $dates['year'];
			}
			$dates['day']     = 1;
			$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, $dates['m_end'], $dates['year'] );
			break;

		case 'today' :
			$dates['day']     = date( 'd', $current_time );
			$dates['m_start'] = date( 'n', $current_time );
			$dates['m_end']   = date( 'n', $current_time );
			$dates['year']    = date( 'Y', $current_time );
			break;

		case 'yesterday' :

			$year  = date( 'Y', $current_time );
			$month = date( 'n', $current_time );
			$day   = date( 'd', $current_time );

			if ( $month == 1 && $day == 1 ) {
				$year  -= 1;
				$month = 12;
				$day   = cal_days_in_month( CAL_GREGORIAN, $month, $year );
			} elseif ( $month > 1 && $day == 1 ) {
				$month -= 1;
				$day   = cal_days_in_month( CAL_GREGORIAN, $month, $year );
			} else {
				$day -= 1;
			}

			$dates['day']      = $day;
			$dates['m_start']  = $month;
			$dates['m_end']    = $month;
			$dates['year']     = $year;
			$dates['year_end'] = $year;
			$dates['day_end']  = $day;
			break;

		case 'this_week' :
		case 'last_week' :
			$base_time = $dates['range'] === 'this_week' ? current_time( 'mysql' ) : date( 'Y-m-d h:i:s', current_time( 'timestamp' ) - WEEK_IN_SECONDS );
			$start_end = get_weekstartend( $base_time, get_option( 'start_of_week' ) );

			$dates['day']     = date( 'd', $start_end['start'] );
			$dates['m_start'] = date( 'n', $start_end['start'] );
			$dates['year']    = date( 'Y', $start_end['start'] );

			$dates['day_end']  = date( 'd', $start_end['end'] );
			$dates['m_end']    = date( 'n', $start_end['end'] );
			$dates['year_end'] = date( 'Y', $start_end['end'] );
			break;

		case 'last_30_days' :

			$date_start = strtotime( '-30 days' );

			$dates['day']     = date( 'd', $date_start );
			$dates['m_start'] = date( 'n', $date_start );
			$dates['year']    = date( 'Y', $date_start );

			$dates['day_end']  = date( 'd', $current_time );
			$dates['m_end']    = date( 'n', $current_time );
			$dates['year_end'] = date( 'Y', $current_time );

			break;

		case 'this_quarter' :
			$month_now         = date( 'n', $current_time );
			$dates['year']     = date( 'Y', $current_time );
			$dates['year_end'] = $dates['year'];

			if ( $month_now <= 3 ) {
				$dates['m_start'] = 1;
				$dates['m_end']   = 3;
			} else if ( $month_now <= 6 ) {
				$dates['m_start'] = 4;
				$dates['m_end']   = 6;
			} else if ( $month_now <= 9 ) {
				$dates['m_start'] = 7;
				$dates['m_end']   = 9;
			} else {
				$dates['m_start'] = 10;
				$dates['m_end']   = 12;
			}

			$dates['day']     = 1;
			$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, $dates['m_end'], $dates['year'] );
			break;

		case 'last_quarter' :
			$month_now = date( 'n' );

			if ( $month_now <= 3 ) {
				$dates['m_start'] = 10;
				$dates['m_end']   = 12;
				$dates['year']    = date( 'Y', $current_time ) - 1; // Previous year
			} else if ( $month_now <= 6 ) {
				$dates['m_start'] = 1;
				$dates['m_end']   = 3;
				$dates['year']    = date( 'Y', $current_time );
			} else if ( $month_now <= 9 ) {
				$dates['m_start'] = 4;
				$dates['m_end']   = 6;
				$dates['year']    = date( 'Y', $current_time );
			} else {
				$dates['m_start'] = 7;
				$dates['m_end']   = 9;
				$dates['year']    = date( 'Y', $current_time );
			}

			$dates['day']      = 1;
			$dates['day_end']  = cal_days_in_month( CAL_GREGORIAN, $dates['m_end'], $dates['year'] );
			$dates['year_end'] = $dates['year'];
			break;

		case 'this_year' :
			$dates['day']      = 1;
			$dates['m_start']  = 1;
			$dates['m_end']    = 12;
			$dates['year']     = date( 'Y', $current_time );
			$dates['year_end'] = $dates['year'];
			break;

		case 'last_year' :
			$dates['day']      = 1;
			$dates['m_start']  = 1;
			$dates['m_end']    = 12;
			$dates['year']     = date( 'Y', $current_time ) - 1;
			$dates['year_end'] = date( 'Y', $current_time ) - 1;
			break;

		case 'custom' :
			$dates['year']     = isset( $args['year'] ) ? $args['year'] : date( 'Y' );
			$dates['year_end'] = isset( $args['year_end'] ) ? $args['year_end'] : date( 'Y' );
			$dates['m_start']  = isset( $args['m_start'] ) ? $args['m_start'] : 1;
			$dates['m_end']    = isset( $args['m_end'] ) ? $args['m_end'] : 12;
			$dates['day']      = isset( $args['day'] ) ? $args['day'] : 1;
			$dates['day_end']  = isset( $args['day_end'] ) ? $args['day_end'] : cal_days_in_month( CAL_GREGORIAN, $dates['m_end'], $dates['year'] );
			break;
	endswitch;

	return $dates;
}
