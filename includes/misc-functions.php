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
function eaccounting_get_currency_config() {
	$config = array(
		'AED' => [
			'name'                => 'UAE Dirham',
			'code'                => 784,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'د.إ',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'AFN' => [
			'name'                => 'Afghani',
			'code'                => 971,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '؋',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'ALL' => [
			'name'                => 'Lek',
			'code'                => 8,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'L',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'AMD' => [
			'name'                => 'Armenian Dram',
			'code'                => 51,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'դր.',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'ANG' => [
			'name'                => 'Netherlands Antillean Guilder',
			'code'                => 532,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'ƒ',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'AOA' => [
			'name'                => 'Kwanza',
			'code'                => 973,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Kz',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'ARS' => [
			'name'                => 'Argentine Peso',
			'code'                => 32,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'AUD' => [
			'name'                => 'Australian Dollar',
			'code'                => 36,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ' ',
		],

		'AWG' => [
			'name'                => 'Aruban Florin',
			'code'                => 533,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'ƒ',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'AZN' => [
			'name'                => 'Azerbaijanian Manat',
			'code'                => 944,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₼',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BAM' => [
			'name'                => 'Convertible Mark',
			'code'                => 977,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'КМ',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BBD' => [
			'name'                => 'Barbados Dollar',
			'code'                => 52,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BDT' => [
			'name'                => 'Taka',
			'code'                => 50,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '৳',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BGN' => [
			'name'                => 'Bulgarian Lev',
			'code'                => 975,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'лв',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BHD' => [
			'name'                => 'Bahraini Dinar',
			'code'                => 48,
			'precision'           => 3,
			'subunit'             => 1000,
			'symbol'              => 'ب.د',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BIF' => [
			'name'                => 'Burundi Franc',
			'code'                => 108,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'Fr',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BMD' => [
			'name'                => 'Bermudian Dollar',
			'code'                => 60,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BND' => [
			'name'                => 'Brunei Dollar',
			'code'                => 96,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BOB' => [
			'name'                => 'Boliviano',
			'code'                => 68,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Bs.',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BOV' => [
			'name'                => 'Mvdol',
			'code'                => 984,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Bs.',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BRL' => [
			'name'                => 'Brazilian Real',
			'code'                => 986,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'R$',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'BSD' => [
			'name'                => 'Bahamian Dollar',
			'code'                => 44,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BTN' => [
			'name'                => 'Ngultrum',
			'code'                => 64,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Nu.',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BWP' => [
			'name'                => 'Pula',
			'code'                => 72,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'P',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'BYN' => [
			'name'                => 'Belarussian Ruble',
			'code'                => 974,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'Br',
			'symbol_position'     => 'after',
			'decimal_mark'        => ',',
			'thousands_separator' => ' ',
		],

		'BZD' => [
			'name'                => 'Belize Dollar',
			'code'                => 84,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'CAD' => [
			'name'                => 'Canadian Dollar',
			'code'                => 124,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'CDF' => [
			'name'                => 'Congolese Franc',
			'code'                => 976,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Fr',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'CHF' => [
			'name'                => 'Swiss Franc',
			'code'                => 756,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'CHF',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'CLF' => [
			'name'                => 'Unidades de fomento',
			'code'                => 990,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'UF',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'CLP' => [
			'name'                => 'Chilean Peso',
			'code'                => 152,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'CNY' => [
			'name'                => 'Yuan Renminbi',
			'code'                => 156,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '¥',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'COP' => [
			'name'                => 'Colombian Peso',
			'code'                => 170,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'CRC' => [
			'name'                => 'Costa Rican Colon',
			'code'                => 188,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₡',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'CUC' => [
			'name'                => 'Peso Convertible',
			'code'                => 931,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'CUP' => [
			'name'                => 'Cuban Peso',
			'code'                => 192,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'CVE' => [
			'name'                => 'Cape Verde Escudo',
			'code'                => 132,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'CZK' => [
			'name'                => 'Czech Koruna',
			'code'                => 203,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Kč',
			'symbol_position'     => 'after',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'DJF' => [
			'name'                => 'Djibouti Franc',
			'code'                => 262,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'Fdj',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'DKK' => [
			'name'                => 'Danish Krone',
			'code'                => 208,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'kr',
			'symbol_position'     => 'after',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'DOP' => [
			'name'                => 'Dominican Peso',
			'code'                => 214,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'DZD' => [
			'name'                => 'Algerian Dinar',
			'code'                => 12,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'د.ج',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'EGP' => [
			'name'                => 'Egyptian Pound',
			'code'                => 818,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'ج.م',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'ERN' => [
			'name'                => 'Nakfa',
			'code'                => 232,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Nfk',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'ETB' => [
			'name'                => 'Ethiopian Birr',
			'code'                => 230,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Br',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'EUR' => [
			'name'                => 'Euro',
			'code'                => 978,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '€',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'FJD' => [
			'name'                => 'Fiji Dollar',
			'code'                => 242,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'FKP' => [
			'name'                => 'Falkland Islands Pound',
			'code'                => 238,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '£',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'GBP' => [
			'name'                => 'Pound Sterling',
			'code'                => 826,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '£',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'GEL' => [
			'name'                => 'Lari',
			'code'                => 981,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'ლ',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'GHS' => [
			'name'                => 'Ghana Cedi',
			'code'                => 936,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₵',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'GIP' => [
			'name'                => 'Gibraltar Pound',
			'code'                => 292,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '£',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'GMD' => [
			'name'                => 'Dalasi',
			'code'                => 270,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'D',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'GNF' => [
			'name'                => 'Guinea Franc',
			'code'                => 324,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'Fr',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'GTQ' => [
			'name'                => 'Quetzal',
			'code'                => 320,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Q',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'GYD' => [
			'name'                => 'Guyana Dollar',
			'code'                => 328,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'HKD' => [
			'name'                => 'Hong Kong Dollar',
			'code'                => 344,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'HNL' => [
			'name'                => 'Lempira',
			'code'                => 340,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'L',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'HRK' => [
			'name'                => 'Croatian Kuna',
			'code'                => 191,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'kn',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'HTG' => [
			'name'                => 'Gourde',
			'code'                => 332,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'G',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'HUF' => [
			'name'                => 'Forint',
			'code'                => 348,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Ft',
			'symbol_position'     => 'after',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'IDR' => [
			'name'                => 'Rupiah',
			'code'                => 360,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Rp',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'ILS' => [
			'name'                => 'New Israeli Sheqel',
			'code'                => 376,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₪',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'INR' => [
			'name'                => 'Indian Rupee',
			'code'                => 356,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₹',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'IQD' => [
			'name'                => 'Iraqi Dinar',
			'code'                => 368,
			'precision'           => 3,
			'subunit'             => 1000,
			'symbol'              => 'ع.د',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'IRR' => [
			'name'                => 'Iranian Rial',
			'code'                => 364,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '﷼',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'ISK' => [
			'name'                => 'Iceland Krona',
			'code'                => 352,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'kr',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'JMD' => [
			'name'                => 'Jamaican Dollar',
			'code'                => 388,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'JOD' => [
			'name'                => 'Jordanian Dinar',
			'code'                => 400,
			'precision'           => 3,
			'subunit'             => 100,
			'symbol'              => 'د.ا',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'JPY' => [
			'name'                => 'Yen',
			'code'                => 392,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => '¥',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'KES' => [
			'name'                => 'Kenyan Shilling',
			'code'                => 404,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'KSh',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'KGS' => [
			'name'                => 'Som',
			'code'                => 417,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'som',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'KHR' => [
			'name'                => 'Riel',
			'code'                => 116,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '៛',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'KMF' => [
			'name'                => 'Comoro Franc',
			'code'                => 174,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'Fr',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'KPW' => [
			'name'                => 'North Korean Won',
			'code'                => 408,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₩',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'KRW' => [
			'name'                => 'Won',
			'code'                => 410,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => '₩',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'KWD' => [
			'name'                => 'Kuwaiti Dinar',
			'code'                => 414,
			'precision'           => 3,
			'subunit'             => 1000,
			'symbol'              => 'د.ك',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'KYD' => [
			'name'                => 'Cayman Islands Dollar',
			'code'                => 136,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'KZT' => [
			'name'                => 'Tenge',
			'code'                => 398,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '〒',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'LAK' => [
			'name'                => 'Kip',
			'code'                => 418,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₭',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'LBP' => [
			'name'                => 'Lebanese Pound',
			'code'                => 422,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'ل.ل',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'LKR' => [
			'name'                => 'Sri Lanka Rupee',
			'code'                => 144,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₨',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'LRD' => [
			'name'                => 'Liberian Dollar',
			'code'                => 430,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'LSL' => [
			'name'                => 'Loti',
			'code'                => 426,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'L',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'LTL' => [
			'name'                => 'Lithuanian Litas',
			'code'                => 440,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Lt',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'LVL' => [
			'name'                => 'Latvian Lats',
			'code'                => 428,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Ls',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'LYD' => [
			'name'                => 'Libyan Dinar',
			'code'                => 434,
			'precision'           => 3,
			'subunit'             => 1000,
			'symbol'              => 'ل.د',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MAD' => [
			'name'                => 'Moroccan Dirham',
			'code'                => 504,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'د.م.',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MDL' => [
			'name'                => 'Moldovan Leu',
			'code'                => 498,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'L',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MGA' => [
			'name'                => 'Malagasy Ariary',
			'code'                => 969,
			'precision'           => 2,
			'subunit'             => 5,
			'symbol'              => 'Ar',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MKD' => [
			'name'                => 'Denar',
			'code'                => 807,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'ден',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MMK' => [
			'name'                => 'Kyat',
			'code'                => 104,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'K',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MNT' => [
			'name'                => 'Tugrik',
			'code'                => 496,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₮',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MOP' => [
			'name'                => 'Pataca',
			'code'                => 446,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'P',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MRO' => [
			'name'                => 'Ouguiya',
			'code'                => 478,
			'precision'           => 2,
			'subunit'             => 5,
			'symbol'              => 'UM',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MUR' => [
			'name'                => 'Mauritius Rupee',
			'code'                => 480,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₨',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MVR' => [
			'name'                => 'Rufiyaa',
			'code'                => 462,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'MVR',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MWK' => [
			'name'                => 'Kwacha',
			'code'                => 454,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'MK',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MXN' => [
			'name'                => 'Mexican Peso',
			'code'                => 484,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MYR' => [
			'name'                => 'Malaysian Ringgit',
			'code'                => 458,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'RM',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'MZN' => [
			'name'                => 'Mozambique Metical',
			'code'                => 943,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'MTn',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'NAD' => [
			'name'                => 'Namibia Dollar',
			'code'                => 516,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'NGN' => [
			'name'                => 'Naira',
			'code'                => 566,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₦',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'NIO' => [
			'name'                => 'Cordoba Oro',
			'code'                => 558,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'C$',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'NOK' => [
			'name'                => 'Norwegian Krone',
			'code'                => 578,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'kr',
			'symbol_position'     => 'after',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'NPR' => [
			'name'                => 'Nepalese Rupee',
			'code'                => 524,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₨',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'NZD' => [
			'name'                => 'New Zealand Dollar',
			'code'                => 554,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'OMR' => [
			'name'                => 'Rial Omani',
			'code'                => 512,
			'precision'           => 3,
			'subunit'             => 1000,
			'symbol'              => 'ر.ع.',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'PAB' => [
			'name'                => 'Balboa',
			'code'                => 590,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'B/.',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'PEN' => [
			'name'                => 'Sol',
			'code'                => 604,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'S/',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'PGK' => [
			'name'                => 'Kina',
			'code'                => 598,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'K',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'PHP' => [
			'name'                => 'Philippine Peso',
			'code'                => 608,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₱',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'PKR' => [
			'name'                => 'Pakistan Rupee',
			'code'                => 586,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₨',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'PLN' => [
			'name'                => 'Zloty',
			'code'                => 985,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'zł',
			'symbol_position'     => 'after',
			'decimal_mark'        => ',',
			'thousands_separator' => ' ',
		],

		'PYG' => [
			'name'                => 'Guarani',
			'code'                => 600,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => '₲',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'QAR' => [
			'name'                => 'Qatari Rial',
			'code'                => 634,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'ر.ق',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'RON' => [
			'name'                => 'New Romanian Leu',
			'code'                => 946,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Lei',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'RSD' => [
			'name'                => 'Serbian Dinar',
			'code'                => 941,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'РСД',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'RUB' => [
			'name'                => 'Russian Ruble',
			'code'                => 643,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₽',
			'symbol_position'     => 'after',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'RWF' => [
			'name'                => 'Rwanda Franc',
			'code'                => 646,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'FRw',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SAR' => [
			'name'                => 'Saudi Riyal',
			'code'                => 682,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'ر.س',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SBD' => [
			'name'                => 'Solomon Islands Dollar',
			'code'                => 90,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SCR' => [
			'name'                => 'Seychelles Rupee',
			'code'                => 690,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₨',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SDG' => [
			'name'                => 'Sudanese Pound',
			'code'                => 938,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '£',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SEK' => [
			'name'                => 'Swedish Krona',
			'code'                => 752,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'kr',
			'symbol_position'     => 'after',
			'decimal_mark'        => ',',
			'thousands_separator' => ' ',
		],

		'SGD' => [
			'name'                => 'Singapore Dollar',
			'code'                => 702,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SHP' => [
			'name'                => 'Saint Helena Pound',
			'code'                => 654,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '£',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SLL' => [
			'name'                => 'Leone',
			'code'                => 694,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Le',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SOS' => [
			'name'                => 'Somali Shilling',
			'code'                => 706,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Sh',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SRD' => [
			'name'                => 'Surinam Dollar',
			'code'                => 968,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SSP' => [
			'name'                => 'South Sudanese Pound',
			'code'                => 728,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '£',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'STD' => [
			'name'                => 'Dobra',
			'code'                => 678,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Db',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SVC' => [
			'name'                => 'El Salvador Colon',
			'code'                => 222,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₡',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SYP' => [
			'name'                => 'Syrian Pound',
			'code'                => 760,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '£S',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'SZL' => [
			'name'                => 'Lilangeni',
			'code'                => 748,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'E',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'THB' => [
			'name'                => 'Baht',
			'code'                => 764,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '฿',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'TJS' => [
			'name'                => 'Somoni',
			'code'                => 972,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'ЅМ',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'TMT' => [
			'name'                => 'Turkmenistan New Manat',
			'code'                => 934,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'T',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'TND' => [
			'name'                => 'Tunisian Dinar',
			'code'                => 788,
			'precision'           => 3,
			'subunit'             => 1000,
			'symbol'              => 'د.ت',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'TOP' => [
			'name'                => 'Pa’anga',
			'code'                => 776,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'T$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'TRY' => [
			'name'                => 'Turkish Lira',
			'code'                => 949,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₺',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'TTD' => [
			'name'                => 'Trinidad and Tobago Dollar',
			'code'                => 780,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'TWD' => [
			'name'                => 'New Taiwan Dollar',
			'code'                => 901,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'TZS' => [
			'name'                => 'Tanzanian Shilling',
			'code'                => 834,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Sh',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'UAH' => [
			'name'                => 'Hryvnia',
			'code'                => 980,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '₴',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'UGX' => [
			'name'                => 'Uganda Shilling',
			'code'                => 800,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'USh',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'USD' => [
			'name'                => 'US Dollar',
			'code'                => 840,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'UYU' => [
			'name'                => 'Peso Uruguayo',
			'code'                => 858,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'UZS' => [
			'name'                => 'Uzbekistan Sum',
			'code'                => 860,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => null,
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'VEF' => [
			'name'                => 'Bolivar',
			'code'                => 937,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'Bs F',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'VND' => [
			'name'                => 'Dong',
			'code'                => 704,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => '₫',
			'symbol_position'     => 'before',
			'decimal_mark'        => ',',
			'thousands_separator' => '.',
		],

		'VUV' => [
			'name'                => 'Vatu',
			'code'                => 548,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'Vt',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'WST' => [
			'name'                => 'Tala',
			'code'                => 882,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'T',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'XAF' => [
			'name'                => 'CFA Franc BEAC',
			'code'                => 950,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'Fr',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'XAG' => [
			'name'                => 'Silver',
			'code'                => 961,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'oz t',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'XAU' => [
			'name'                => 'Gold',
			'code'                => 959,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'oz t',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'XCD' => [
			'name'                => 'East Caribbean Dollar',
			'code'                => 951,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'XDR' => [
			'name'                => 'SDR (Special Drawing Right)',
			'code'                => 960,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'SDR',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'XOF' => [
			'name'                => 'CFA Franc BCEAO',
			'code'                => 952,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'Fr',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'XPF' => [
			'name'                => 'CFP Franc',
			'code'                => 953,
			'precision'           => 0,
			'subunit'             => 1,
			'symbol'              => 'Fr',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'YER' => [
			'name'                => 'Yemeni Rial',
			'code'                => 886,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '﷼',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'ZAR' => [
			'name'                => 'Rand',
			'code'                => 710,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'R',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'ZMW' => [
			'name'                => 'Zambian Kwacha',
			'code'                => 967,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => 'ZK',
			'symbol_position'     => 'after',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
		],

		'ZWL' => [
			'name'                => 'Zimbabwe Dollar',
			'code'                => 932,
			'precision'           => 2,
			'subunit'             => 100,
			'symbol'              => '$',
			'symbol_position'     => 'before',
			'decimal_mark'        => '.',
			'thousands_separator' => ',',
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
