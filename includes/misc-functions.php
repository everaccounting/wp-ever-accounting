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
 * @return mixed|void
 * @since 1.0.2
 */
function eaccounting_get_currencies_data() {
	$config = array(
		'AED' => [
			'name'              => 'UAE Dirham',
			'currency'          => 'AED',
			'code'              => 784,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'د.إ',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'AFN' => [
			'name'              => 'Afghani',
			'currency'          => 'AFN',
			'code'              => 971,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '؋',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'ALL' => [
			'name'              => 'Lek',
			'currency'          => 'ALL',
			'code'              => 8,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'L',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'AMD' => [
			'name'              => 'Armenian Dram',
			'currency'          => 'AMD',
			'code'              => 51,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'դր.',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'ANG' => [
			'name'              => 'Netherlands Antillean Guilder',
			'currency'          => 'ANG',
			'code'              => 532,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'ƒ',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'AOA' => [
			'name'              => 'Kwanza',
			'currency'          => 'AOA',
			'code'              => 973,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Kz',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'ARS' => [
			'name'              => 'Argentine Peso',
			'currency'          => 'ARS',
			'code'              => 32,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'AUD' => [
			'name'              => 'Australian Dollar',
			'currency'          => 'AUD',
			'code'              => 36,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ' ',
		],

		'AWG' => [
			'name'              => 'Aruban Florin',
			'currency'          => 'AWG',
			'code'              => 533,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'ƒ',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'AZN' => [
			'name'              => 'Azerbaijanian Manat',
			'currency'          => 'AZN',
			'code'              => 944,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₼',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BAM' => [
			'name'              => 'Convertible Mark',
			'currency'          => 'BAM',
			'code'              => 977,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'КМ',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BBD' => [
			'name'              => 'Barbados Dollar',
			'currency'          => 'BBD',
			'code'              => 52,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BDT' => [
			'name'              => 'Taka',
			'currency'          => 'BDT',
			'code'              => 50,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '৳',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BGN' => [
			'name'              => 'Bulgarian Lev',
			'currency'          => 'BGN',
			'code'              => 975,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'лв',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BHD' => [
			'name'              => 'Bahraini Dinar',
			'currency'          => 'BHD',
			'code'              => 48,
			'precision'         => 3,
			'subunit'           => 1000,
			'symbol'            => 'ب.د',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BIF' => [
			'name'              => 'Burundi Franc',
			'currency'          => 'BIF',
			'code'              => 108,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'Fr',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BMD' => [
			'name'              => 'Bermudian Dollar',
			'currency'          => 'BMD',
			'code'              => 60,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BND' => [
			'name'              => 'Brunei Dollar',
			'currency'          => 'BND',
			'code'              => 96,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BOB' => [
			'name'              => 'Boliviano',
			'currency'          => 'BOB',
			'code'              => 68,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Bs.',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BOV' => [
			'name'              => 'Mvdol',
			'currency'          => 'BOV',
			'code'              => 984,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Bs.',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BRL' => [
			'name'              => 'Brazilian Real',
			'currency'          => 'BRL',
			'code'              => 986,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'R$',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'BSD' => [
			'name'              => 'Bahamian Dollar',
			'currency'          => 'BSD',
			'code'              => 44,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BTN' => [
			'name'              => 'Ngultrum',
			'currency'          => 'BTN',
			'code'              => 64,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Nu.',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BWP' => [
			'name'              => 'Pula',
			'currency'          => 'BWP',
			'code'              => 72,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'P',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'BYN' => [
			'name'              => 'Belarussian Ruble',
			'currency'          => 'BYN',
			'code'              => 974,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'Br',
			'position'          => 'after',
			'decimalSeparator'  => ',',
			'thousandSeparator' => ' ',
		],

		'BZD' => [
			'name'              => 'Belize Dollar',
			'currency'          => 'BZD',
			'code'              => 84,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'CAD' => [
			'name'              => 'Canadian Dollar',
			'currency'          => 'CAD',
			'code'              => 124,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'CDF' => [
			'name'              => 'Congolese Franc',
			'currency'          => 'CDF',
			'code'              => 976,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Fr',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'CHF' => [
			'name'              => 'Swiss Franc',
			'currency'          => 'CHF',
			'code'              => 756,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'CHF',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'CLF' => [
			'name'              => 'Unidades de fomento',
			'currency'          => 'CLF',
			'code'              => 990,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'UF',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'CLP' => [
			'name'              => 'Chilean Peso',
			'currency'          => 'CLP',
			'code'              => 152,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'CNY' => [
			'name'              => 'Yuan Renminbi',
			'currency'          => 'CNY',
			'code'              => 156,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '¥',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'COP' => [
			'name'              => 'Colombian Peso',
			'currency'          => 'COP',
			'code'              => 170,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'CRC' => [
			'name'              => 'Costa Rican Colon',
			'currency'          => 'CRC',
			'code'              => 188,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₡',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'CUC' => [
			'name'              => 'Peso Convertible',
			'currency'          => 'CUC',
			'code'              => 931,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'CUP' => [
			'name'              => 'Cuban Peso',
			'currency'          => 'CUP',
			'code'              => 192,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'CVE' => [
			'name'              => 'Cape Verde Escudo',
			'currency'          => 'CVE',
			'code'              => 132,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'CZK' => [
			'name'              => 'Czech Koruna',
			'currency'          => 'CZK',
			'code'              => 203,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Kč',
			'position'          => 'after',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'DJF' => [
			'name'              => 'Djibouti Franc',
			'currency'          => 'DJF',
			'code'              => 262,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'Fdj',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'DKK' => [
			'name'              => 'Danish Krone',
			'currency'          => 'DKK',
			'code'              => 208,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'kr',
			'position'          => 'after',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'DOP' => [
			'name'              => 'Dominican Peso',
			'currency'          => 'DOP',
			'code'              => 214,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'DZD' => [
			'name'              => 'Algerian Dinar',
			'currency'          => 'DZD',
			'code'              => 12,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'د.ج',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'EGP' => [
			'name'              => 'Egyptian Pound',
			'currency'          => 'EGP',
			'code'              => 818,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'ج.م',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'ERN' => [
			'name'              => 'Nakfa',
			'currency'          => 'ERN',
			'code'              => 232,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Nfk',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'ETB' => [
			'name'              => 'Ethiopian Birr',
			'currency'          => 'ETB',
			'code'              => 230,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Br',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'EUR' => [
			'name'              => 'Euro',
			'currency'          => 'EUR',
			'code'              => 978,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '€',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'FJD' => [
			'name'              => 'Fiji Dollar',
			'currency'          => 'FJD',
			'code'              => 242,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'FKP' => [
			'name'              => 'Falkland Islands Pound',
			'currency'          => 'FKP',
			'code'              => 238,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '£',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'GBP' => [
			'name'              => 'Pound Sterling',
			'currency'          => 'GBP',
			'code'              => 826,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '£',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'GEL' => [
			'name'              => 'Lari',
			'currency'          => 'GEL',
			'code'              => 981,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'ლ',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'GHS' => [
			'name'              => 'Ghana Cedi',
			'currency'          => 'GHS',
			'code'              => 936,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₵',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'GIP' => [
			'name'              => 'Gibraltar Pound',
			'currency'          => 'GIP',
			'code'              => 292,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '£',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'GMD' => [
			'name'              => 'Dalasi',
			'currency'          => 'GMD',
			'code'              => 270,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'D',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'GNF' => [
			'name'              => 'Guinea Franc',
			'currency'          => 'GNF',
			'code'              => 324,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'Fr',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'GTQ' => [
			'name'              => 'Quetzal',
			'currency'          => 'GTQ',
			'code'              => 320,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Q',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'GYD' => [
			'name'              => 'Guyana Dollar',
			'currency'          => 'GYD',
			'code'              => 328,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'HKD' => [
			'name'              => 'Hong Kong Dollar',
			'currency'          => 'HKD',
			'code'              => 344,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'HNL' => [
			'name'              => 'Lempira',
			'currency'          => 'HNL',
			'code'              => 340,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'L',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'HRK' => [
			'name'              => 'Croatian Kuna',
			'currency'          => 'HRK',
			'code'              => 191,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'kn',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'HTG' => [
			'name'              => 'Gourde',
			'currency'          => 'HTG',
			'code'              => 332,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'G',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'HUF' => [
			'name'              => 'Forint',
			'currency'          => 'HUF',
			'code'              => 348,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Ft',
			'position'          => 'after',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'IDR' => [
			'name'              => 'Rupiah',
			'currency'          => 'IDR',
			'code'              => 360,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Rp',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'ILS' => [
			'name'              => 'New Israeli Sheqel',
			'currency'          => 'ILS',
			'code'              => 376,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₪',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'INR' => [
			'name'              => 'Indian Rupee',
			'currency'          => 'INR',
			'code'              => 356,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₹',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'IQD' => [
			'name'              => 'Iraqi Dinar',
			'currency'          => 'IQD',
			'code'              => 368,
			'precision'         => 3,
			'subunit'           => 1000,
			'symbol'            => 'ع.د',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'IRR' => [
			'name'              => 'Iranian Rial',
			'currency'          => 'IRR',
			'code'              => 364,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '﷼',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'ISK' => [
			'name'              => 'Iceland Krona',
			'currency'          => 'ISK',
			'code'              => 352,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'kr',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'JMD' => [
			'name'              => 'Jamaican Dollar',
			'currency'          => 'JMD',
			'code'              => 388,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'JOD' => [
			'name'              => 'Jordanian Dinar',
			'currency'          => 'JOD',
			'code'              => 400,
			'precision'         => 3,
			'subunit'           => 100,
			'symbol'            => 'د.ا',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'JPY' => [
			'name'              => 'Yen',
			'currency'          => 'JPY',
			'code'              => 392,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => '¥',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'KES' => [
			'name'              => 'Kenyan Shilling',
			'currency'          => 'KES',
			'code'              => 404,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'KSh',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'KGS' => [
			'name'              => 'Som',
			'currency'          => 'KGS',
			'code'              => 417,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'som',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'KHR' => [
			'name'              => 'Riel',
			'currency'          => 'KHR',
			'code'              => 116,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '៛',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'KMF' => [
			'name'              => 'Comoro Franc',
			'currency'          => 'KMF',
			'code'              => 174,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'Fr',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'KPW' => [
			'name'              => 'North Korean Won',
			'currency'          => 'KPW',
			'code'              => 408,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₩',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'KRW' => [
			'name'              => 'Won',
			'currency'          => 'KRW',
			'code'              => 410,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => '₩',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'KWD' => [
			'name'              => 'Kuwaiti Dinar',
			'currency'          => 'KWD',
			'code'              => 414,
			'precision'         => 3,
			'subunit'           => 1000,
			'symbol'            => 'د.ك',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'KYD' => [
			'name'              => 'Cayman Islands Dollar',
			'currency'          => 'KYD',
			'code'              => 136,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'KZT' => [
			'name'              => 'Tenge',
			'currency'          => 'KZT',
			'code'              => 398,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '〒',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'LAK' => [
			'name'              => 'Kip',
			'currency'          => 'LAK',
			'code'              => 418,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₭',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'LBP' => [
			'name'              => 'Lebanese Pound',
			'currency'          => 'LBP',
			'code'              => 422,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'ل.ل',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'LKR' => [
			'name'              => 'Sri Lanka Rupee',
			'currency'          => 'LKR',
			'code'              => 144,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₨',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'LRD' => [
			'name'              => 'Liberian Dollar',
			'currency'          => 'LRD',
			'code'              => 430,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'LSL' => [
			'name'              => 'Loti',
			'currency'          => 'LSL',
			'code'              => 426,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'L',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'LTL' => [
			'name'              => 'Lithuanian Litas',
			'currency'          => 'LTL',
			'code'              => 440,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Lt',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'LVL' => [
			'name'              => 'Latvian Lats',
			'currency'          => 'LVL',
			'code'              => 428,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Ls',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'LYD' => [
			'name'              => 'Libyan Dinar',
			'currency'          => 'LYD',
			'code'              => 434,
			'precision'         => 3,
			'subunit'           => 1000,
			'symbol'            => 'ل.د',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MAD' => [
			'name'              => 'Moroccan Dirham',
			'currency'          => 'MAD',
			'code'              => 504,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'د.م.',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MDL' => [
			'name'              => 'Moldovan Leu',
			'currency'          => 'MDL',
			'code'              => 498,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'L',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MGA' => [
			'name'              => 'Malagasy Ariary',
			'currency'          => 'MGA',
			'code'              => 969,
			'precision'         => 2,
			'subunit'           => 5,
			'symbol'            => 'Ar',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MKD' => [
			'name'              => 'Denar',
			'currency'          => 'MKD',
			'code'              => 807,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'ден',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MMK' => [
			'name'              => 'Kyat',
			'currency'          => 'MMK',
			'code'              => 104,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'K',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MNT' => [
			'name'              => 'Tugrik',
			'currency'          => 'MNT',
			'code'              => 496,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₮',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MOP' => [
			'name'              => 'Pataca',
			'currency'          => 'MOP',
			'code'              => 446,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'P',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MRO' => [
			'name'              => 'Ouguiya',
			'currency'          => 'MRO',
			'code'              => 478,
			'precision'         => 2,
			'subunit'           => 5,
			'symbol'            => 'UM',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MUR' => [
			'name'              => 'Mauritius Rupee',
			'currency'          => 'MUR',
			'code'              => 480,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₨',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MVR' => [
			'name'              => 'Rufiyaa',
			'currency'          => 'MVR',
			'code'              => 462,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'MVR',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MWK' => [
			'name'              => 'Kwacha',
			'currency'          => 'MWK',
			'code'              => 454,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'MK',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MXN' => [
			'name'              => 'Mexican Peso',
			'currency'          => 'MXN',
			'code'              => 484,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MYR' => [
			'name'              => 'Malaysian Ringgit',
			'currency'          => 'MYR',
			'code'              => 458,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'RM',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'MZN' => [
			'name'              => 'Mozambique Metical',
			'currency'          => 'MZN',
			'code'              => 943,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'MTn',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'NAD' => [
			'name'              => 'Namibia Dollar',
			'currency'          => 'NAD',
			'code'              => 516,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'NGN' => [
			'name'              => 'Naira',
			'currency'          => 'NGN',
			'code'              => 566,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₦',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'NIO' => [
			'name'              => 'Cordoba Oro',
			'currency'          => 'NIO',
			'code'              => 558,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'C$',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'NOK' => [
			'name'              => 'Norwegian Krone',
			'currency'          => 'NOK',
			'code'              => 578,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'kr',
			'position'          => 'after',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'NPR' => [
			'name'              => 'Nepalese Rupee',
			'currency'          => 'NPR',
			'code'              => 524,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₨',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'NZD' => [
			'name'              => 'New Zealand Dollar',
			'currency'          => 'NZD',
			'code'              => 554,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'OMR' => [
			'name'              => 'Rial Omani',
			'currency'          => 'OMR',
			'code'              => 512,
			'precision'         => 3,
			'subunit'           => 1000,
			'symbol'            => 'ر.ع.',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'PAB' => [
			'name'              => 'Balboa',
			'currency'          => 'PAB',
			'code'              => 590,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'B/.',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'PEN' => [
			'name'              => 'Sol',
			'currency'          => 'PEN',
			'code'              => 604,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'S/',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'PGK' => [
			'name'              => 'Kina',
			'currency'          => 'PGK',
			'code'              => 598,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'K',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'PHP' => [
			'name'              => 'Philippine Peso',
			'currency'          => 'PHP',
			'code'              => 608,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₱',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'PKR' => [
			'name'              => 'Pakistan Rupee',
			'currency'          => 'PKR',
			'code'              => 586,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₨',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'PLN' => [
			'name'              => 'Zloty',
			'currency'          => 'PLN',
			'code'              => 985,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'zł',
			'position'          => 'after',
			'decimalSeparator'  => ',',
			'thousandSeparator' => ' ',
		],

		'PYG' => [
			'name'              => 'Guarani',
			'currency'          => 'PYG',
			'code'              => 600,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => '₲',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'QAR' => [
			'name'              => 'Qatari Rial',
			'currency'          => 'QAR',
			'code'              => 634,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'ر.ق',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'RON' => [
			'name'              => 'New Romanian Leu',
			'currency'          => 'RON',
			'code'              => 946,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Lei',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'RSD' => [
			'name'              => 'Serbian Dinar',
			'currency'          => 'RSD',
			'code'              => 941,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'РСД',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'RUB' => [
			'name'              => 'Russian Ruble',
			'currency'          => 'RUB',
			'code'              => 643,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₽',
			'position'          => 'after',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'RWF' => [
			'name'              => 'Rwanda Franc',
			'currency'          => 'RWF',
			'code'              => 646,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'FRw',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SAR' => [
			'name'              => 'Saudi Riyal',
			'currency'          => 'SAR',
			'code'              => 682,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'ر.س',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SBD' => [
			'name'              => 'Solomon Islands Dollar',
			'currency'          => 'SBD',
			'code'              => 90,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SCR' => [
			'name'              => 'Seychelles Rupee',
			'currency'          => 'SCR',
			'code'              => 690,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₨',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SDG' => [
			'name'              => 'Sudanese Pound',
			'currency'          => 'SDG',
			'code'              => 938,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '£',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SEK' => [
			'name'              => 'Swedish Krona',
			'currency'          => 'SEK',
			'code'              => 752,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'kr',
			'position'          => 'after',
			'decimalSeparator'  => ',',
			'thousandSeparator' => ' ',
		],

		'SGD' => [
			'name'              => 'Singapore Dollar',
			'currency'          => 'SGD',
			'code'              => 702,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SHP' => [
			'name'              => 'Saint Helena Pound',
			'currency'          => 'SHP',
			'code'              => 654,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '£',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SLL' => [
			'name'              => 'Leone',
			'currency'          => 'SLL',
			'code'              => 694,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Le',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SOS' => [
			'name'              => 'Somali Shilling',
			'currency'          => 'SOS',
			'code'              => 706,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Sh',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SRD' => [
			'name'              => 'Surinam Dollar',
			'currency'          => 'SRD',
			'code'              => 968,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SSP' => [
			'name'              => 'South Sudanese Pound',
			'currency'          => 'SSP',
			'code'              => 728,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '£',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'STD' => [
			'name'              => 'Dobra',
			'currency'          => 'STD',
			'code'              => 678,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Db',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SVC' => [
			'name'              => 'El Salvador Colon',
			'currency'          => 'SVC',
			'code'              => 222,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₡',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SYP' => [
			'name'              => 'Syrian Pound',
			'currency'          => 'SYP',
			'code'              => 760,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '£S',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'SZL' => [
			'name'              => 'Lilangeni',
			'currency'          => 'SZL',
			'code'              => 748,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'E',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'THB' => [
			'name'              => 'Baht',
			'currency'          => 'THB',
			'code'              => 764,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '฿',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'TJS' => [
			'name'              => 'Somoni',
			'currency'          => 'TJS',
			'code'              => 972,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'ЅМ',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'TMT' => [
			'name'              => 'Turkmenistan New Manat',
			'currency'          => 'TMT',
			'code'              => 934,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'T',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'TND' => [
			'name'              => 'Tunisian Dinar',
			'currency'          => 'TND',
			'code'              => 788,
			'precision'         => 3,
			'subunit'           => 1000,
			'symbol'            => 'د.ت',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'TOP' => [
			'name'              => 'Pa’anga',
			'currency'          => 'TOP',
			'code'              => 776,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'T$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'TRY' => [
			'name'              => 'Turkish Lira',
			'currency'          => 'TRY',
			'code'              => 949,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₺',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'TTD' => [
			'name'              => 'Trinidad and Tobago Dollar',
			'currency'          => 'TTD',
			'code'              => 780,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'TWD' => [
			'name'              => 'New Taiwan Dollar',
			'currency'          => 'TWD',
			'code'              => 901,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'TZS' => [
			'name'              => 'Tanzanian Shilling',
			'currency'          => 'TZS',
			'code'              => 834,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Sh',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'UAH' => [
			'name'              => 'Hryvnia',
			'currency'          => 'UAH',
			'code'              => 980,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '₴',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'UGX' => [
			'name'              => 'Uganda Shilling',
			'currency'          => 'UGX',
			'code'              => 800,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'USh',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'USD' => [
			'name'              => 'US Dollar',
			'currency'          => 'USD',
			'code'              => 840,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'UYU' => [
			'name'              => 'Peso Uruguayo',
			'currency'          => 'UYU',
			'code'              => 858,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'UZS' => [
			'name'              => 'Uzbekistan Sum',
			'currency'          => 'UZS',
			'code'              => 860,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => null,
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'VEF' => [
			'name'              => 'Bolivar',
			'currency'          => 'VEF',
			'code'              => 937,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'Bs F',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'VND' => [
			'name'              => 'Dong',
			'currency'          => 'VND',
			'code'              => 704,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => '₫',
			'position'          => 'before',
			'decimalSeparator'  => ',',
			'thousandSeparator' => '.',
		],

		'VUV' => [
			'name'              => 'Vatu',
			'currency'          => 'VUV',
			'code'              => 548,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'Vt',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'WST' => [
			'name'              => 'Tala',
			'currency'          => 'WST',
			'code'              => 882,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'T',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'XAF' => [
			'name'              => 'CFA Franc BEAC',
			'currency'          => 'XAF',
			'code'              => 950,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'Fr',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'XAG' => [
			'name'              => 'Silver',
			'currency'          => 'XAG',
			'code'              => 961,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'oz t',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'XAU' => [
			'name'              => 'Gold',
			'currency'          => 'XAU',
			'code'              => 959,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'oz t',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'XCD' => [
			'name'              => 'East Caribbean Dollar',
			'currency'          => 'XCD',
			'code'              => 951,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'XDR' => [
			'name'              => 'SDR (Special Drawing Right)',
			'currency'          => 'XDR',
			'code'              => 960,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'SDR',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'XOF' => [
			'name'              => 'CFA Franc BCEAO',
			'currency'          => 'XOF',
			'code'              => 952,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'Fr',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'XPF' => [
			'name'              => 'CFP Franc',
			'currency'          => 'XPF',
			'code'              => 953,
			'precision'         => 0,
			'subunit'           => 1,
			'symbol'            => 'Fr',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'YER' => [
			'name'              => 'Yemeni Rial',
			'currency'          => 'YER',
			'code'              => 886,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '﷼',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'ZAR' => [
			'name'              => 'Rand',
			'currency'          => 'ZAR',
			'code'              => 710,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'R',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'ZMW' => [
			'name'              => 'Zambian Kwacha',
			'currency'          => 'ZMW',
			'code'              => 967,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => 'ZK',
			'position'          => 'after',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],

		'ZWL' => [
			'name'              => 'Zimbabwe Dollar',
			'currency'          => 'ZWL',
			'code'              => 932,
			'precision'         => 2,
			'subunit'           => 100,
			'symbol'            => '$',
			'position'          => 'before',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ',',
		],
	);

	return apply_filters( 'wpcp_currency_config', $config );
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
		'check'         => __( 'Cheque', 'wp-ever-accounting' ),
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
 *
 * @param string $period
 * @param array $args
 *
 * @return array
 * @since 1.0.0
 */
function eaccounting_get_dates_from_period( $period = 'last_30_days', $args = array() ) {
	$dates        = array();
	$current_time = current_time( 'timestamp' );

	switch ( $period ) :
		case 'this_month' :
			$dates['m_start']  = date( 'n', $current_time );
			$dates['m_end']    = date( 'n', $current_time );
			$dates['day']      = 1;
			$dates['year']     = date( 'Y' );
			$dates['year_end'] = date( 'Y' );
			$dates['day_end']  = cal_days_in_month( CAL_GREGORIAN, $dates['m_end'], $dates['year'] );
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
			$base_time = $period === 'this_week' ? current_time( 'mysql' ) : date( 'Y-m-d h:i:s', current_time( 'timestamp' ) - WEEK_IN_SECONDS );
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

		case 'last_12_month' :
			$date_start = strtotime( '-12 month' );

			$dates['day']     = date( 'd', $date_start );
			$dates['m_start'] = date( 'n', $date_start );
			$dates['year']    = date( 'Y', $date_start );

			$dates['day_end']  = date( 'd', $current_time );
			$dates['m_end']    = date( 'n', $current_time );
			$dates['year_end'] = date( 'Y', $current_time );
			break;

		case 'this_year' :
			$dates['day']      = 1;
			$dates['day_end']  = 31;
			$dates['m_start']  = 1;
			$dates['m_end']    = 12;
			$dates['year']     = date( 'Y', $current_time );
			$dates['year_end'] = $dates['year'];
			break;

		case 'last_year' :
			$dates['day']      = 1;
			$dates['day_end']  = 31;
			$dates['m_start']  = 1;
			$dates['m_end']    = 12;
			$dates['year']     = date( 'Y', $current_time ) - 1;
			$dates['year_end'] = date( 'Y', $current_time ) - 1;
			break;
		case 'all_time' :
			$dates['day']      = 1;
			$dates['day_end']  = 31;
			$dates['m_start']  = 1;
			$dates['m_end']    = 12;
			$dates['year']     = 1970;
			$dates['year_end'] = date( 'Y', $current_time );
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
